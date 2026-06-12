@extends('layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<div class="pos-container">

    <!-- TOP NAV -->
    

    <!-- MAIN -->
    <div class="main-content">

        <!-- LEFT CART -->
        <div class="cart-panel">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4>Order List</h4>

                <a href="{{ route('cart.clear') }}"
                    class="btn btn-danger btn-sm rounded-pill">
                    Clear
                </a>
            </div>
            @php
                $grandTotal = 0;

                foreach($cart as $item){
                    $grandTotal += $item['price'] * $item['quantity'];
                }
            @endphp
            <!-- CART ITEMS -->
            @foreach($cart as $id => $item)

            @php
                $total = $item['price'] * $item['quantity'];
            @endphp

            <div class="cart-item">

                <div>
                    <strong>{{ $item['name'] }}</strong>

                    <div class="small text-muted">
                        Rp {{ number_format($item['price']) }}
                    </div>
                </div>

                <div class="qty-box">

                    <span>{{ $item['quantity'] }}</span>

                </div>

            </div>

            @endforeach

            <hr>

            <div class="d-flex justify-content-between mb-4">
                <strong>Total</strong>

                <strong>
                    Rp {{ number_format($grandTotal) }}
                </strong>
            </div>

            <!-- SERVICE -->
            <div class="mb-3">
                <label>Service Charge</label>

                <input type="number"
                       id="service_charge"
                       class="form-control rounded-pill">
            </div>

            <!-- CHECKOUT -->
            <button type="button"
        class="checkout-btn"
        data-bs-toggle="modal"
        data-bs-target="#paymentModal"
        {{ empty($cart) ? 'disabled' : '' }}>

    Proceed
</button>
<div class="modal fade" id="paymentModal" tabindex="-1">

    <div class="modal-dialog modal-fullscreen">

        <div class="modal-content payment-page">

            <div class="modal-body">

                <div class="payment-container">

                    <!-- LEFT -->
                    <div class="payment-left">

                        <h3 class="fw-bold mb-4">
                            Order List
                        </h3>

                        @foreach($cart as $item)

                        @php
                            $total = $item['price'] * $item['quantity'];
                        @endphp

                        <div class="payment-item">

                            <div>
                                <strong>{{ $item['name'] }}</strong>
                                <br>
                                {{ $item['quantity'] }}x
                            </div>

                            <div>
                                Rp {{ number_format($total) }}
                            </div>

                        </div>

                        @endforeach

                        <hr>

                        <div class="d-flex justify-content-between">
                            <strong>Total</strong>

                            <strong>
                                Rp {{ number_format($grandTotal) }}
                            </strong>
                        </div>

                    </div>

                    <!-- RIGHT -->
                    <div class="payment-right">

                        <!-- TOTAL -->
                        <div class="payment-total">

                            <small>Total</small>

                            <h1>
                                Rp {{ number_format($grandTotal) }}
                            </h1>

                        </div>

                        <!-- PAYMENT METHODS -->
                        <div class="payment-grid">

                            <button type="button"
                                    class="payment-method active"
                                    onclick="selectPayment('Cash', this)">

                                Cash

                            </button>

                            <button type="button"
                                    class="payment-method"
                                    onclick="selectPayment('QRIS', this)">

                                QRIS

                            </button>
                            <button type="button"
                                    class="payment-method"
                                    onclick="selectPayment('Transfer', this)">

                                Transfer

                            </button>
                            <button type="button"
                                    class="payment-method"
                                    onclick="selectPayment('GoFood', this)">

                                GoFood

                            </button>
                            <button type="button"
                                    class="payment-method"
                                    onclick="selectPayment('GrabFood', this)">

                                GrabFood

                            </button>
                            <button type="button"
                                    class="payment-method "
                                    onclick="selectPayment('Dana', this)">

                                Dana

                            </button>
                            <button type="button"
                                    class="payment-method "
                                    onclick="selectPayment('OVO', this)">

                                OVO

                            </button>

                            

                        </div>

                        <!-- PAY FORM -->
                        <form action="/checkout" method="POST">
                            @csrf

                            <input type="hidden"
                                   name="service_charge"
                                   id="service_charge_hidden_modal">
                                   
                            <input type="hidden"
                            name="payment_method"
                            id="payment_method"
                            value="Cash">
                            <!-- CANCEL + PAY -->
                            <div class="d-flex gap-3">

                                <!-- CANCEL -->
                                <button type="button"
                                        class="cancel-btn"
                                        data-bs-dismiss="modal">

                                    Cancel
                                </button>

                                <!-- PAY -->
                                <button type="submit"
                                    class="pay-button flex-grow-1">
                                    Pay
                                </button>

                            </div>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

            

        </div>

        <!-- RIGHT PRODUCTS -->
        <div class="products-panel">

            <!-- SEARCH -->
            <div class="d-flex justify-content-between mb-4">

                <div>
                    @foreach($categories as $cat)
                        <a href="{{ route('dashboard', ['category'=>$cat]) }}"
                           class="category-btn">
                            {{ $cat }}
                        </a>
                    @endforeach
                </div>

                <input type="text"
                       class="search-box"
                       placeholder="Search">
            </div>

            <!-- PRODUCTS -->
            <div class="products-grid">

                @foreach($products as $product)

                <div class="product-card">

                    <img src="{{ asset('storage/'.$product->image) }}">

                    <div class="product-info">

                        <h6>{{ $product->name }}</h6>

                        <p>
                            Rp {{ number_format($product->price) }}
                        </p>

                        @if($product->stock > 0)

                        <button class="add-btn"
                                data-bs-toggle="modal"
                                data-bs-target="#cartModal{{ $product->id }}">
                            Add to Cart
                        </button>

                        @else

                        <button class="add-btn disabled">
                            Out of Stock
                        </button>

                        @endif

                    </div>

                </div>

                <div class="modal fade" id="cartModal{{ $product->id }}" tabindex="-1">

    <div class="modal-dialog">

        <form action="{{ route('cart.add') }}" method="POST">
            @csrf

            <input type="hidden"
                   name="product_id"
                   value="{{ $product->id }}">

            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">
                        {{ $product->name }}
                    </h5>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal">
                    </button>
                </div>

                <div class="modal-body">

                    <!-- QUANTITY -->
                    <div class="mb-3">
                        <label>Quantity</label>

                        <input type="number"
                               name="quantity"
                               class="form-control"
                               min="1"
                               max="{{ $product->stock }}"
                               value="1"
                               required>
                    </div>

                    <!-- DISCOUNT TYPE -->
                    <div class="mb-3">
                        <label>Discount Type</label>

                        <select name="discount_type"
                                class="form-control">

                            <option value="none">
                                No Discount
                            </option>

                            <option value="percent">
                                Percentage (%)
                            </option>

                            <option value="fixed">
                                Fixed (Rp)
                            </option>

                        </select>
                    </div>

                    <!-- DISCOUNT VALUE -->
                    <div class="mb-3">
                        <label>Discount Value</label>

                        <input type="number"
                               name="discount_value"
                               class="form-control"
                               value="0"
                               min="0">
                    </div>

                </div>

                <div class="modal-footer">

                    <button class="btn btn-success w-100">
                        Add to Cart
                    </button>

                </div>

            </div>

        </form>

    </div>

</div>

                @endforeach

            </div>

        </div>

    </div>

</div>
<script>

function selectPayment(method, button)
{
    // remove active class from all buttons
    document.querySelectorAll('.payment-method').forEach(btn => {
        btn.classList.remove('active');
    });

    // activate clicked button
    button.classList.add('active');

    // update hidden input
    document.getElementById('payment_method').value = method;

    console.log(method); // optional debug
}

</script>

<style>
    /* .pos-container{
    background: #efefdd;
    min-height: 100vh;
} */

.topbar{
    height: 80px;
    background: #F97316;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 40px;
}

.main-content{
    display: flex;
    padding: 30px;
    gap: 30px;
}

.cart-panel{
    width: 30%;
    border-right: 2px solid #999;
    padding-right: 30px;
}

.products-panel{
    width: 70%;
}

.products-grid{
    display: grid;
    grid-template-columns: repeat(3,1fr);
    gap: 20px;
}

.product-card{
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0,0,0,0.15);
}

