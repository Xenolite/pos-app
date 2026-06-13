<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
class ProductController extends Controller
{
    
    public function create()
    {
        return view('admin.create-product');
    }

    public function store(Request $request)
{
    $request->validate([
        'name' => 'required',
        'buy_price' => 'required|integer',
        'price' => 'required|integer',
        'stock' => 'required|integer',
        'image' => 'image|mimes:jpg,png,jpeg|max:2048'
    ]);

    // Upload image
    $imagePath = null;
    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('products', 'public');
    }

    Product::create([
        'name' => $request->name,
        'category' => $request->category,
        'buy_price' => $request->buy_price,
        'price' => $request->price,
        'stock' => $request->stock,
        'image' => $imagePath,
        'price_after_tax' => (bool) $request->has('price_after_tax'),
        'is_active' => true,
    ]);

    return redirect()->route('products')->with('success', 'Product added!');
}

    public function edit($id)
{
    $product = Product::findOrFail($id);
    return view('admin.edit-product', compact('product'));
}

public function update(Request $request, $id)
{
    $request->validate([
        'name' => 'required',
        'category' => 'required',
        'price' => 'required|numeric',
        'buy_price' => 'required|numeric',
        'stock' => 'required|integer',
        'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
    ]);

    $product = Product::findOrFail($id);
    
    $data = [
        'name' => $request->name,
        'category' => $request->category,
        'price' => $request->price,
        'buy_price' => $request->buy_price,
        'stock' => $request->stock,
        'is_active' => (bool) $request->has('is_active'),
        'price_after_tax' => (bool) $request->has('price_after_tax'),
    ];

    if ($request->hasFile('image')) {
        if ($product->image) {
            Storage::delete('public/' . $product->image);
        }
        $data['image'] = $request->file('image')->store('products', 'public');
    }

    $product->update($data);

    return redirect()->route('products')
        ->with('success', 'Product updated successfully!');
}

public function updateStock(Request $request, $id)
{
    $request->validate([
        'stock' => 'required|integer|min:0'
    ]);

    $product = Product::findOrFail($id);
    $product->stock = $request->stock;
    $product->save();

    return redirect()->back()->with('success', 'Stock updated!');
}
public function destroy($id)
{
    $product = Product::findOrFail($id);

    $product->is_active = false;
    $product->save();

    return redirect()->route('products')
        ->with('success', 'Product deactivated!');
}

public function activate($id)
{
    $product = Product::findOrFail($id);

    $product->is_active = true;
    $product->save();

    return redirect()->route('products')
        ->with('success', 'Product reactivated!');
}
}
