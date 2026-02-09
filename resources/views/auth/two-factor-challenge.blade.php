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
                    <h4 class="fs-13 fw-bold mb-2">Enter your authentication code</h4>
                    <p class="fs-12 fw-medium text-muted">Please enter the code from your authenticator app to continue.</p>

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
                                   placeholder="Authentication Code"
                                   required
                                   autofocus
                                   autocomplete="off"
                                   maxlength="6">
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mt-5">
                            <button type="submit" class="btn btn-lg btn-primary w-100">Verify</button>
                        </div>
                    </form>

                    <div class="mt-5 text-muted text-center">
                        <p class="mb-0">Lost access to your device?</p>
                        <a href="{{ route('login') }}" class="fw-bold">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
