@extends('layouts.app')

@section('content')

<div class="container">

<h3 class="mb-4">Edit Product</h3>

<form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    

    <div class="row">

        <!-- LEFT SIDE -->
        <div class="col-md-6">

            <!-- Name -->
            <div class="mb-3">
                <label>Product Name</label>
                <input type="text" name="name" class="form-control"
                    value="{{ $product->name }}" required>
            </div>

            <!-- Category -->
            <div class="mb-3">
                <label>Category</label>
                <input type="text" name="category" class="form-control"
                    value="{{ $product->category }}" required>
            </div>

            <!-- Buy Price -->
            <div class="mb-3">
                <label>Buy Price</label>
                <input type="number" name="buy_price" id="buy_price"
                    class="form-control" value="{{ $product->buy_price }}" required>
            </div>

            <!-- Sell Price -->
            <div class="mb-3">
                <label>Sell Price</label>
                <input type="number" name="price" id="price"
                    class="form-control" value="{{ $product->price }}" required>
            </div>

            <!-- Profit Buttons -->
            <div class="mb-3">
                <label>Quick Margin</label><br>
                <button type="button" class="btn btn-sm btn-outline-success" onclick="setMargin(5)">+5%</button>
                <button type="button" class="btn btn-sm btn-outline-success" onclick="setMargin(15)">+15%</button>
                <button type="button" class="btn btn-sm btn-outline-success" onclick="setMargin(20)">+20%</button>
            </div>

            <!-- Tax Option -->
            <div class="mb-3">
                <label>Price After Tax?</label>
                <select name="after_tax" class="form-control">
                    <option value="0" {{ !$product->after_tax ? 'selected' : '' }}>No</option>
                    <option value="1" {{ $product->after_tax ? 'selected' : '' }}>Yes</option>
                </select>
            </div>

            <!-- Stock -->
            <div class="mb-3">
                <label>Stock</label>
                <input type="number" name="stock" class="form-control"
                    value="{{ $product->stock }}" required>
            </div>

            <!-- Active Toggle -->
            <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" name="is_active"
                    {{ $product->is_active ? 'checked' : '' }}>
                <label class="form-check-label">Active Product</label>
            </div>

        </div>

        <!-- RIGHT SIDE -->
        <div class="col-md-6 text-center">

            <!-- Current Image -->
            @if($product->image)
                <img src="{{ asset('storage/'.$product->image) }}" 
                     class="img-fluid mb-3" 
                     style="max-height: 200px;">
            @endif

            <!-- Upload New Image -->
            <div class="mb-3">
                <label>Change Image</label>
                <input type="file" name="image" class="form-control">
            </div>

        </div>

    </div>

    <!-- ACTION BUTTONS -->
    <div class="mt-3 d-flex justify-content-between">

        <button class="btn btn-primary">
            💾 Update Product
        </button>

       

    </div>

</form>

<!-- <form action="{{ route('products.delete', $product->id) }}"
          method="POST"
          onsubmit="return confirm('Are you sure you want to delete this product?')">
        @csrf
        @method('DELETE')

        <button class="btn btn-danger">
            Deactivate
        </button>
</form>

@if(!$product->is_active)
<form action="{{ route('products.activate', $product->id) }}"
      method="POST"
      class="mt-2">
    @csrf
    <button class="btn btn-success ">
        Reactivate Product
    </button>
</form>
@endif -->

<a href="{{ route('products') }}" class="btn btn-secondary">Back</a>
</div>

@endsection

@section('scripts')
<script>
function setMargin(percent) {
    let buy = document.getElementById('buy_price').value;
    let sell = buy * (1 + percent / 100);
    document.getElementById('price').value = Math.round(sell);
}
</script>
@endsection