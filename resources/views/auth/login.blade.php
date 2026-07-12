<x-guest-layout>

<div class="container-fluid vh-100">
    <div class="row h-100">

        <!-- LEFT SIDE -->
        <div class="col-md-5 d-flex align-items-center justify-content-center"
             style="background: #FFFBEB;">

            <div style="width: 70%;">

                <h3 class="mb-5 text-center fw-light">
                    Login
                </h3>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- EMAIL -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold">
                            Email
                        </label>

                        <input type="email"
                            name="email"
                            placeholder="example@gmail.com"
                            class="form-control border-dark rounded-0 {{ $errors->has('email') ? 'is-invalid' : '' }}"
                            value="{{ old('email') }}"
                            required>

                        
                    </div>

                    <!-- PASSWORD -->
                    <div class="mb-4">
                        <label class="form-label small fw-bold">
                            Password
                        </label>

                        <input type="password"
                            name="password"
                            class="form-control border-dark rounded-0 {{ $errors->has('password') ? 'is-invalid' : '' }}"
                            placeholder="Minimum 8 characters"
                            required>

                         
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger py-2 mb-3 rounded-0" role="alert">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <!-- LOGIN BUTTON -->
                    <button style="background: #F97316;" class="btn w-100 rounded-2">
                        Login
                    </button>
                    

                </form>

                <!-- DIVIDER -->
                <div class="d-flex align-items-center my-4">
                    <hr class="flex-grow-1">
                    <span class="px-3 text-muted small">OR</span>
                    <hr class="flex-grow-1">
                </div>

                <!-- GOOGLE LOGIN -->
                <a href="{{ route('auth.google') }}"
                   class="btn btn-outline-dark w-100 rounded-2 d-flex align-items-center justify-content-center gap-2">

                    <img src="https://www.google.com/favicon.ico" alt="" width="16" height="16">
                    Login with Google

                </a>

                <!-- LINKS -->
                <div class="text-center mt-4">

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                           class="text-dark">
                            Forgot Your Password
                        </a>
                    @endif

                </div>

            </div>
        </div>

        <!-- RIGHT SIDE -->
        <div class="col-md-7 position-relative p-0">

            <div style="
                background-image: url('https://images.unsplash.com/photo-1521017432531-fbd92d768814?q=80&w=1200');
                background-size: cover;
                background-position: center;
                height: 100%;
                width: 100%;
                position: relative;
            ">

                <!-- OVERLAY -->
                <div style="
                    background: rgba(0,0,0,0.25);
                    width: 100%;
                    height: 100%;
                    position: absolute;
                    top: 0;
                    left: 0;
                "></div>

                <!-- TEXT -->
                <div class="position-absolute top-50 start-50 translate-middle text-white text-center">
                    <h1 class="fw-light">
                        Welcome
                    </h1>
                </div>

                <!-- LOGO -->
                <div class="position-absolute bottom-0 end-0 p-4 text-white">
                    POS System
                </div>

            </div>

        </div>

    </div>
</div>

</x-guest-layout>