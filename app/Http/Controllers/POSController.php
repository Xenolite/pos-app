<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Carbon\Carbon;
class POSController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->category;
        $products = Product::where('is_active', true)
        ->when($category, function ($query) use ($category) {
            $query->where('category', $category);
        })
        ->get();

        $lowStockProducts = Product::where('stock', '<=', \DB::raw('low_stock_limit'))
    ->where('is_active', 1)
    ->get();

    $cart = session()->get('cart', []);
    $category = $request->category;
    // 🔥 Today's sales
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
    $discountValue = $request->discount_value;

    $price = $product->price;

    // 🔥 APPLY DISCOUNT
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

   public function checkout()
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

    $transaction = Transaction::create([
        'user_id' => auth()->id(),
        'total' => $total,
        'profit' => $totalProfit,
        'service_charge' => $serviceCharge,
        'payment_method' => $paymentMethod
    ]);

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

    session()->forget('cart');

    return redirect()->back()->with('success', 'Transaction completed!');
}

public function products(Request $request)
{
    $category = $request->category;

    $categories = Product::select('category')
        ->distinct()
        ->pluck('category');

    $products = Product::when($category, function($query) use ($category){
            $query->where('category', $category);
        });

    // CASHIER ONLY SEES ACTIVE PRODUCTS
    if(auth()->user()->role !== 'admin'){
        $products->where('is_active', 1);
    }

    $products = $products->get();

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
}
