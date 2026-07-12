@extends('layouts.app')

@section('content')

<div class="container">

<h3 class="mb-4">Add New Product</h3>

@if($errors->any())
<div class="alert alert-danger">
    {{ $errors->first() }}
</div>
@endif

<form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
@csrf

<div class="mb-3">
    <label>Name</label>
    <input type="text" name="name" class="form-control" required>
</div>

<div class="mb-2">
    <label>Category</label>
    <input type="text" name="category" class="form-control" list="categoryList" required>
    <datalist id="categoryList">
        @foreach($categories as $cat)
            <option value="{{ $cat }}">
        @endforeach
    </datalist>
</div>

<div class="mb-3">
    <label>Buy Price</label>
    <input type="number" id="buy_price" name="buy_price" class="form-control" required>
</div>

<div class="mb-3">
    <label>Sell Price</label>
    <input type="number" id="sell_price" name="price" class="form-control" required>
</div>

<!--  Margin Buttons -->
<div class="mb-3">
    <button type="button" class="btn btn-outline-primary btn-sm" onclick="setMargin(5)">+5%</button>
    <button type="button" class="btn btn-outline-primary btn-sm" onclick="setMargin(15)">+15%</button>
    <button type="button" class="btn btn-outline-primary btn-sm" onclick="setMargin(20)">+20%</button>
</div>

<!-- After Tax -->
<div class="form-check mb-3">
    <input type="checkbox" name="price_after_tax" id="taxCheck" class="form-check-input">
    <label class="form-check-label">Price includes tax (10%)</label>
</div>

<div class="mb-3">
    <label>Stock</label>
    <input type="number" name="stock" class="form-control">
</div>

<!-- Image Upload -->
<div class="mb-3">
    <label>Product Image</label>
    <input type="file" name="image" class="form-control" accept="image/png, image/jpeg">
</div>

<button class="btn btn-success">Save</button>

</form>

<a href="{{ route('products') }}" class="mt-3 btn btn-secondary">Back</a>
</div>
</div>


@endsection
@section('scripts')
<script>
function setMargin(percent) {
    let buy = parseFloat(document.getElementById('buy_price').value);
    if (!buy) return;

    let sell = buy + (buy * percent / 100);

    // tax logic
    if (document.getElementById('taxCheck').checked) {
        sell = sell + (sell * 0.10);
    }

    document.getElementById('sell_price').value = Math.round(sell);
}
</script>
@endsection