@extends('layouts.auth')

@section('content')
<div class="container-fluid p-0">
    <div class="row g-0 login-container">
        <!-- Left Side: Brand & Visuals -->
        <div class="col-lg-6 login-sidebar">
            <div class="brand-icon">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
            <h1 class="brand-title">StockSync</h1>
            <p class="brand-tagline">Streamline your inventory, optimize your sales, and take full control of your business with our advanced management system.</p>
        </div>

        <!-- Right Side: Login Form -->
        <div class="col-lg-6 login-form-section bg-white">
            <div class="row justify-content-center w-100">
                <div class="col-md-10 col-lg-8">
                    <div class="form-header text-center">
                        <h2>Welcome</h2>
                        <p class="text-muted">Please enter your details to sign in.</p>
                    </div>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <!-- Email Input -->
                        <div class="form-floating mb-3">
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="name@example.com">
                            <label for="email" class="text-muted">Email Address</label>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <!-- Password Input -->
                        <div class="form-floating mb-4">
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Password">
                            <label for="password" class="text-muted">Password</label>
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary-custom shadow-sm">
                            Sign In
                        </button>

                        <div class="text-center mt-4">
                            <p class="text-muted small mb-0">Don't have an account? 
                                <a href="{{ route('register') }}" class="text-primary fw-bold text-decoration-none">Create an account</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
