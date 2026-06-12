@extends('layouts.app')

@section('content')

<div class="transactions-page">

    <!-- HEADER -->
    <div class="transactions-header">

        <div>
            <h2 class="fw-bold">
                Transaction History
            </h2>

           
        </div>

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
            <div class="col-md-3 d-flex align-items-end">

                <button class="btn btn-warning w-100">

                    Filter

                </button>

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

                    <td>
                        {{ $transaction->user->name ?? '-' }}
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

                        <button class="view-btn"
                                data-bs-toggle="modal"
                                data-bs-target="#transactionModal{{ $transaction->id }}">

                            View

                        </button>

                    </td>

                </tr>

                <!-- MODAL -->
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

                                @foreach($transaction->items as $item)

                                <div class="d-flex justify-content-between mb-3">

                                    <div>
                                        <strong>
                                            {{ $item->product->name ?? 'Deleted Product' }}
                                        </strong>

                                        <br>

                                        {{ $item->quantity }}x
                                    </div>

                                    <div>
                                        Rp {{ number_format($item->price * $item->quantity) }}
                                    </div>

                                </div>

                                @endforeach

                                <hr>

                                <div class="d-flex justify-content-between">

                                    <strong>Total</strong>

                                    <strong>
                                        Rp {{ number_format($transaction->total) }}
                                    </strong>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                @empty

                <tr>

                    <td colspan="7" class="text-center py-5 text-muted">
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

</style>

@endsection