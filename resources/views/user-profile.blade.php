@extends('layouts.app')

@section('content')
@if(session('error'))
<div class="alert alert-secondary alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3"
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

<div class="profile-page">

    <div class="profile-card">

    <!-- TOP BAR -->
    <div class="d-flex justify-content-end mb-2">

        <form action="{{ route('profile.darkmode') }}"
              method="POST">

            @csrf

            <button type="submit" class="theme-toggle">

                @if(auth()->user()->dark_mode)
                    ☀️ Light Mode
                @else
                    🌙 Dark Mode
                @endif

            </button>

        </form>

    </div>

    <!-- PROFILE -->
    <div class="text-center">

        <img src="https://ui-avatars.com/api/?name={{ auth()->user()->name }}"
             class="profile-image">

        <h3 class="mt-3 profile-name" title="{{ auth()->user()->name }}">
            {{ \Illuminate\Support\Str::limit(auth()->user()->name, 20) }}
        </h3>

        <div class="text-muted">
            {{ auth()->user()->role }}
        </div>

    </div>

    <hr>

    <!-- INFO -->
    <div class="profile-info">

        <div class="info-row">
            <strong>Email</strong>
            <span>{{ auth()->user()->email }}</span>
        </div>

        <div class="info-row">
            <strong>Phone</strong>
            <span>{{ auth()->user()->phone ?? '-' }}</span>
        </div>

        <div class="info-row">
            <strong>Role</strong>
            <span>{{ auth()->user()->role }}</span>
        </div>

    </div>

    <!-- BUTTONS -->
    <div class="mt-4 d-flex gap-3">

        <button type="button"
                class="profile-btn"
                data-bs-toggle="modal"
                data-bs-target="#editProfileModal">

            Edit Profile

        </button>

        <button type="button"
                class="password-btn"
                data-bs-toggle="modal"
                data-bs-target="#passwordModal">

            Change Password

        </button>

    </div>

</div>

</div>

<!-- EDIT PROFILE MODAL -->
<div class="modal fade" id="editProfileModal">

    <div class="modal-dialog">

        <form action="{{ route('profile.update') }}"
              method="POST">

            @csrf

            <div class="modal-content">

                <div class="modal-header">
                    <h5>Edit Profile</h5>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label>Name</label>

                        <input type="text"
                               name="name"
                               class="form-control"
                               value="{{ auth()->user()->name }}">
                    </div>

                    <div class="mb-3">
                        <label>Email</label>

                        <input type="email"
                               name="email"
                               class="form-control"
                               value="{{ auth()->user()->email }}">
                    </div>

                    <div class="mb-3">
                        <label>Phone</label>

                        <input type="text"
                               name="phone"
                               class="form-control"
                               value="{{ auth()->user()->phone }}">
                    </div>

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

<!-- PASSWORD MODAL -->
<div class="modal fade" id="passwordModal">

    <div class="modal-dialog">

        <form action="{{ route('profile.password') }}"
              method="POST">

            @csrf

            <div class="modal-content">

                <div class="modal-header">
                    <h5>Change Password</h5>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label>Current Password</label>

                        <input type="password"
                               name="current_password"
                               class="form-control">
                    </div>

                    <div class="mb-3">
                        <label>New Password</label>

                        <input type="password"
                               name="password"
                               class="form-control"
                               oninput="validatePasswordLength()">
                               
                    </div>

                    <div class="mb-3">
                        <label>Confirm Password</label>

                        <input type="password"
                               name="password_confirmation"
                               class="form-control">
                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">

                        Cancel

                    </button>

                    <button class="btn btn-warning">
                        Update Password
                    </button>

                </div>

            </div>

        </form>

    </div>

</div>

<style>

.profile-page{
    display: flex;
    justify-content: center;
    padding-top: 40px;
}

.profile-card{
    width: 500px;
    background: white;
    border-radius: 25px;
    padding: 35px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
}

.profile-image{
    width: 120px;
    height: 120px;
    border-radius: 50%;
}

.profile-name{
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 100%;
}

.info-row{
    display: flex;
    justify-content: space-between;
    margin-bottom: 15px;
}

.profile-btn{
    flex: 1;
    border: none;
    background: #F97316;
    color: white;
    padding: 12px;
    border-radius: 12px;
}

.password-btn{
    flex: 1;
    border: none;
    background: #111827;
    color: white;
    padding: 12px;
    border-radius: 12px;
}
.theme-toggle{
    border: none;
    background: #F97316;
    color: white;
    padding: 8px 14px;
    border-radius: 999px;
    font-size: 14px;
    font-weight: 600;
    transition: .2s;
}

.theme-toggle:hover{
    opacity: .9;
    transform: translateY(-1px);
}
</style>
<script>
function validatePasswordLength() {
    const input = document.getElementById('newPassword');
    const error = document.getElementById('passwordError');

    if (input.value.length > 0 && input.value.length < 8) {
        input.classList.add('is-invalid');
        error.style.display = 'block';
    } else {
        input.classList.remove('is-invalid');
        error.style.display = 'none';
    }
}
</script>
@endsection