@extends('layouts.app')

@section('content')

<div class="summary-page">

    <div class="summary-single-card">

        <!-- HEADER -->
        <div class="summary-header">

            <div>
                <h2 class="fw-bold">
                    Transaction Summary
                </h2>

                <p class="text-muted mb-0">
                    {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}
                    &mdash;
                    {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                </p>
            </div>

        </div>

        <hr class="summary-divider">

        <!-- FILTER JANGKA WAKTU -->
        <form method="GET" action="{{ route('transactions.summary') }}" id="periodForm">

            <!-- SATU-SATUNYA field 'period' yang dikirim; nilainya diatur lewat JS -->
            <input type="hidden" name="period" id="periodInput" value="{{ $period }}">

            <div class="row g-3 align-items-end">

                <!-- QUICK PERIOD -->
                <div class="col-md-6">

                    <label class="form-label">
                        Period
                    </label>

                    <div class="period-tabs">

                        @foreach(['today' => 'Today', 'week' => 'This Week', 'month' => 'This Month', 'year' => 'This Year'] as $value => $label)

                        <button type="button"
                                class="period-btn {{ $period == $value ? 'active' : '' }}"
                                onclick="document.getElementById('periodInput').value='{{ $value }}'; document.getElementById('periodForm').submit();">
                            {{ $label }}
                        </button>

                        @endforeach

                    </div>

                </div>

                <!-- START DATE -->
                <div class="col-md-2">

                    <label class="form-label">
                        Start Date
                    </label>

                    <input type="date"
                           name="start_date"
                           class="form-control"
                           value="{{ $startDate }}"
                           onchange="document.getElementById('periodInput').value='custom';">

                </div>

                <!-- END DATE -->
                <div class="col-md-2">

                    <label class="form-label">
                        End Date
                    </label>

                    <input type="date"
                           name="end_date"
                           class="form-control"
                           value="{{ $endDate }}"
                           onchange="document.getElementById('periodInput').value='custom';">

                </div>

                <!-- BUTTON -->
                <div class="col-md-2">

                    <button type="submit"
                            class="btn btn-warning w-100"
                            onclick="document.getElementById('periodInput').value='custom';">
                        Filter
                    </button>

                </div>

            </div>

        </form>

        <hr class="summary-divider">

        <!-- TOP SUMMARY -->
        <div class="summary-grid">

            <div class="summary-metric">
                <div class="summary-title">Total Sales</div>
                <div class="summary-value">Rp {{ number_format($totalSales) }}</div>
            </div>

            <div class="summary-metric">
                <div class="summary-title">Total Transaction</div>
                <div class="summary-value">{{ number_format($totalTransactions) }}</div>
            </div>

            <div class="summary-metric">
                <div class="summary-title">Total Profit</div>
                <div class="summary-value text-primary">Rp {{ number_format($totalProfit) }}</div>
            </div>

            <div class="summary-metric">
                <div class="summary-title">Average / Transaksi</div>
                <div class="summary-value">Rp {{ number_format($averageTransaction) }}</div>
            </div>

            <div class="summary-metric">
                <div class="summary-title">Total Service Charge</div>
                <div class="summary-value">Rp {{ number_format($totalServiceCharge) }}</div>
            </div>

        </div>

        <hr class="summary-divider">

        <!-- PAYMENT METHOD BREAKDOWN -->
        <h5 class="fw-bold mb-3">
            Payment Method Breakdown
        </h5>

        <div class="table-responsive mb-4">

            <table class="detail-table">

                <thead>
                    <tr>
                        <th>Method</th>
                        <th class="text-center">Amount</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($paymentBreakdown as $row)

                    <tr>
                        <td>{{ $row->payment_method }}</td>
                        <td class="text-center">{{ $row->total_count }}</td>
                        <td class="text-end">Rp {{ number_format($row->total_amount) }}</td>
                    </tr>

                    @empty

                    <tr>
                        <td colspan="3" class="text-center text-muted py-3">
                            No Data
                        </td>
                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        <hr class="summary-divider">

        <!-- TOP PRODUCTS -->
        <h5 class="fw-bold mb-3">
            Top Products
        </h5>

        <div class="table-responsive">

            <table class="detail-table">

                <thead>
                    <tr>
                        <th>Product</th>
                        <th class="text-center">Qty Sold</th>
                        <th class="text-end">Total Sold</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($topProducts as $item)

                    <tr>
                        <td title="{{ $item->product->name ?? 'Deleted Product' }}">
                            {{ \Illuminate\Support\Str::limit($item->product->name ?? 'Deleted Product', 30) }}
                        </td>
                        <td class="text-center">{{ $item->total_sold }}</td>
                        <td class="text-end">Rp {{ number_format($item->total_revenue) }}</td>
                    </tr>

                    @empty

                    <tr>
                        <td colspan="3" class="text-center py-4 text-muted">
                            Tidak ada data penjualan pada periode ini
                        </td>
                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

<style>

.summary-page{
    padding: 10px;
    
}

.summary-single-card{
    background: white;
    border-radius: 20px;
    padding: 30px;
    border: 1px solid #ddd;
    box-shadow: 0 10px 30px rgba(0,0,0,0.06);
}

.summary-header{
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.summary-divider{
    border: none;
    border-top: 1px solid #eee;
    margin: 22px 0;
}

.period-tabs{
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.period-btn{
    border: 1px solid #ddd;
    background: #f8f9fa;
    color: #555;
    padding: 8px 16px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 14px;
}

.period-btn:hover{
    background: #eee;
}

.period-btn.active{
    background: #F97316;
    border-color: #F97316;
    color: white;
}

.summary-grid{
    display: grid;
    grid-template-columns: repeat(5,1fr);
    gap: 20px;
    
}

.summary-metric{
    text-align: center;
    border: 1px solid #eee;
    border-radius: 14px;
    padding: 18px 10px;
    
}

.summary-title{
    color:black;
    margin-bottom: 10px;
    font-size: 13px;
}

.summary-value{
    color:black;
    font-size: 22px;
    font-weight: bold;
}


.detail-table{
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}

.detail-table thead th{
    text-align: left;
    color: #666;
    font-weight: 600;
    padding-bottom: 8px;
    border-bottom: 1px solid #ddd;
}

.detail-table tbody td{
    padding: 10px 0;
    border-bottom: 1px solid #f0f0f0;
}

@media (max-width: 900px){
    .summary-grid{
        grid-template-columns: repeat(2,1fr);
    }
}

</style>

@endsection
