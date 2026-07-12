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


<div class="accounts-page">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h3 class="fw-bold">
            Account Management
        </h3>

        <button class="btn btn-warning"
                data-bs-toggle="modal"
                data-bs-target="#createAccountModal">

            + Create Account

        </button>

    </div>

    <div class="card border-0 shadow-sm rounded-4">

        <div class="account-card">

            <table class="account-table">

                <thead >
                    <tr>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Email</th>
                        <th>Last Login</th>
                        <th>Last Logout</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach($users as $user)

                    <tr>

                        <td>{{ $user->name }}</td>

                        <td>
                            {{ ucfirst($user->role) }}
                        </td>

                        <td>
                            {{ $user->email }}
                            @if($user->google_id)
                            <span class="badge bg-light text-dark border ms-1" style="font-weight: 500;">
                                Google
                            </span>
                            @endif
                        </td>

                        <td>
                            {{ $user->last_login_at ?? '-' }}
                        </td>
                        <td>
                            {{$user->last_logout_at ?? '-'}}
                        </td>

                        <td class="d-flex gap-2">

                            <button class="btn btn-sm btn-outline-dark"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editAccountModal{{ $user->id }}">
                                Edit
                            </button>

                            @if($user->id !== auth()->id())
                            <form action="{{ route('admin.accounts.destroy', $user) }}"
                                  method="POST"
                                  onsubmit="return confirm('Delete this account? This cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    Delete
                                </button>
                            </form>
                            @endif

                        </td>

                    </tr>

                    <!-- EDIT ACCOUNT MODAL -->
                    <div class="modal fade" id="editAccountModal{{ $user->id }}">

                        <div class="modal-dialog">

                            <form action="{{ route('admin.accounts.update', $user) }}"
                                  method="POST">

                                @csrf
                                @method('PUT')

                                <div class="modal-content">

                                    <div class="modal-header">
                                        <h5>Edit Account</h5>
                                    </div>

                                    <div class="modal-body">

                                        <input type="text"
                                               name="name"
                                               class="form-control mb-3"
                                               placeholder="Name"
                                               value="{{ $user->name }}">

                                        <input type="email"
                                               name="email"
                                               class="form-control mb-1"
                                               placeholder="Email"
                                               value="{{ $user->email }}"
                                               @if($user->google_id) disabled @endif>

                                        @if($user->google_id)
                                        <div class="form-text text-muted mb-2">
                                            This account is logged in via Google - Email cannot be changed                               
                                        </div>
                                        @endif

                                        <input type="text"
                                               name="phone"
                                               class="form-control mb-3"
                                               placeholder="Phone"
                                               value="{{ $user->phone }}">

                                        <input type="password"
                                               name="password"
                                               class="form-control mb-1"
                                               placeholder="New Password (kosongkan jika tidak diubah)"
                                               @if($user->google_id) disabled @endif>

                                        @if($user->google_id)
                                        <div class="form-text text-muted mb-2">
                                            This account is logged in via Google - Password cannot be changed                                           
                                        </div>
                                        @endif

                                        <select name="role"
                                                class="form-control">

                                            <option value="cashier" {{ $user->role == 'cashier' ? 'selected' : '' }}>
                                                Cashier
                                            </option>

                                            <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>
                                                Admin
                                            </option>

                                        </select>

                                    </div>

                                    <div class="modal-footer">

                                        <button type="button"
                                                class="btn btn-secondary"
                                                data-bs-dismiss="modal">
                                            Cancel
                                        </button>

                                        <button class="btn btn-warning">
                                            Save
                                        </button>

                                    </div>

                                </div>

                            </form>

                        </div>

                    </div>

                    @endforeach

                </tbody>

            </table>

            {{ $users->links() }}

        </div>

    </div>

</div>

@include('admin.create-account')
<style>
.account-card{
    background: white;
    border-radius: 24px;
    padding: 25px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.06);
    overflow-x: auto;
}

.account-table{
    width: 100%;
    border-collapse: collapse;
}

.account-table th{
    padding: 18px;
    border-bottom: 2px solid #eee;
    color: #888;
    font-weight: 600;
}

.account-table td{
    padding: 18px;
    border-bottom: 1px solid #f3f3f3;
}
</style>
@endsection