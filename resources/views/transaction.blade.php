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

<div class="transactions-page">

    <!-- HEADER -->
    <div class="transactions-header">

        <div>
            <h2 class="fw-bold">
                Transaction History
            </h2>

           
        </div>

        <div class="d-flex align-items-center gap-2">

            <!-- SEARCH -->
            <form method="GET"
                  action="{{ route('transactions') }}"
                  class="search-form">

                <input type="text"
                       name="search"
                       value="{{ $search }}"
                       placeholder="Search transaction ID..."
                       class="search-input">

            </form>

        </div>

    </div>
    <div class="filter-card mb-4">

    <form method="GET"
          action="{{ route('transactions') }}">

        <div class="row g-3">

            <!-- PAYMENT METHOD -->
            <div class="col-md-3">

                <label class="form-label">
                    Payment Method
                </label>

                <select name="payment_method"
                        class="form-control">

                    <option value="">
                        All
                    </option>

                    <option value="Cash"
                        {{ request('payment_method') == 'Cash' ? 'selected' : '' }}>
                        Cash
                    </option>

                    <option value="QRIS"
                        {{ request('payment_method') == 'QRIS' ? 'selected' : '' }}>
                        QRIS
                    </option>

                    <option value="Transfer"
                        {{ request('payment_method') == 'Transfer' ? 'selected' : '' }}>
                        Transfer
                    </option>

                    <option value="GoFood"
                        {{ request('payment_method') == 'GoFood' ? 'selected' : '' }}>
                        GoFood
                    </option>

                    <option value="GrabFood"
                        {{ request('payment_method') == 'GrabFood' ? 'selected' : '' }}>
                        GrabFood
                    </option>

                    <option value="Dana"
                        {{ request('payment_method') == 'Dana' ? 'selected' : '' }}>
                        Dana
                    </option>

                    <option value="OVO"
                        {{ request('payment_method') == 'OVO' ? 'selected' : '' }}>
                        OVO
                    </option>
                    <option value="LinkAja"
                        {{ request('payment_method') == 'LinkAja' ? 'selected' : '' }}>
                        LinkAja
                    </option>
                    <option value="ShoppeFood"
                        {{ request('payment_method') == 'ShoppeFood' ? 'selected' : '' }}>
                        ShoppeFood
                    </option>

                </select>

            </div>

            <!-- START DATE -->
            <div class="col-md-3">

                <label class="form-label">
                    Start Date
                </label>

                <input type="date"
                       name="start_date"
                       class="form-control"
                       value="{{ request('start_date') }}">

            </div>

            <!-- END DATE -->
            <div class="col-md-3">

                <label class="form-label">
                    End Date
                </label>

                <input type="date"
                       name="end_date"
                       class="form-control"
                       value="{{ request('end_date') }}">

            </div>

            <!-- BUTTON -->
            <div class="col-md-3 d-flex align-items-end gap-2">

                <button class="btn btn-warning w-100">

                    Filter

                </button>
                
                <!-- EXPORT EXCEL (ikut filter yang sedang aktif) -->
                <a href="{{ route('transactions.export', request()->query()) }}"
                   class="btn btn-success w-100 text-center">
                    Export Excel
                </a>

            </div>

        </div>

    </form>

