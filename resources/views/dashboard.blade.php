@extends('layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

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

<div class="pos-container">

    <!-- MAIN -->
    <div class="main-content">

        @php
            $subtotal = 0;
            $discountTotal = 0;

            foreach($cart as $item){
                $lineOriginal = $item['original_price'] * $item['quantity'];
                $lineActual = $item['price'] * $item['quantity'];

                $subtotal += $lineOriginal;
                $discountTotal += ($lineOriginal - $lineActual);
            }

            // Belum ada mekanisme pajak transaksi di backend saat ini —
            // price_after_tax di produk cuma flag apakah harga jual sudah
            // termasuk pajak atau belum, bukan nilai pajak yang dihitung
            // terpisah. Ditampilkan Rp 0 sampai ada logic pajak yang jelas.
            $taxTotal = 0;
            $grandTotal = $subtotal - $discountTotal;
        @endphp

        <!-- LEFT CART -->
        <div class="cart-panel">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">Order List</h4>

                @if(!empty($cart))
                <a href="{{ route('cart.clear') }}" class="clear-link">
                    Clear
                </a>
                @endif
            </div>

            <!-- ORDER TABLE -->
            <table class="order-table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th class="text-center">Qty</th>
                        <th class="text-end">Total</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cart as $id => $item)
                    @php
                        $lineTotal = $item['price'] * $item['quantity'];
                    @endphp
                    <tr>
                        <td>{{ $item['name'] }}</td>
                        <td class="text-center">
                            <form action="{{ route('cart.updateQuantity', $id) }}"
                                  method="POST"
                                  class="qty-form">
                                @csrf
                                <input type="number"
                                       name="quantity"
                                       value="{{ $item['quantity'] }}"
                                       min="1"
                                       class="qty-input"
                                       onchange="this.form.submit()">
                            </form>
                        </td>
                        <td class="text-end">Rp {{ number_format($lineTotal) }}</td>
                        <td class="text-center">
                            <a href="{{ url('/remove/'.$id) }}" class="remove-item-btn" title="Remove item">
                                &times;
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">
                            No items yet — add a product to start an order.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <hr>

            <div class="d-flex justify-content-between mb-3">
                <strong>Total</strong>
                <strong>Rp {{ number_format($grandTotal) }}</strong>
            </div>

            <!-- SERVICE -->
            <div class="mb-3">
                <label class="small text-muted mb-1">Service Charge</label>

                <input type="number"
                       id="service_charge"
                       class="form-control rounded-pill"
                       value="0"
                       min="0">
            </div>

            <!-- PAY -->
            <button type="button"
                    class="pay-trigger-btn"
                    data-bs-toggle="modal"
                    data-bs-target="#paymentModal"
                    {{ empty($cart) ? 'disabled' : '' }}>
                Pay
            </button>

            <!-- PAYMENT MODAL -->
            <div class="modal fade" id="paymentModal" tabindex="-1">
                <div class="modal-dialog modal-fullscreen">
                    <div class="modal-content payment-page">
                        <div class="modal-body">
                            <div class="payment-container">

                                <!-- LEFT -->
                                <div class="payment-left">

                                    <h3 class="fw-bold mb-4">Order List</h3>

                                    <table class="order-table">
                                        <thead>
                                            <tr>
                                                <th>Item</th>
                                                <th class="text-center">Qty</th>
                                                <th class="text-end">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($cart as $item)
                                            @php
                                                $lineTotal = $item['price'] * $item['quantity'];
                                            @endphp
                                            <tr>
                                                <td>{{ $item['name'] }}</td>
                                                <td class="text-center">{{ $item['quantity'] }}x</td>
                                                <td class="text-end">Rp {{ number_format($lineTotal) }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>

                                    <hr>

                                    <div class="d-flex justify-content-between">
                                        <strong>Total</strong>
                                        <strong>Rp {{ number_format($grandTotal) }}</strong>
                                    </div>

                                </div>

                                <!-- RIGHT -->
                                <div class="payment-right">

                                    <!-- TOTAL -->
                                    <div class="payment-total">
                                        <small>Total</small>
                                        <h1 id="payment_total_display">
                                            Rp {{ number_format($grandTotal) }}
                                        </h1>
                                    </div>

                                    <!-- PAYMENT METHODS -->
                                    <div class="payment-grid">

                                        <button type="button" class="payment-method active" onclick="selectPayment('Cash', this)">
                                            Cash
                                        </button>

                                        <button type="button" class="payment-method" onclick="selectPayment('QRIS', this)">
                                            <span class="brand-label" style="letter-spacing:1px;">QRIS</span>
                                        </button>

                                        <button type="button" class="payment-method" onclick="selectPayment('Transfer', this)">
                                            Transfer
                                        </button>

                                        <button type="button" class="payment-method" onclick="selectPayment('GoFood', this)">
                                            <span class="brand-label" style="color:#00AA13;">GoFood</span>
                                        </button>

                                        <button type="button" class="payment-method" onclick="selectPayment('GrabFood', this)">
                                            <span class="brand-label" style="color:#00B14F;">GrabFood</span>
                                        </button>

                                        <button type="button" class="payment-method" onclick="selectPayment('ShopeeFood', this)">
                                            <span class="brand-label" style="color:#EE4D2D;">ShopeeFood</span>
                                        </button>

                                        <button type="button" class="payment-method" onclick="selectPayment('Dana', this)">
                                            <span class="brand-label" style="color:#118EEA;">DANA</span>
                                        </button>

                                        <button type="button" class="payment-method" onclick="selectPayment('LinkAja', this)">
                                            <span class="brand-label" style="color:#E4032E;">LinkAja!</span>
                                        </button>

                                        <button type="button" class="payment-method" onclick="selectPayment('OVO', this)">
                                            <span class="brand-label" style="color:#4C3494;">OVO</span>
                                        </button>

                                    </div>

                                    <!-- BREAKDOWN -->
                                    <div class="payment-breakdown">

                                        <div class="d-flex justify-content-between">
                                            <span>Subtotal</span>
                                            <span>Rp {{ number_format($subtotal) }}</span>
                                        </div>

                                        <div class="d-flex justify-content-between">
                                            <span>Total Discount</span>
                                            <span>Rp {{ number_format($discountTotal) }}</span>
                                        </div>

                                        <div class="d-flex justify-content-between">
                                            <span>Total Service Charge</span>
                                            <span id="service_charge_display">Rp 0</span>
                                        </div>

                                        <div class="d-flex justify-content-between">
                                            <span>Total Tax</span>
                                            <span>Rp {{ number_format($taxTotal) }}</span>
                                        </div>

                                        <hr>

                                        <div class="d-flex justify-content-between fw-bold fs-5">
                                            <span>Total ({{ count($cart) }} items)</span>
                                            <span id="payment_breakdown_total">Rp {{ number_format($grandTotal) }}</span>
                                        </div>

                                    </div>

                                    <!-- PAY FORM -->
                                    <form action="/checkout" method="POST" id="checkoutForm">
                                        @csrf

                                        <input type="hidden" name="service_charge" id="service_charge_hidden_modal">
                                        <input type="hidden" name="payment_method" id="payment_method" value="Cash">

                                        <div class="d-flex gap-3">
                                            <button type="button" class="cancel-btn" data-bs-dismiss="modal">
                                                Cancel
                                            </button>

                                            <button type="submit" class="pay-button flex-grow-1" id="payButton">
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

            <!-- CATEGORIES + SEARCH -->
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

                <div class="category-list">
                    <a href="{{ route('dashboard', ['search' => request('search')]) }}"
                       class="category-btn {{ !request('category') ? 'active' : '' }}">
                        All
                    </a>
                    @foreach($categories as $cat)
                        <a href="{{ route('dashboard', ['category' => $cat, 'search' => request('search')]) }}"
                           class="category-btn {{ request('category') == $cat ? 'active' : '' }}">
                            {{ $cat }}
                        </a>
                    @endforeach
                </div>

                <form method="GET" action="{{ route('dashboard') }}" class="search-form">
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           class="search-box"
                           placeholder="Search">
                </form>
            </div>

            <!-- PRODUCTS -->
            <div class="products-grid">

                @forelse($products as $product)

                <div class="product-card">

                    <img src="{{ asset('storage/'.$product->image) }}">

                    <div class="product-info">

                        <h6>{{ $product->name }}</h6>

                        <p>
                            Rp {{ number_format($product->price) }}
                        </p>
                        <p class="small text-muted">
                            Stock: {{ $product->stock }}
                        </p>

                        @if($product->stock > 0)

                        <button class="add-btn"
                                data-bs-toggle="modal"
                                data-bs-target="#cartModal{{ $product->id }}">
                            Add to Cart
                        </button>

                        @else

                        <button class="add-btn-disabled disabled">
                            Out of Stock
                        </button>

                        @endif

                    </div>

                </div>

                <div class="modal fade" id="cartModal{{ $product->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <form action="{{ route('cart.add') }}" method="POST">
                            @csrf

                            <input type="hidden" name="product_id" value="{{ $product->id }}">

                            <div class="modal-content">

                                <div class="modal-header">
                                    <h5 class="modal-title">{{ $product->name }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">

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

                                    <div class="mb-3">
                                        <label>Discount Type</label>
                                        <select name="discount_type"
                                                class="form-control"
                                                onchange="toggleDiscountValue(this)">
                                            <option value="none">No Discount</option>
                                            <option value="percent">Percentage (%)</option>
                                            <option value="fixed">Fixed (Rp)</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label>Discount Value</label>
                                        <input type="number"
                                               name="discount_value"
                                               class="form-control"
                                               value="0"
                                               min="0"
                                               disabled>
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

                @empty
                <div class="text-center text-muted py-5" style="grid-column: 1 / -1;">
                    No products found.
                </div>
                @endforelse

            </div>

        </div>

    </div>

</div>

<script>

function selectPayment(method, button)
{
    document.querySelectorAll('.payment-method').forEach(btn => {
        btn.classList.remove('active');
    });

    button.classList.add('active');
    document.getElementById('payment_method').value = method;
}

// CASH tetap submit form biasa (redirect langsung, transaksi lunas di kasir).
// Metode lain (QRIS/Transfer/e-wallet/dst) dikirim via AJAX ke /checkout,
// lalu Midtrans Snap dimunculkan sebagai popup pembayaran.
document.getElementById('checkoutForm').addEventListener('submit', function (e) {

    const method = document.getElementById('payment_method').value;

    if (method === 'Cash') {
        return; // biarkan submit form normal
    }

    e.preventDefault();

    const form = this;
    const payButton = document.getElementById('payButton');
    payButton.disabled = true;
    payButton.textContent = 'Processing...';

    fetch(form.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            'Accept': 'application/json',
        },
        body: new FormData(form),
    })
    .then(res => res.json())
    .then(data => {

        payButton.disabled = false;
        payButton.textContent = 'Pay';

        if (!data.snap_token) {
            alert('Failed to start payment. Please try again.');
            return;
        }

        snap.pay(data.snap_token, {
            onSuccess: function () {
                window.location.href = window.location.pathname;
            },
            onPending: function () {
                alert('Payment is pending. The transaction will be confirmed once payment is completed.');
                window.location.href = window.location.pathname;
            },
            onError: function () {
                alert('Payment failed. Please try again.');
            },
            onClose: function () {
                // Customer closed the Snap popup without finishing payment;
                // transaction stays "pending" and stock is untouched.
            }
        });
    })
    .catch(() => {
        payButton.disabled = false;
        payButton.textContent = 'Pay';
        alert('Something went wrong. Please try again.');
    });
});

