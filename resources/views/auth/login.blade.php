@extends('layouts.guest')

@section('content')
    <main class="auth-cover-wrapper">
        <div class="auth-cover-content-inner">
            <div class="auth-cover-content-wrapper">
                <div class="auth-img">
                    <img src="{{ asset('images/auth/auth-cover-login-bg.svg') }}" alt="" class="img-fluid">
                </div>
            </div>
        </div>
        <div class="auth-cover-sidebar-inner">
            <div class="auth-cover-card-wrapper">
                <div class="auth-cover-card p-sm-5">
                    <div class="wd-50 mb-5">
                        <img src="{{ asset('images/logo-abbr.png') }}" alt="{{ config('app.name') }}" class="img-fluid">
                    </div>

                    <h2 class="fs-20 fw-bolder mb-4">Login</h2>
                    <h4 class="fs-13 fw-bold mb-2">Login to your account</h4>
                    <p class="fs-12 fw-medium text-muted">Welcome back to {{ config('app.name') }}. Please sign in to continue.</p>

                    @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="w-100 mt-4 pt-2">
                        @csrf

                        <div class="mb-4">
                            <input type="email" name="email" class="form-control" placeholder="Email" value="{{ old('email') }}" required autofocus>
                        </div>

                        <div class="mb-3">
                            <input type="password" name="password" class="form-control" placeholder="Password" required>
                        </div>

                        <div class="d-flex align-items-center justify-content-between">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="rememberMe" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="custom-control-label c-pointer" for="rememberMe">Remember Me</label>
                            </div>
                            <div>
                                <a href="{{ route('password.request') }}" class="fs-11 text-primary">Forgot password?</a>
                            </div>
                        </div>

                        <div class="mt-5">
                            <button type="submit" class="btn btn-lg btn-primary w-100">Login</button>
                        </div>
                    </form>

                    <div class="w-100 mt-5 text-center mx-auto">
                        <div class="mb-4 border-bottom position-relative">
                            <span class="small py-1 px-3 text-uppercase text-muted bg-white position-absolute translate-middle">or</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-center gap-2">
                            <a href="javascript:void(0);" class="btn btn-light-brand flex-fill" data-bs-toggle="tooltip" data-bs-trigger="hover" title="Login with Facebook">
                                <i class="feather-facebook"></i>
                            </a>
                            <a href="javascript:void(0);" class="btn btn-light-brand flex-fill" data-bs-toggle="tooltip" data-bs-trigger="hover" title="Login with Twitter">
                                <i class="feather-twitter"></i>
                            </a>
                            <a href="javascript:void(0);" class="btn btn-light-brand flex-fill" data-bs-toggle="tooltip" data-bs-trigger="hover" title="Login with Github">
                                <i class="feather-github"></i>
                            </a>
                        </div>
                    </div>

                    <div class="mt-5 text-muted">
                        <span>Don't have an account?</span>
                        <a href="{{ route('register') }}" class="fw-bold">Create an Account</a>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