.product-card img{
    width: 100%;
    height: 180px;
    object-fit: cover;
}

.product-info{
    padding: 15px;
    text-align: center;
}

.add-btn{
    width: 100%;
    border: none;
    background: #F97316;
    padding: 10px;
    border-radius: 10px;
}

.checkout-btn{
    width: 100%;
    border: none;
    background: #F97316;
    padding: 14px;
    border-radius: 999px;
}

.search-box{
    border-radius: 999px;
    border: 1px solid #999;
    padding: 10px 20px;
    width: 250px;
}

.category-btn{
    background: #F97316;
    padding: 8px 18px;
    border-radius: 999px;
    text-decoration: none;
    color: black;
    margin-right: 10px;
}

.payment-page{
    background: #efefdd;
}

.payment-container{
    display: flex;
    height: 100vh;
}

.payment-left{
    width: 40%;
    padding: 40px;
    border-right: 2px solid #ccc;
}

.payment-right{
    width: 60%;
    padding: 40px;
}

.payment-item{
    display: flex;
    justify-content: space-between;
    margin-bottom: 20px;
}

.payment-total{
    background: #F97316;
    color: white;
    border-radius: 15px;
    padding: 20px;
    text-align: center;
    margin-bottom: 40px;
}

.payment-grid{
    display: grid;
    grid-template-columns: repeat(3,1fr);
    gap: 20px;
    margin-bottom: 40px;
}

.payment-method{
    background: white;
    border: none;
    border-radius: 15px;
    padding: 40px 20px;
    font-weight: bold;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.pay-button{
    background: #F97316;
    border: none;
    color: white;
    padding: 16px;
    border-radius: 12px;
    font-weight: bold;
}

.cancel-btn{
    background: #ddd;
    border: none;
    padding: 16px 30px;
    border-radius: 12px;
    font-weight: bold;
}
.payment-method.active{
    background: #F97316 !important;
    color: white !important;
    border: 3px solid #ea580c !important;
    transform: scale(1.03);
}
</style>
@endsection