// Discount Value field should only be editable when a discount type
// (Percentage/Fixed) is actually selected — keeps it disabled + zeroed
// while "No Discount" is picked, so it can't be filled in for nothing.
function toggleDiscountValue(select)
{
    const form = select.closest('form');
    const input = form.querySelector('input[name="discount_value"]');

    if (select.value === 'none') {
        input.value = 0;
        input.disabled = true;
    } else {
        input.disabled = false;
    }
}

// Keep the payment modal's service charge + total breakdown in sync
// with whatever the cashier types in the main Order List panel.
function syncServiceCharge()
{
    const raw = document.getElementById('service_charge').value;
    const value = parseInt(raw) || 0;
    const grandTotal = {{ $grandTotal }};
    const newTotal = grandTotal + value;

    document.getElementById('service_charge_hidden_modal').value = value;
    document.getElementById('service_charge_display').textContent =
        'Rp ' + value.toLocaleString('id-ID');
    document.getElementById('payment_total_display').textContent =
        'Rp ' + newTotal.toLocaleString('id-ID');
    document.getElementById('payment_breakdown_total').textContent =
        'Rp ' + newTotal.toLocaleString('id-ID');
}

document.getElementById('service_charge').addEventListener('input', syncServiceCharge);
document.addEventListener('DOMContentLoaded', syncServiceCharge);

