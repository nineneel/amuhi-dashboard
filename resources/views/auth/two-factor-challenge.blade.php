@extends('layouts.guest')

@section('title', 'Two-Factor Authentication')

@section('content')
<main class="auth-minimal-wrapper">
    <div class="auth-minimal-inner">
        <div class="minimal-card-wrapper">
            <div class="card mb-4 mt-5 mx-4 mx-sm-0 position-relative">
                <div class="wd-50 bg-white p-2 rounded-circle shadow-lg position-absolute translate-middle top-0 start-50">
                    <img src="{{ asset('images/logo-abbr.png') }}" alt="{{ config('app.name') }}" class="img-fluid">
                </div>
                <div class="card-body p-sm-5">
                    <h2 class="fs-20 fw-bolder mb-4">Two-Factor Authentication</h2>
                    <h4 class="fs-13 fw-bold mb-2">Enter your email verification code</h4>
                    <p class="fs-12 fw-medium text-muted">
                        We've emailed a 6-digit code to <strong>{{ $maskedEmail }}</strong>. You can also use a recovery code.
                    </p>

                    @if (session('status'))
                        <div class="alert alert-success mt-4">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger mt-4">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('two-factor.verify') }}" class="w-100 mt-4 pt-2">
                        @csrf

                        <div class="mb-4">
                            <input type="text"
                                   name="code"
                                   id="code"
                                   class="form-control @error('code') is-invalid @enderror"
                                   placeholder="Email verification or recovery code"
                                   required
                                   autofocus
                                   autocomplete="off"
                                   maxlength="10">
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mt-5">
                            <button type="submit" class="btn btn-lg btn-primary w-100">Verify</button>
                        </div>
                    </form>

                    <div class="mt-5 text-muted text-center">
                        <p class="mb-1">Didn't receive a code?</p>
                        <div class="d-flex align-items-center justify-content-center">
                            <form method="POST" action="{{ route('two-factor.challenge.resend') }}">
                                @csrf
                                <button type="submit" class="btn btn-link p-0 fw-bold align-baseline">Resend code</button>
                            </form>
                            <span class="mx-2">|</span>
                            <a href="{{ route('login') }}" class="fw-bold">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
