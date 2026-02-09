@extends('layouts.guest')

@section('title', 'Verify Email')

@section('content')
<main class="auth-minimal-wrapper">
    <div class="auth-minimal-inner">
        <div class="minimal-card-wrapper">
            <div class="card mb-4 mt-5 mx-4 mx-sm-0 position-relative">
                <div class="wd-50 bg-white p-2 rounded-circle shadow-lg position-absolute translate-middle top-0 start-50">
                    <img src="{{ asset('images/logo-abbr.png') }}" alt="{{ config('app.name') }}" class="img-fluid">
                </div>
                <div class="card-body p-sm-5">
                    <h2 class="fs-20 fw-bolder mb-4">Verify Your Email</h2>
                    <h4 class="fs-13 fw-bold mb-2">Check your inbox</h4>
                    <p class="fs-12 fw-medium text-muted">Thanks for signing up! Please verify your email address by clicking the link we just emailed to you.</p>

                    @if (session('status'))
                        <div class="alert alert-success mt-4">
                            A new verification link has been sent to your email address.
                        </div>
                    @endif

                    <form method="POST" action="{{ route('verification.resend') }}" class="w-100 mt-4 pt-2">
                        @csrf

                        <div class="mt-5">
                            <button type="submit" class="btn btn-lg btn-primary w-100">Resend Verification Email</button>
                        </div>
                    </form>

                    <form method="POST" action="{{ route('logout') }}" class="mt-3">
                        @csrf
                        <button type="submit" class="btn btn-lg btn-outline-secondary w-100">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
