@extends('layouts.guest')

@push('styles')
<style>
    .auth-minimal-wrapper .auth-minimal-inner .minimal-card-wrapper {
        max-width: 640px;
    }
</style>
@endpush

@section('title', 'Register')

@section('content')
<main class="auth-minimal-wrapper">
    <div class="auth-minimal-inner">
        <div class="minimal-card-wrapper">
            <div class="card mb-4 mt-5 mx-4 mx-sm-0 position-relative">
                <div class="wd-50 bg-white p-2 rounded-circle shadow-lg position-absolute translate-middle top-0 start-50">
                    <img src="{{ asset('images/logo-abbr.png') }}" alt="{{ config('app.name') }}" class="img-fluid">
                </div>
                <div class="card-body p-sm-5">
                    <h2 class="fs-20 fw-bolder mb-4">Register</h2>
                    <h4 class="fs-13 fw-bold mb-2">Create your account</h4>
                    <p class="fs-12 fw-medium text-muted">Join <strong>{{ config('app.name') }}</strong> to access programs and events. Let's get you setup.</p>

                    @if ($errors->any())
                        <div class="alert alert-danger mt-4">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register') }}" class="w-100 mt-4 pt-2">
                        @csrf

                        <div class="row g-3 g-md-4">
                            <div class="col-12">
                                <select name="member_type"
                                        id="member_type"
                                        class="form-select member-type-select @error('member_type') is-invalid @enderror"
                                        required>
                                    <option value="">Select Member Type</option>
                                    @foreach (\App\MemberType::cases() as $type)
                                        <option value="{{ $type->value }}" @selected(old('member_type') === $type->value)>
                                            {{ strtoupper(str_replace('_', ' ', $type->value)) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('member_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-lg-6">
                                <input type="text"
                                       name="name"
                                       id="name"
                                       class="form-control @error('name') is-invalid @enderror"
                                       placeholder="Full Name"
                                       value="{{ old('name') }}"
                                       required
                                       autofocus>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-lg-6">
                                <input type="text"
                                       name="company_name"
                                       id="company_name"
                                       class="form-control @error('company_name') is-invalid @enderror"
                                       placeholder="Company Name (Optional)"
                                       value="{{ old('company_name') }}">
                                @error('company_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-lg-6">
                                <input type="tel"
                                       name="phone"
                                       id="phone"
                                       class="form-control @error('phone') is-invalid @enderror"
                                       placeholder="Phone Number"
                                       value="{{ old('phone') }}"
                                       required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-lg-6">
                                <input type="email"
                                       name="email"
                                       id="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       placeholder="Email"
                                       value="{{ old('email') }}"
                                       required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-lg-6">
                                <input type="password"
                                       name="password"
                                       id="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       placeholder="Password"
                                       required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-lg-6">
                                <input type="password"
                                       name="password_confirmation"
                                       id="password_confirmation"
                                       class="form-control"
                                       placeholder="Confirm Password"
                                       required>
                            </div>

                            <div class="col-12">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox"
                                           name="terms"
                                           value="1"
                                           class="custom-control-input @error('terms') is-invalid @enderror"
                                           id="termsCondition"
                                           {{ old('terms') ? 'checked' : '' }}
                                           required>
                                    <label class="custom-control-label c-pointer" for="termsCondition">
                                        I agree to the <a href="#" class="text-primary">Terms & Conditions</a>
                                    </label>
                                    @error('terms')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12 mt-2">
                                <button type="submit" class="btn btn-lg btn-primary w-100">Create Account</button>
                            </div>
                        </div>
                    </form>

                    <div class="mt-5 text-muted">
                        <span>Already have an account?</span>
                        <a href="{{ route('login') }}" class="fw-bold">Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
