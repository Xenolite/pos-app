<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ config('app.name') }}</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>

        body{
            background: #efefdd;
            margin: 0;
        }

        /* TOPBAR */
        .topbar{
            height: 80px;
            background: #F97316;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 40px;
        }

        .menu-center{
            display: flex;
            gap: 60px;
        }

        .menu-item{
            text-decoration: none;
            color: black;
            text-align: center;
            font-size: 14px;
        }

        .menu-item:hover{
            color: black;
        }

        .menu-item i{
            display: block;
            font-size: 24px;
            margin-bottom: 5px;
        }

        .user-section{
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .main-content{
            padding: 30px;
        }

    </style>
</head>

<body class="{{ auth()->check() && auth()->user()->dark_mode ? 'dark-mode' : '' }}">

    <!-- NAVBAR -->
    <div class="topbar">

        <!-- LOGO -->
        <div class="fw-bold">
            POS System
        </div>

        <!-- MENU -->
        <div class="menu-center">

            <a href="{{ route('dashboard') }}" class="menu-item {{ request()->routeIs('dashboard') ? 'active-menu' : '' }}">
                
                <span>POS</span>
            </a>

            

            <a href="{{ route('products') }}"
   class="menu-item {{ request()->routeIs('products') ? 'active-menu' : '' }}">

    Product
</a>

            <a href="{{ route('transactions') }}"
   class="menu-item {{ request()->routeIs('transactions') ? 'active-menu' : '' }}">

     Transactions
</a>

            @if(auth()->user()->role === 'admin')
            <a href="{{ route('admin.dashboard') }}"  class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active-menu' : '' }}">
                
                <span>Admin</span>
            </a>
            @endif

        </div>

        <!-- USER -->
        <div class="user-section">

            <div>
                <a href="{{ route('profile.page') }}">
                👤 {{ auth()->user()->name }}
            </a>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button class="btn btn-sm btn-dark rounded-pill">
                    Logout
                </button>
            </form>

        </div>

    </div>

    <!-- PAGE CONTENT -->
    <div class="main-content">
        @yield('content')
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Midtrans Snap.js (sandbox). Ganti ke https://app.midtrans.com/snap/snap.js untuk production. -->
<script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('services.midtrans.client_key') }}"></script>

@yield('scripts')
     
</body>

</html>

<style>
    .menu-item{
    text-decoration: none;
    color: white;
    position: relative;
    padding-bottom: 8px;
    transition: .2s;
    font-weight: 500;
}

.menu-item:hover{
    color: white;
}

.active-menu{
    font-weight: bold;
}

.active-menu::after{
    content: '';
    position: absolute;
    left: 0;
    bottom: 0;
    width: 100%;
    height: 3px;
    background: white;
    border-radius: 999px;
}


.dark-mode{
    background: #383733;
    color: #ffffff;
}

.dark-mode body{
    background: #383733;
}

.dark-mode .main-content{
    background: #383733;
}

.dark-mode .dashboard-card{
    background: #595959;
    border-radius: 20px;
    padding: 25px;
    border: 1px solid #ddd;
}
.dark-mode .summary-card{
    background: #595959;
    border-radius: 18px;
    padding: 30px;
    text-align: center;
    border: 1px solid #ddd;
}
.dark-mode .summary-title{
    color: #ffffff;
    margin-bottom: 15px;
}
.dark-mode .transactions-table th{
    padding: 18px;
    border-bottom: 2px solid #eee;
    color: #ffffff;
    font-weight: 600;
}
.dark-mode .transactions-card{
    background: #595959;
    border-radius: 24px;
    padding: 25px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.06);
    overflow-x: auto;
}
.dark-mode .transactions-table td{
    padding: 18px;
    border-bottom: 1px solid #f3f3f3;
}
.dark-mode .account-card{
    background: #595959;
    border-radius: 24px;
    padding: 25px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.06);
    overflow-x: auto;
}

.dark-mode .account-table{
    width: 100%;
    border-collapse: collapse;
}

.dark-mode .account-table th{
    padding: 18px;
    border-bottom: 2px solid #eee;
    color: #ffffff;
    font-weight: 600;
}

.dark-mode .account-table td{
    padding: 18px;
    border-bottom: 1px solid #f3f3f3;
}
.dark-mode .profile-card,
.dark-mode .analytics-card,
.dark-mode .product-card,
.dark-mode .card,
.dark-mode .categories-sidebar,
.dark-mode .modal-content,
.dark-mode .table-container{
    background: #595959;
    color: white;
    border: 1px solid #6b6b6b;
}

.dark-mode .text-muted{
    color: #d1d5db !important;
}

.dark-mode .form-control,
.dark-mode .form-select{
    background: #6a6a6a;
    border: 1px solid #7a7a7a;
    color: white;
}

.dark-mode .form-control:focus,
.dark-mode .form-select:focus{
    background: #6a6a6a;
    color: white;
    border-color: #F97316;
    box-shadow: none;
}
.dark-mode .category-item{
    display: block;
    padding: 14px 18px;
    margin-bottom: 12px;
    border-radius: 14px;
    text-decoration: none;
    color: white;
    transition: .2s;
    font-weight: 500;
}

.dark-mode table{
    color: white;
}

.dark-mode .table{
    color: white;
}

.dark-mode .table th{
    background: #4d4d4d;
}

.dark-mode .table td{
    border-color: #6b6b6b;
}

.dark-mode .modal-header,
.dark-mode .modal-footer{
    border-color: #6b6b6b;
}

.dark-mode .menu-item{
    color: white;
}

.dark-mode .menu-item:hover{
    color: black;
}

.dark-mode .active-menu::after{
    background: #F97316;
}
.dark-mode .payment-method{
    color:black;
    background: white;
    border: none;
    border-radius: 15px;
    padding: 40px 20px;
    font-weight: bold;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
</style>