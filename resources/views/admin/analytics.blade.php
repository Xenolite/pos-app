@extends('layouts.app')

@section('content')

<div class="analytics-page">

    <!-- HEADER -->
    <div class="analytics-filter mb-4">

    <form method="GET"
          action="{{ route('admin.analytics') }}">

        <div class="row g-3">

            <!-- START -->
            <div class="col-md-4">

                <label class="form-label">
                    Start Date
                </label>

                <input type="date"
                       name="start_date"
                       class="form-control"
                       value="{{ $startDate }}">

            </div>

            <!-- END -->
            <div class="col-md-4">

                <label class="form-label">
                    End Date
                </label>

                <input type="date"
                       name="end_date"
                       class="form-control"
                       value="{{ $endDate }}">

            </div>

            <!-- BUTTON -->
            <div class="col-md-4 d-flex align-items-end">

                <button class="btn btn-warning w-100">

                    Apply Filter

                </button>

            </div>

        </div>

    </form>

</div>

    <!-- STATS -->
    <div class="stats-grid">

        <div class="analytics-card">

            <div class="small text-muted">
                Total Sales
            </div>

            <h2>
                Rp {{ number_format($totalSales) }}
            </h2>

        </div>

        <div class="analytics-card">

            <div class="small text-muted">
                Total Profit
            </div>

            <h2 class="text-success">
                Rp {{ number_format($totalProfit) }}
            </h2>

        </div>

        <div class="analytics-card">

            <div class="small text-muted">
                Transactions
            </div>

            <h2>
    {{ $totalTransactions }}
</h2>

        </div>

    </div>

    <!-- CHART -->
    <div class="analytics-card mt-4">

        <h4 class="mb-4">
            Sales Analytics
        </h4>

        <div style="height:400px;">
            <canvas id="analyticsChart"></canvas>
        </div>

    </div>

</div>

<style>

.analytics-page{
    padding: 10px;
}

.stats-grid{
    display: grid;
    grid-template-columns: repeat(3,1fr);
    gap: 20px;
}

.analytics-card{
    background: white;
    border-radius: 20px;
    padding: 25px;
    border: 1px solid #eee;
}

</style>

@endsection

@section('scripts')

<script>

document.addEventListener('DOMContentLoaded', function(){

    const canvas = document.getElementById('analyticsChart');

    if(!canvas) return;

    const ctx = canvas.getContext('2d');

    new Chart(ctx, {

        type: 'line',

        data: {

            labels: @json($labels),

            datasets: [

                {
                    label: 'Sales',

                    data: @json($salesData),

                    borderColor: '#F97316',

                    backgroundColor: 'rgba(249,115,22,0.15)',

                    fill: true,

                    tension: 0.4
                },

                {
                    label: 'Profit',

                    data: @json($profitData),

                    borderColor: '#22c55e',

                    backgroundColor: 'rgba(34,197,94,0.15)',

                    fill: true,

                    tension: 0.4
                }

            ]
        },

        options: {
            responsive: true,
            maintainAspectRatio: false,

            interaction: {
                intersect: false,
                mode: 'index'
            }
        }

    });

});

</script>

@endsection