</div>
    <!-- TABLE -->
    <div class="transactions-card">

        <table class="transactions-table">

            <thead>

                <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Cashier</th>
                    <th>Total</th>
                    <th>Profit</th>
                    <th>Service</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>

            </thead>

            <tbody>

                @forelse($transactions as $transaction)

                <tr>

                    <td>
                        #{{ $transaction->id }}
                    </td>

                    <td>
                        {{ $transaction->created_at->format('d M Y H:i') }}
                    </td>

                    <td title="{{ $transaction->user->name ?? '-' }}">
                        {{ \Illuminate\Support\Str::limit($transaction->user->name ?? '-', 15) }}
                    </td>

                    <td class="text-success fw-bold">
                        Rp {{ number_format($transaction->total) }}
                    </td>

                    <td class="text-primary fw-bold">
                        Rp {{ number_format($transaction->profit) }}
                    </td>

                    <td>
                        Rp {{ number_format($transaction->service_charge ?? 0) }}
                    </td>
                    <td>
                        {{ $transaction->payment_method }}
                    </td>
                    <td>
                        @php
                            // Hanya ada dua status yang tercatat: "paid" (Berhasil)
                            // dan "failed" (Gagal). Tidak ada lagi "pending"/"expired".
                            $statusLabels = [
                                'paid' => 'Berhasil',
                                'failed' => 'Gagal',
                            ];
                            $statusColors = [
                                'paid' => '#198754',
                                'failed' => '#dc3545',
                            ];
                            $statusLabel = $statusLabels[$transaction->payment_status] ?? ucfirst($transaction->payment_status);
                            $statusColor = $statusColors[$transaction->payment_status] ?? '#6c757d';
                        @endphp
                        <span style="color: {{ $statusColor }}; font-weight: 600;">
                            {{ $statusLabel }}
                        </span>
                    </td>
                    <td>

                        <button class="view-btn"
                                data-bs-toggle="modal"
                                data-bs-target="#transactionModal{{ $transaction->id }}">

                            View

                        </button>

                    </td>

                </tr>

                @empty

                <tr>

                    <td colspan="9" class="text-center py-5 text-muted">
                        No transactions found
                    </td>

                </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    <!-- PAGINATION -->
    <div class="mt-4">
        {{ $transactions->links() }}
    </div>

    <!-- MODALS (dipindahkan ke luar <table>; sebelumnya berada di dalam <tbody> sehingga browser
         memaksa-keluarkan <div> modal dari tabel, membuat tampilan tabel rusak) -->
    @foreach($transactions as $transaction)

    <div class="modal fade"
         id="transactionModal{{ $transaction->id }}"
         tabindex="-1">

        <div class="modal-dialog modal-lg">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title">
                        Transaction #{{ $transaction->id }}
                    </h5>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal">
                    </button>

                </div>

                <div class="modal-body">

                    <!-- INFO -->
                    <div class="transaction-info mb-4">

                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Date</span>
                            <span>{{ $transaction->created_at->format('d M Y, H:i') }}</span>
                        </div>

                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Cashier</span>
                            <span title="{{ $transaction->user->name ?? '-' }}">{{ \Illuminate\Support\Str::limit($transaction->user->name ?? '-', 20) }}</span>
                        </div>

                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Payment Method</span>
                            <span>{{ $transaction->payment_method }}</span>
                        </div>

                    </div>

                    <hr>

                    <!-- ITEMS -->
                    @php
                        $itemsSubtotal = 0;
                    @endphp

                    <table class="detail-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Unit Price</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transaction->items as $item)
                            @php
                                $lineTotal = $item->price * $item->quantity;
                                $itemsSubtotal += $lineTotal;
                            @endphp
                            <tr>
                                <td title="{{ $item->product->name ?? 'Deleted Product' }}">{{ \Illuminate\Support\Str::limit($item->product->name ?? 'Deleted Product', 20) }}</td>
                                <td class="text-center">{{ $item->quantity }}x</td>
                                <td class="text-end">Rp {{ number_format($item->price) }}</td>
                                <td class="text-end">Rp {{ number_format($lineTotal) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <hr>

                    <!-- BREAKDOWN -->
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Subtotal</span>
                        <span>Rp {{ number_format($itemsSubtotal) }}</span>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Service Charge</span>
                        <span>Rp {{ number_format($transaction->service_charge ?? 0) }}</span>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between mb-1">
                        <strong>Total</strong>
                        <strong>Rp {{ number_format($transaction->total) }}</strong>
                    </div>

                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Profit</span>
                        <span class="text-primary fw-bold">
                            Rp {{ number_format($transaction->profit) }}
                        </span>
                    </div>

                </div>

            </div>

        </div>

    </div>

    @endforeach

</div>

<style>

.transactions-page{
    padding: 10px;
}

.transactions-header{
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.search-input{
    border: 1px solid #ddd;
    border-radius: 14px;
    padding: 12px 18px;
    width: 280px;
}

.transactions-card{
    background: white;
    border-radius: 24px;
    padding: 25px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.06);
    overflow-x: auto;
}

.transactions-table{
    width: 100%;
    border-collapse: collapse;
}

.transactions-table th{
    padding: 18px;
    border-bottom: 2px solid #eee;
    color: #888;
    font-weight: 600;
}

.transactions-table td{
    padding: 18px;
    border-bottom: 1px solid #f3f3f3;
}

.view-btn{
    background: #F97316;
    border: none;
    color: white;
    padding: 10px 18px;
    border-radius: 12px;
    font-weight: 600;
}

.view-btn:hover{
    background: #ea580c;
}

.export-btn{
    background: #198754;
    border: none;
    color: white;
    padding: 10px 16px;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.export-btn:hover{
    background: #146c43;
    color: white;
}

.transaction-info{
    font-size: 14px;
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
    padding: 8px 0;
    border-bottom: 1px solid #f0f0f0;
}

</style>

@endsection