</script>

<style>

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

.clear-link{
    color: #ef4444;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
}

.clear-link:hover{
    text-decoration: underline;
}


.order-table{
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 10px;
}

.order-table thead th{
    text-align: left;
    font-size: 13px;
    color: #666;
    font-weight: 600;
    padding-bottom: 10px;
    border-bottom: 1px solid #ccc;
}

.order-table tbody td{
    padding: 10px 0;
    border-bottom: 1px solid #eee;
    font-size: 14px;
}

.qty-form{
    display: inline-block;
}

.qty-input{
    width: 55px;
    text-align: center;
    border: 1px solid #ccc;
    border-radius: 8px;
    padding: 4px 2px;
    font-size: 14px;
}

.qty-input:focus{
    outline: none;
    border-color: #F97316;
}

.remove-item-btn{
    color: #ef4444;
    font-size: 20px;
    line-height: 1;
    text-decoration: none;
    font-weight: bold;
}

.remove-item-btn:hover{
    color: #b91c1c;
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
.add-btn-disabled{
    width: 100%;
    border: none;
    background: #a8a6a4;
    padding: 10px;
    border-radius: 10px;
}

.pay-trigger-btn{
    width: 100%;
    border: none;
    background: #F97316;
    color: white;
    padding: 14px;
    border-radius: 999px;
    font-weight: 700;
    font-size: 16px;
}

.pay-trigger-btn:disabled{
    opacity: .5;
}

.category-list{
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    flex: 1 1 auto;
    min-width: 0;
}

.category-btn{
    background: white;
    border: 1.5px solid #F97316;
    color: #F97316;
    padding: 8px 18px;
    border-radius: 999px;
    text-decoration: none;
    font-weight: 600;
    white-space: nowrap;
}

.category-btn:hover{
    background: #fff2e8;
    color: #F97316;
}

.category-btn.active{
    background: #F97316;
    color: white;
}

.search-form{
    flex-shrink: 0;
}

.search-box{
    border-radius: 999px;
    border: 1px solid #999;
    padding: 10px 20px;
    width: 250px;
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
    overflow-y: auto;
}

.payment-right{
    width: 60%;
    padding: 40px;
    overflow-y: auto;
}

.payment-total{
    background: #F97316;
    color: white;
    border-radius: 15px;
    padding: 20px;
    text-align: center;
    margin-bottom: 30px;
}

.payment-grid{
    display: grid;
    grid-template-columns: repeat(3,1fr);
    gap: 16px;
    margin-bottom: 30px;
}

.payment-method{
    background: white;
    border: none;
    border-radius: 15px;
    padding: 28px 16px;
    font-weight: bold;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.payment-method.active{
    background: #F97316 !important;
    color: white !important;
    border: 3px solid #ea580c !important;
    transform: scale(1.03);
}

.payment-method.active .brand-label{
    color: white !important;
}

.payment-breakdown{
    background: white;
    border-radius: 15px;
    padding: 20px 24px;
    margin-bottom: 24px;
}

.payment-breakdown > div{
    padding: 6px 0;
    color: #555;
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

</style>
@endsection
