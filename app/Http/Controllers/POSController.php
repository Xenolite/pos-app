<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Exports\TransactionsExport;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
class POSController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->category;
        $search = $request->search;
        $products = Product::where('is_active', true)
        ->when($category, function ($query) use ($category) {
            $query->where('category', $category);
        })
        ->when($search, function ($query) use ($search) {
            $query->where('name', 'ilike', '%'.$search.'%');
        })
        ->get();

 $lowStockProducts = Product::whereColumn('stock', '<=', 'low_stock_limit')
    ->where('is_active', true)
    ->get();

    $cart = session()->get('cart', []);
    $category = $request->category;
    $todaySales = Transaction::whereDate('created_at', Carbon::today())
                    ->sum('total');

    $todayTransactions = Transaction::whereDate('created_at', Carbon::today())
                            ->count();
    $categories = Product::select('category')->distinct()->pluck('category');
    return view('dashboard', compact(
        'products',
        'cart',
        'todaySales',
        'todayTransactions',
        'categories',
        'lowStockProducts'
    ));
    }

    public function clearCart()
{
    session()->forget('cart');

    return redirect()->back();
}

    

    public function addToCart(Request $request)
{
    $product = Product::findOrFail($request->product_id);

    if ($product->stock <= 0) {
        return back()->with('error', 'Out of stock!');
    }

    $quantity = $request->quantity;
    $discountType = $request->discount_type;
    $discountValue = $request->discount_value ?? 0;

    $price = $product->price;

 
    if ($discountType == 'percent') {
        $price -= ($price * $discountValue / 100);
    } elseif ($discountType == 'fixed') {
        $price -= $discountValue;
    }

    if ($price < 0) $price = 0;

    $cart = session()->get('cart', []);

    if (isset($cart[$product->id])) {
        $cart[$product->id]['quantity'] += $quantity;
    } else {
        $cart[$product->id] = [
            "name" => $product->name,
            "price" => $price,
            "original_price" => $product->price,
            "quantity" => $quantity,
            "discount_type" => $discountType,
            "discount_value" => $discountValue
        ];
    }

    session()->put('cart', $cart);

    return back()->with('success', 'Added to cart!');
}

    public function removeFromCart($id)
    {
        $cart = session()->get('cart');
        unset($cart[$id]);
        session()->put('cart', $cart);
        return redirect()->back();
    }

    public function updateCartQuantity(Request $request, $id)
    {
        $cart = session()->get('cart', []);

        if (! isset($cart[$id])) {
            return redirect()->back();
        }

        $quantity = (int) $request->quantity;

        // Quantity 0 (atau kurang) = sama aja kayak hapus item itu dari cart.
        if ($quantity <= 0) {
            unset($cart[$id]);
            session()->put('cart', $cart);
            return redirect()->back();
        }

        $product = Product::find($id);

        // Jangan izinkan quantity di cart lebih besar dari stock yang
        // sebenarnya ada — sama seperti batasan waktu pertama kali
        // ditambahkan lewat modal Add to Cart.
        if ($product && $quantity > $product->stock) {
            $quantity = $product->stock;
        }

        $cart[$id]['quantity'] = $quantity;
        session()->put('cart', $cart);

        return redirect()->back();
    }

   public function checkout(\App\Services\MidtransService $midtrans)
{
   $paymentMethod = request()->payment_method;
    $serviceCharge = request()->service_charge ?? 0;
    $cart = session()->get('cart');

    if (!$cart) {
        return redirect()->back();
    }

    $total = 0;
    $totalProfit = 0;

    foreach ($cart as $id => $item) {

        $product = Product::find($id);
        if (!$product) continue;

        $subtotal = $item['price'] * $item['quantity'];
        $profit = ($item['price'] - $product->buy_price) * $item['quantity'];

        $total += $subtotal;


        $totalProfit += $profit;
    }

    
    $total += $serviceCharge;

    // CASH = langsung lunas di kasir, stok langsung dipotong seperti alur lama.
    if ($paymentMethod === 'Cash') {

        $transaction = Transaction::create([
            'user_id' => auth()->id(),
            'total' => $total,
            'profit' => $totalProfit,
            'service_charge' => $serviceCharge,
            'payment_method' => $paymentMethod,
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);

        $this->deductStockAndSaveItems($transaction, $cart);

        session()->forget('cart');

        return redirect()->back()->with('success', 'Transaction completed!');
    }

    // NON-CASH (QRIS, Transfer, e-wallet, dst) = dibayar via Midtrans Snap.
    // Transaksi dibuat dengan status "pending" dulu, stok BELUM dipotong
    // sampai pembayaran benar-benar sukses (dikonfirmasi lewat webhook).
    $transaction = Transaction::create([
        'user_id' => auth()->id(),
        'total' => $total,
        'profit' => $totalProfit,
        'service_charge' => $serviceCharge,
        'payment_method' => $paymentMethod,
        'payment_status' => 'pending',
    ]);

    // Simpan isi cart di transaksi supaya bisa diproses ulang saat webhook masuk
    // (session cart akan kosong begitu customer pindah/refresh halaman).
    session()->put('pending_cart_'.$transaction->id, $cart);

    $orderId = 'POS-'.$transaction->id.'-'.time();

    $itemDetails = [];
    foreach ($cart as $id => $item) {
        $itemDetails[] = [
            'id' => (string) $id,
            'price' => (int) $item['price'],
            'quantity' => (int) $item['quantity'],
            'name' => substr($item['name'], 0, 50),
        ];
    }

    if ($serviceCharge > 0) {
        $itemDetails[] = [
            'id' => 'SERVICE_CHARGE',
            'price' => (int) $serviceCharge,
            'quantity' => 1,
            'name' => 'Service Charge',
        ];
    }

    $snapToken = $midtrans->createSnapToken(
        $orderId,
        (int) $total,
        $itemDetails,
        [
            'first_name' => auth()->user()->name,
            'email' => auth()->user()->email,
        ]
    );

    $transaction->update([
        'midtrans_order_id' => $orderId,
        'snap_token' => $snapToken,
    ]);

    return response()->json([
        'snap_token' => $snapToken,
        'transaction_id' => $transaction->id,
    ]);
}

/**
 * Webhook Midtrans (server-to-server notification). Route ini harus
 * dikecualikan dari CSRF middleware karena dipanggil oleh server Midtrans,
 * bukan dari browser.
 */
public function midtransNotification(\App\Services\MidtransService $midtrans)
{
    $notification = $midtrans->handleNotification();

    $orderId = $notification->order_id;
    $status = $notification->transaction_status;
    $fraud = $notification->fraud_status;

    $transaction = Transaction::where('midtrans_order_id', $orderId)->first();

    if (! $transaction) {
        return response()->json(['message' => 'Transaction not found'], 404);
    }

    // Hindari memproses dua kali kalau notifikasi sama dikirim ulang oleh Midtrans.
    if ($transaction->payment_status === 'paid') {
        return response()->json(['message' => 'Already processed']);
    }

    if ($status === 'capture' && $fraud === 'accept') {
        $this->markTransactionPaid($transaction, $notification);
    } elseif ($status === 'settlement') {
        $this->markTransactionPaid($transaction, $notification);
    } elseif (in_array($status, ['cancel', 'deny'])) {
        $transaction->update(['payment_status' => 'failed']);
    } elseif ($status === 'expire') {
        $transaction->update(['payment_status' => 'expired']);
    } elseif ($status === 'pending') {
        $transaction->update(['payment_status' => 'pending']);
    }

    return response()->json(['message' => 'OK']);
}

private function markTransactionPaid(Transaction $transaction, $notification)
{
    $transaction->update([
        'payment_status' => 'paid',
        'payment_type' => $notification->payment_type ?? null,
        'paid_at' => now(),
    ]);

    $cart = session()->pull('pending_cart_'.$transaction->id, []);

    $this->deductStockAndSaveItems($transaction, $cart);
}

private function deductStockAndSaveItems(Transaction $transaction, array $cart)
{
    foreach ($cart as $id => $item) {

        $product = Product::find($id);


        if ($product && $product->stock >= $item['quantity']) {

            $product->stock -= $item['quantity'];
            $product->save();

            TransactionItem::create([
                'transaction_id' => $transaction->id,
                'product_id' => $id,
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'buy_price' => $product->buy_price
            ]);
        }
    }
}

public function products(Request $request)
{
    $category = $request->category;

    $categories = Product::select('category')
        ->distinct()
        ->pluck('category');

    $products = Product::query()
    ->when($category, function($query) use ($category){
        $query->where('category', $category);
    })
    ->when(auth()->user()->role !== 'admin', function($query){
    $query->where('is_active', true);
    })
    ->get();

    return view('product', compact(
        'products',
        'categories',
        'category'
    ));
}

public function transactions(Request $request)
{
    $query = Transaction::query();
    $search = $request->search;

    // FILTER BY TRANSACTION ID
    if($search){
        $query->where('id', $search);
    }

    // FILTER PAYMENT METHOD
    if($request->payment_method){
        $query->where('payment_method', $request->payment_method);
    }

    // FILTER DATE RANGE
    if($request->start_date && $request->end_date){

        $query->whereBetween('created_at', [
            $request->start_date . ' 00:00:00',
            $request->end_date . ' 23:59:59'
        ]);
    }

    $transactions = $query->latest()->paginate(10);

    return view('transaction', compact('transactions','search'));
}

public function exportTransactions(Request $request)
{
    $fileName = 'laporan-penjualan-'.now()->format('Y-m-d_His').'.xlsx';

    return Excel::download(
        new TransactionsExport(
            $request->payment_method,
            $request->start_date,
            $request->end_date
        ),
        $fileName
    );
}
}
