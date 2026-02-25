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
            <p class="brand-tagline">Join us today! Create your account to start managing your inventory efficiently.</p>
        </div>

        <!-- Right Side: Register Form -->
        <div class="col-lg-6 login-form-section bg-white">
            <div class="row justify-content-center w-100">
                <div class="col-md-10 col-lg-8">
                    <div class="form-header">
                        <h2>Create Account</h2>
                        <p class="text-muted">Enter your details to register.</p>
                    </div>

                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <!-- Name Input -->
                        <div class="form-floating mb-3">
                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="John Doe">
                            <label for="name" class="text-muted">Full Name</label>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <!-- Email Input -->
                        <div class="form-floating mb-3">
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="name@example.com">
                            <label for="email" class="text-muted">Email Address</label>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <!-- Password Input -->
                        <div class="form-floating mb-3">
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="Password">
                            <label for="password" class="text-muted">Password</label>
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <!-- Confirm Password Input -->
                        <div class="form-floating mb-4">
                            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm Password">
                            <label for="password-confirm" class="text-muted">Confirm Password</label>
                        </div>

                        <button type="submit" class="btn btn-primary-custom shadow-sm">
                            Sign Up
                        </button>

                        <div class="text-center mt-4">
                            <p class="text-muted small mb-0">Already have an account? 
                                <a href="{{ route('login') }}" class="text-primary fw-bold text-decoration-none">Sign in</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
