@extends('layouts.app')

@section('content')

<div class="container">
<div class="d-flex justify-content-between align-items-center mb-3">


<div class="row ">


    <div class ="admin-dashboard">
        
<form method="GET" action="{{ route('admin.dashboard') }}" class="row mb-4">

    <div class="col-md-4">
        <label>Start Date</label>
        <input type="date"
               name="start_date"
               class="form-control"
               value="{{ $startDate }}">
    </div>

    <div class="col-md-4">
        <label>End Date</label>
        <input type="date"
               name="end_date"
               class="form-control"
               value="{{ $endDate }}">
    </div>

    <div class="col-md-4 d-flex align-items-end">
        <button class="btn btn-warning w-100">
            Filter
        </button>
    </div>

</form>

        <h2 class="fw-bold">
            Sales Summary
        </h2>

    </div>

    <!-- TOP SUMMARY -->
    <div class="summary-grid">
            
        <div class="summary-card">

            <div class="summary-title">
                Total Income
            </div>

            <div class="summary-value">
                Rp {{ number_format($totalSales) }}
            </div>

        </div>

        <div class="summary-card">

            <div class="summary-title">
                Total Item Sold
            </div>

            <div class="summary-value">
                {{ $totalTransactions }}
            </div>

        </div>

        <div class="summary-card">

            <div class="summary-title">
                Average Transaction Value
            </div>

            <div class="summary-value">
                Rp {{ number_format($totalTransactions > 0 ? $totalSales / $totalTransactions : 0) }}
            </div>
            <div>
        
        </div>
         
    
        
    </div>
    <div class="summary-card">
            <div class="summary-title">
            Today's Profit
            </div>
            <div class="summary-value">
            Rp {{ number_format($todayProfit) }}
            </div>
            <small class="
            {{ $percentChange >= 0 ? 'text-success' : 'text-danger' }}
        ">
            Yesterday: Rp {{ number_format($yesterdayProfit) }}

            (
            {{ $percentChange >= 0 ? '+' : '' }}
            {{ number_format($percentChange, 1) }}%
            )
        </small>
        </div>
    </div>
    
    <div class="dashboard-grid">
        <div class="dashboard-card">

        <div class="card-header-custom">

        <h4 class="fw-bold" >Products</h4>

        <a href="{{ route('products') }}"
           class="view-btn">

            View

        </a>

        </div>

        <h5 class="mb-4">
        Top Selling Products
        </h5>

        <div class="top-products-grid">

        @foreach($topProducts as $item)

        <div class="mini-product-card">

            <img src="{{ $item->product->image_url ?? asset('images/no-image.png') }}">

            <h6 class="mt-2" title="{{ $item->product->name ?? 'Deleted Product' }}">
                {{ \Illuminate\Support\Str::limit($item->product->name ?? 'Deleted Product', 15) }}
            </h6>

            <small class="text-muted">
                Products Sold : {{ $item->total_sold }}
                
            </small>

            @if(($item->product->stock ?? 0) <= 5)

            <div class="low-stock">
                Stock : {{ $item->product->stock ?? 0 }}!!
            </div>

            @endif

        </div>

        @endforeach

        </div>

        </div>

     <div class="dashboard-card">
            <div class="card-header-custom">

                <h4 class="fw-bold" >History</h4>

                <a href="{{ route('transactions') }}"
                   class="view-btn">

                    View

                </a>

            </div>

            <div class="history-list">

                @foreach($recentTransactions->take(5) as $transaction)

                <div class="history-item">

                    <div>
                        <strong>
                            Transaction #{{ $transaction->id }}
                        </strong>

                        <div class="small text-muted">
                            {{ $transaction->created_at->format('d M Y') }}
                        </div>
                    </div>

                    <div class="fw-bold text-success">
                        Rp {{ number_format($transaction->total) }}
                    </div>

                </div>

                @endforeach

            </div>

        </div>

        <div class="dashboard-card">

    <div class="card-header-custom">

        <h4 class="fw-bold">Analytics</h4>

        <a href="{{ route('admin.analytics') }}"
           class="view-btn">

            View

        </a>

    </div>

    

    <!-- MINI GRAPH -->
    <div style="height:220px;">
        <canvas id="profitChart"></canvas>
    </div>

</div>

