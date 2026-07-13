<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
class ProductController extends Controller
{
    
    public function create()
    {
        $categories = Product::select('category')->distinct()->orderBy('category')->pluck('category');

        return view('admin.create-product', compact('categories'));
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

    // Upload image ke Supabase Storage (bukan disk lokal), supaya file
    // tetap ada meskipun aplikasi di-redeploy di Railway.
    //
    // PENTING: folder di sini ("uploads") sengaja BEDA dari nama bucket
    // ("products", lihat SUPABASE_STORAGE_BUCKET / SUPABASE_STORAGE_URL
    // di .env). Kalau nama folder di sini sama persis dengan nama bucket,
    // URL publik yang di-generate Product::image_url akan double
    // (.../public/products/products/nama-file.jpg) karena nama bucket
    // sudah ada di SUPABASE_STORAGE_URL, jadi jangan diulang lagi di sini.
    $imagePath = null;
    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('uploads', 'supabase');
    }

    Product::create([
        'name' => $request->name,
        'category' => Str::title(trim($request->category)),
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
    $categories = Product::select('category')->distinct()->orderBy('category')->pluck('category');

    return view('admin.edit-product', compact('product', 'categories'));
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
        'category' => Str::title(trim($request->category)),
        'price' => $request->price,
        'buy_price' => $request->buy_price,
        'stock' => $request->stock,
        'is_active' => (bool) $request->has('is_active'),
        'price_after_tax' => (bool) $request->has('price_after_tax'),
    ];

    if ($request->hasFile('image')) {
        if ($product->image) {
            Storage::disk('supabase')->delete($product->image);
        }
        $data['image'] = $request->file('image')->store('uploads', 'supabase');
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

public function forceDelete($id)
{
    $product = Product::findOrFail($id);

    try {
        if ($product->image) {
            Storage::disk('supabase')->delete($product->image);
        }

        $product->delete();
    } catch (\Illuminate\Database\QueryException $e) {

        return redirect()->route('products')
            ->with('error', 'Product cannot be permanently deleted because it has transaction history. Use Deactivate instead.');
    }

    return redirect()->route('products')
        ->with('success', 'Product permanently deleted!');
}
}
