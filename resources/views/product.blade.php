@extends('layouts.app')

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3"
     style="z-index: 9999;" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<script>
    setTimeout(() => {
        document.querySelector('.alert')?.remove();
    }, 3000);
</script>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3"
     style="z-index: 9999;" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<script>
    setTimeout(() => {
        document.querySelector('.alert')?.remove();
    }, 3000);
</script>
@endif

<div class="products-page">

    <!-- LEFT SIDEBAR -->
    <div class="categories-sidebar">

        <div>

            <h4 class="sidebar-title">
                Categories
            </h4>

            <!-- ALL -->
            <a href="{{ route('products') }}"
               class="category-item {{ !$category ? 'active-category' : '' }}">

                All Products

            </a>

            <!-- CATEGORY LOOP -->
            @foreach($categories as $cat)

            <a href="{{ route('products', ['category' => $cat]) }}"
               class="category-item {{ $category == $cat ? 'active-category' : '' }}"
               title="{{ $cat }}">

                {{ \Illuminate\Support\Str::limit($cat, 18) }}

            </a>

            @endforeach

        </div>

    </div>

    <!-- RIGHT CONTENT -->
    <div class="products-content">

        <!-- HEADER -->
        <div class="products-header">

            

            <!-- ADMIN ONLY -->
            @if(auth()->user()->role === 'admin')

            <a href="{{ route('products.create') }}"
               class="add-product-btn">

                + Add Product

            </a>

            @endif

        </div>

        <!-- PRODUCTS GRID -->
        <div class="products-grid">

            @foreach($products as $product)

            <div class="product-card">

                <!-- IMAGE -->
                <div class="product-image-wrapper">

                    <img src="{{ $product->image_url }}"
                         class="product-image">

                    @if($product->stock <= 5)

                    <div class="stock-badge">
                        Low Stock
                    </div>

                    @endif

                </div>

                <!-- CONTENT -->
                <div class="product-info">

                    <h5 class="product-name" title="{{ $product->name }}">
                        {{ \Illuminate\Support\Str::limit($product->name, 20) }}
                    </h5>

                    <div class="product-category" title="{{ $product->category }}">
                        {{ \Illuminate\Support\Str::limit($product->category, 15) }}
                    </div>

                    <div class="product-price">
                        Rp {{ number_format($product->price) }}
                    </div>

                    <div class="product-stock">
                        Stock: {{ $product->stock }}
                    </div>

                    <!-- ADMIN CONTROLS -->
                    @if(auth()->user()->role === 'admin')

                    <div class="product-actions">

                        <!-- EDIT -->
                        <a href="{{ route('products.edit', $product->id) }}"
                           class="btn-edit">

                            Edit

                        </a>

                        <!-- ACTIVE -->
                        @if($product->is_active)

                        <form action="{{ route('products.delete', $product->id) }}"
                              method="POST"
                              class="w-100">

                            @csrf
                            @method('DELETE')

                            <button class="btn-delete w-100">
                                Deactivate
                            </button>

                        </form>

                        <!-- INACTIVE -->
                        @else

                        <form action="{{ route('products.activate', $product->id) }}"
                              method="POST"
                              class="w-100">

                            @csrf

                            <button class="btn-reactivate w-100">
                                Reactivate
                            </button>

                        </form>

                        <!--  PERMANENT DELETE  -->
                        <form action="{{ route('products.forceDelete', $product->id) }}"
                              method="POST"
                              class="w-100"
                              onsubmit="return confirm('DELETE The Product PERMANENTLY? This Action Cannot be Undone.');">

                            @csrf
                            @method('DELETE')

                            <button class="btn-delete-permanent w-100">
                                Delete
                            </button>

                        </form>

                        @endif

                    </div>

                    @endif

                </div>

            </div>

            @endforeach

        </div>

    </div>

</div>

<style>

.products-page{
    display: flex;
    gap: 30px;
    min-height: calc(100vh - 100px);
}

/* SIDEBAR */

.categories-sidebar{
    width: 260px;
    background: white;
    border-radius: 24px;
    padding: 30px;
    height: fit-content;
    box-shadow: 0 10px 30px rgba(0,0,0,0.06);
}

.sidebar-title{
    font-weight: 700;
    margin-bottom: 25px;
}

.category-item{
    display: block;
    padding: 14px 18px;
    margin-bottom: 12px;
    border-radius: 14px;
    text-decoration: none;
    color: #444;
    transition: .2s;
    font-weight: 500;
}

.category-item:hover{
    background: #fff7ed;
    color: #F97316;
}

.active-category{
    background: #F97316;
    color: white !important;
}

/* CONTENT */

.products-content{
    flex: 1;
}

.products-header{
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 35px;
}

.page-title{
    font-weight: 700;
    margin-bottom: 5px;
}

.page-subtitle{
    color: #888;
}

/* ADD BUTTON */

.add-product-btn{
    background: #F97316;
    color: white;
    padding: 14px 22px;
    border-radius: 14px;
    text-decoration: none;
    font-weight: 600;
    transition: .2s;
}

.add-product-btn:hover{
    background: #ea580c;
    color: white;
}

/* GRID */

.products-grid{
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 25px;
}

/* CARD */

.product-card{
    background: white;
    border-radius: 24px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.06);
    transition: .25s;
}

.product-card:hover{
    transform: translateY(-5px);
}

.product-image-wrapper{
    position: relative;
}

.product-image{
    width: 100%;
    height: 220px;
    object-fit: cover;
}

/* BADGE */

.stock-badge{
    position: absolute;
    top: 15px;
    right: 15px;
    background: #ef4444;
    color: white;
    padding: 6px 12px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: bold;
}

/* INFO */

.product-info{
    padding: 22px;
}

.product-name{
    font-weight: 700;
    margin-bottom: 8px;
}

.product-category{
    color: #999;
    margin-bottom: 10px;
    font-size: 14px;
}

.product-price{
    color: #F97316;
    font-size: 22px;
    font-weight: bold;
    margin-bottom: 8px;
}

.product-stock{
    color: #666;
    margin-bottom: 20px;
}

/* ACTIONS */

.product-actions{
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.btn-edit,
.btn-delete,
.btn-reactivate{
    border: none;
    padding: 10px;
    border-radius: 12px;
    text-align: center;
    text-decoration: none;
    font-weight: 600;
    transition: .2s;
}

.btn-edit{
    flex: 1;
    background: #F97316;
    color: white;
}

.btn-edit:hover{
    background: #ea580c;
}

.btn-delete{
    background: #ef4444;
    color: white;
}

.btn-reactivate{
    background: #22c55e;
    color: white;
}

.btn-delete-permanent{
    flex-basis: 100%;
    background: transparent;
    color: #ef4444;
    border: 1.5px solid #ef4444 !important;
}

.btn-delete-permanent:hover{
    background: #ef4444;
    color: white;
}

</style>

@endsection