<div class="dashboard-card">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <h5 class="fw-bold">
            Account Management
        </h5>

        <a href="{{ route('admin.accounts') }}"
           class="view-btn">

            View
        </a>

    </div>

    @foreach($newestAccounts as $user)

    <div class="d-flex justify-content-between align-items-center mb-3">

        <div>

            <div class="fw-bold" title="{{ $user->name }}">
                {{ \Illuminate\Support\Str::limit($user->name, 15) }}
            </div>

            <small class="text-muted">
                {{ $user->role }}
            </small>

        </div>

        <div>

            @if($user->is_online)

                <span class="badge bg-success">
                    Online
                </span>

            @else

                <span class="badge bg-secondary">
                    Offline
                </span>

            @endif

        </div>

    </div>

    @endforeach

</div>
    </div> <!-- end -->

    
    
</div>



</div>


<!-- <h6>Sales Report</h6>
<form action="{{ route('admin.toggle.report') }}" method="POST" class="mb-3">
    @csrf -->

    <!-- <div class="mb-2">
        <input type="email" 
               name="email" 
               class="form-control" 
               placeholder="Enter report email"
               value="{{ optional(\App\Models\Setting::first())->report_email }}"
               required>
    </div> -->

    <!-- <div class="form-check form-switch mb-2">
        <input 
            class="form-check-input" 
            type="checkbox" 
            name="report_enabled"
            id="reportToggle"
            {{ optional(\App\Models\Setting::first())->report_enabled ? 'checked' : '' }}
        >
        <label class="form-check-label" for="reportToggle">
            Enable Daily Report
        </label>
    </div>
    
    <button class="btn btn-primary">
        Save Settings
    </button>
    <form action="{{ route('admin.send.report.now') }}" method="POST" class="mt-2">
    @csrf
    <button class="btn btn-success">
        📧 Send Report Now
    </button>
</form>
</form>

</div> -->

<style>

.admin-dashboard{
    padding: 10px;
}

/* SUMMARY */
.summary-grid{
    display: grid;
    grid-template-columns: repeat(3,1fr);
    gap: 20px;
    margin-bottom: 35px;
}

.summary-card{
    background: white;
    border-radius: 18px;
    padding: 30px;
    text-align: center;
    border: 1px solid #ddd;
}

.summary-title{
    color: #666;
    margin-bottom: 15px;
}

.summary-value{
    font-size: 32px;
    font-weight: bold;
}

/* GRID */
.dashboard-grid{
    display: grid;
    grid-template-columns: repeat(2,1fr);
    gap: 25px;
}

.dashboard-card{
    background: white;
    border-radius: 20px;
    padding: 25px;
    border: 1px solid #ddd;
}

/* HEADER */
.card-header-custom{
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.view-btn{
    background: #F97316;
    color: white;
    padding: 8px 18px;
    border-radius: 10px;
    text-decoration: none;
}

/* PRODUCTS */
.top-products-grid{
    display: flex;
    gap: 15px;
}

.mini-product-card{
    width: 100px;
}

.mini-product-card img{
    width: 100px;
    height: 70px;
    object-fit: cover;
    border-radius: 12px;
}

.mini-product-card h6{
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.low-stock{
    color: red;
    font-weight: bold;
    margin-top: 5px;
}

/* USERS */
.user-row{
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.user-left{
    display: flex;
    align-items: center;
    gap: 15px;
}

.user-avatar{
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background: #eee;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* HISTORY */
.history-item{
    display: flex;
    justify-content: space-between;
    margin-bottom: 15px;
}

/* ANALYTICS */
.analytics-box{
    text-align: center;
    padding: 30px;
}

</style>
@endsection
@section('scripts')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@section('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {

    const canvas = document.getElementById('profitChart');

    if (!canvas) {
        console.log('Canvas not found');
        return;
    }

    const ctx = canvas.getContext('2d');

    new Chart(ctx, {

        type: 'line',

        data: {
            labels: @json($labels),

            datasets: [{
                label: 'Sales',
                
                data: @json($data),

                borderColor: '#F97316',
                backgroundColor: 'rgba(249,115,22,0.15)',

                fill: true,
                tension: 0.4,
                pointRadius: 4
            }]
        },

        options: {
            responsive: true,
            maintainAspectRatio: false,

            plugins: {
                legend: {
                    display: false
                }
            },

            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },

                y: {
                    grid: {
                        color: '#eee'
                    }
                }
            }
        }

    });

});

</script>

@endsection

@endsection