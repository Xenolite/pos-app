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
        $favoriteOnly = $request->boolean('favorite');
        $products = Product::where('is_active', true)
        ->when($category, function ($query) use ($category) {
            $query->where('category', $category);
        })
        ->when($search, function ($query) use ($search) {
            $query->where('name', 'ilike', '%'.$search.'%');
        })
        ->when($favoriteOnly, function ($query) {
            $query->where('is_favorite', 1);
        })
        ->orderByDesc('is_favorite')
        ->orderBy('name')
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
        'lowStockProducts',
        'favoriteOnly'
    ));
    }

   
    public function toggleFavorite($id)
    {
        $product = Product::findOrFail($id);
        $product->is_favorite = ! $product->is_favorite;
        $product->save();

        return redirect()->back();
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

       
        if ($quantity <= 0) {
            unset($cart[$id]);
            session()->put('cart', $cart);
            return redirect()->back();
        }

        $product = Product::find($id);

   
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

   
    $orderId = 'POS-'.now()->timestamp.'-'.\Illuminate\Support\Str::random(8);

    \Illuminate\Support\Facades\Cache::put("midtrans_order:{$orderId}", [
        'user_id' => auth()->id(),
        'cart' => $cart,
        'total' => $total,
        'profit' => $totalProfit,
        'service_charge' => $serviceCharge,
        'payment_method' => $paymentMethod,
    ], now()->addHours(24));


    session()->forget('cart');

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

    return response()->json([
        'snap_token' => $snapToken,
        'order_id' => $orderId,
    ]);
}


public function midtransNotification(\App\Services\MidtransService $midtrans)
{
    $notification = $midtrans->handleNotification();

    $this->finalizeMidtransOrder(
        $notification->order_id,
        $notification->transaction_status,
        $notification->fraud_status,
        $notification->payment_type ?? null
    );

    return response()->json(['message' => 'OK']);
}


public function checkOrderStatus($orderId, \App\Services\MidtransService $midtrans)
{
    // Sudah final (baris transaksi sudah dibuat) -- tidak perlu tanya ulang.
    if (Transaction::where('midtrans_order_id', $orderId)->exists()) {
        return response()->json(['message' => 'Already finalized']);
    }

    try {
        $result = $midtrans->getStatus($orderId);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Could not reach Midtrans'], 502);
    }

    $this->finalizeMidtransOrder(
        $orderId,
        $result['transaction_status'] ?? null,
        $result['fraud_status'] ?? null,
        $result['payment_type'] ?? null
    );

    return response()->json(['message' => 'Checked']);
}


private function finalizeMidtransOrder(?string $orderId, ?string $status, ?string $fraud, ?string $paymentType = null): void
{
    if (! $orderId) {
        return;
    }


    if (Transaction::where('midtrans_order_id', $orderId)->exists()) {
        return;
    }

    $cacheKey = "midtrans_order:{$orderId}";
    $order = \Illuminate\Support\Facades\Cache::get($cacheKey);

    if (! $order) {

        return;
    }

    $isSuccess = ($status === 'settlement') || ($status === 'capture' && $fraud === 'accept');
    $isFinalFailure = in_array($status, ['cancel', 'deny', 'expire']);

    if (! $isSuccess && ! $isFinalFailure) {

        return;
    }

    $transaction = Transaction::create([
        'user_id' => $order['user_id'],
        'total' => $order['total'],
        'profit' => $order['profit'],
        'service_charge' => $order['service_charge'],
        'payment_method' => $order['payment_method'],
        'payment_status' => $isSuccess ? 'paid' : 'failed',
        'payment_type' => $paymentType,
        'midtrans_order_id' => $orderId,
        'paid_at' => $isSuccess ? now() : null,
    ]);

    if ($isSuccess) {
        
        $this->deductStockAndSaveItems($transaction, $order['cart']);
    } else {
        
        $this->saveTransactionItems($transaction, $order['cart']);
    }

    \Illuminate\Support\Facades\Cache::forget($cacheKey);
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


private function saveTransactionItems(Transaction $transaction, array $cart)
{
    foreach ($cart as $id => $item) {

        $product = Product::find($id);

        if (! $product) continue;

        TransactionItem::create([
            'transaction_id' => $transaction->id,
            'product_id' => $id,
            'quantity' => $item['quantity'],
            'price' => $item['price'],
            'buy_price' => $product->buy_price
        ]);
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

public function transactionSummary(Request $request)
{
   
    $period = $request->period ?: 'month';

    if ($period === 'custom' && $request->start_date && $request->end_date) {
        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();
    } else {
        switch ($period) {
            case 'today':
                $startDate = Carbon::today();
                $endDate = Carbon::today()->endOfDay();
                break;
            case 'week':
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                break;
            case 'year':
                $startDate = Carbon::now()->startOfYear();
                $endDate = Carbon::now()->endOfYear();
                break;
            case 'month':
            default:
                $period = 'month';
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
        }
    }

  
    $baseQuery = Transaction::query()
        ->where('payment_status', 'paid')
        ->whereBetween('created_at', [$startDate, $endDate]);

    // RINGKASAN UTAMA
    $totalSales = (clone $baseQuery)->sum('total');
    $totalProfit = (clone $baseQuery)->sum('profit');
    $totalServiceCharge = (clone $baseQuery)->sum('service_charge');
    $totalTransactions = (clone $baseQuery)->count();
    $averageTransaction = $totalTransactions > 0 ? $totalSales / $totalTransactions : 0;

    // BREAKDOWN PER METODE PEMBAYARAN
    $paymentBreakdown = (clone $baseQuery)
        ->selectRaw('payment_method, COUNT(*) as total_count, SUM(total) as total_amount')
        ->groupBy('payment_method')
        ->orderByDesc('total_amount')
        ->get();

    // PRODUK TERLARIS PADA PERIODE INI
    $topProducts = TransactionItem::query()
        ->whereHas('transaction', function ($query) use ($startDate, $endDate) {
            $query->where('payment_status', 'paid')
                  ->whereBetween('created_at', [$startDate, $endDate]);
        })
        ->selectRaw('product_id, SUM(quantity) as total_sold, SUM(price * quantity) as total_revenue')
        ->groupBy('product_id')
        ->orderByDesc('total_sold')
        ->with('product')
        ->take(10)
        ->get();

    return view('transaction_summary', [
        'period' => $period,
        'startDate' => $startDate->format('Y-m-d'),
        'endDate' => $endDate->format('Y-m-d'),
        'totalSales' => $totalSales,
        'totalProfit' => $totalProfit,
        'totalServiceCharge' => $totalServiceCharge,
        'totalTransactions' => $totalTransactions,
        'averageTransaction' => $averageTransaction,
        'paymentBreakdown' => $paymentBreakdown,
        'topProducts' => $topProducts,
    ]);
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
