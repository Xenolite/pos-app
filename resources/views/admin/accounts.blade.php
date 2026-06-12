@extends('layouts.app')

@section('content')

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
                        
                    </tr>
                </thead>

                <tbody>

                    @foreach($users as $user)

                    <tr>

                        <td>{{ $user->name }}</td>

                        <td>
                            {{ ucfirst($user->role) }}
                        </td>

                        <td>{{ $user->email }}</td>

                        <td>
                            {{ $user->last_login_at ?? '-' }}
                        </td>
                        <td>
                            {{$user->last_logout_at ?? '-'}}
                        </td>
                       

                        

                    </tr>

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