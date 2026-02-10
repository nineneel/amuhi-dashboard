@extends('layouts.app')

@section('title', 'Two-Factor Authentication')
@section('header', 'Two-Factor Authentication')

@section('page-header')
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Two-Factor Authentication</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('settings.index', ['section' => 'security']) }}">Security</a></li>
                <li class="breadcrumb-item">Two-Factor Authentication</li>
            </ul>
        </div>
    </div>
@endsection

@section('content')
    <p class="text-muted mb-4">Add an extra layer of security to your account.</p>

    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (! $twoFactorEnabled)
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Enable two-factor authentication</h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-4">
                    We'll send a 6-digit verification code to your email address to confirm activation.
                </p>

                <div class="d-flex flex-column gap-3 mb-4">
                    <div class="d-flex align-items-start gap-3">
                        <div class="avatar-text bg-soft-primary text-primary">
                            <i class="feather-mail"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Send a verification code</h6>
                            <p class="text-muted fs-13 mb-0">We'll email a code to <strong>{{ $maskedEmail }}</strong>.</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-start gap-3">
                        <div class="avatar-text bg-soft-warning text-warning">
                            <i class="feather-hash"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Enter the 6-digit code</h6>
                            <p class="text-muted fs-13 mb-0">Codes expire after 30 minutes.</p>
                        </div>
                    </div>
                </div>

                <div class="row g-3 align-items-end">
                    <div class="col-lg-4">
                        <form method="POST" action="{{ route('two-factor.enable.send') }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-primary w-100">
                                <i class="feather-send me-2"></i>Send code
                            </button>
                            @if ($enableCodeSent)
                                <div class="text-muted fs-12 mt-2">A code was sent to {{ $maskedEmail }}.</div>
                            @endif
                        </form>
                    </div>
                    <div class="col-lg-8">
                        <form method="POST" action="{{ route('two-factor.enable') }}">
                            @csrf

                            <div class="row g-3 align-items-end">
                                <div class="col-lg-7">
                                    <label class="form-label" for="code">Email verification code</label>
                                    <input
                                        id="code"
                                        name="code"
                                        type="text"
                                        inputmode="numeric"
                                        autocomplete="one-time-code"
                                        maxlength="6"
                                        required
                                        class="form-control @error('code') is-invalid @enderror"
                                    />
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-lg-5">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="feather-shield me-2"></i>Enable two-factor
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Two-factor status</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-success d-flex align-items-center gap-3 mb-4">
                    <i class="feather-shield fs-3"></i>
                    <div>
                        <strong>Two-factor authentication is enabled.</strong>
                        <p class="mb-0 fs-13">Keep your recovery codes safe in case you lose access.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('two-factor.disable') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="feather-shield-off me-2"></i>Disable two-factor
                    </button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Recovery codes</h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-4">Use a recovery code if you lose access to your email.</p>

                <div class="row g-2">
                    @forelse ($recoveryCodes as $code)
                        <div class="col-6 col-md-4 col-lg-3">
                            <div class="border rounded bg-light p-2 text-center font-monospace fs-12">
                                {{ $code }}
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-warning mb-0">
                                No recovery codes available.
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    @endif
@endsection
