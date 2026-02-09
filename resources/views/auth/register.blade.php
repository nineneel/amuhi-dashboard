@extends('layouts.guest')

@section('content')
    <main class="auth-creative-wrapper">
        <div class="auth-creative-inner">
            <div class="creative-card-wrapper">
                <div class="card my-4 overflow-hidden" style="z-index: 1">
                    <div class="row flex-1 g-0">
                        <div class="col-lg-6 h-100 my-auto">
                            <div class="wd-50 bg-white p-2 rounded-circle shadow-lg position-absolute translate-middle top-50 start-50">
                                <img src="{{ asset('images/logo-abbr.png') }}" alt="{{ config('app.name') }}" class="img-fluid">
                            </div>
                            <div class="creative-card-body card-body p-sm-5">
                                <h2 class="fs-20 fw-bolder mb-4">Register</h2>
                                <h4 class="fs-13 fw-bold mb-2">Create your account</h4>
                                <p class="fs-12 fw-medium text-muted">Join {{ config('app.name') }} to access programs, events, and subscriptions.</p>

                                @if ($errors->any())
                                    <div class="alert alert-danger" role="alert">
                                        <ul class="mb-0 ps-3">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <form method="POST" action="{{ route('register') }}" class="w-100 mt-4 pt-2">
                                    @csrf

                                    <div class="mb-4">
                                        <select class="form-select" name="member_type" required>
                                            @foreach (\App\MemberType::cases() as $type)
                                                <option value="{{ $type->value }}" @selected(old('member_type') === $type->value)>
                                                    {{ $type->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-4">
                                        <input type="text" name="name" class="form-control" placeholder="Full Name" value="{{ old('name') }}" required autofocus>
                                    </div>

                                    <div class="mb-4">
                                        <input type="text" name="company_name" class="form-control" placeholder="Company Name (optional)" value="{{ old('company_name') }}">
                                    </div>

                                    <div class="mb-4">
                                        <input type="text" name="phone" class="form-control" placeholder="Phone" value="{{ old('phone') }}" required>
                                    </div>

                                    <div class="mb-4">
                                        <input type="email" name="email" class="form-control" placeholder="Email" value="{{ old('email') }}" required>
                                    </div>

                                    <div class="mb-4">
                                        <input type="password" class="form-control" name="password" id="newPassword" placeholder="Password" required>
                                    </div>

                                    <div class="mb-4">
                                        <input type="password" name="password_confirmation" class="form-control" placeholder="Password again" required>
                                    </div>

                                    <div class="mt-4">
                                        <div class="custom-control custom-checkbox mb-2">
                                            <input type="checkbox" class="custom-control-input" id="termsCondition" name="terms" value="1" required {{ old('terms') ? 'checked' : '' }}>
                                            <label class="custom-control-label c-pointer text-muted" for="termsCondition" style="font-weight: 400 !important">I agree to the terms and conditions.</label>
                                        </div>
                                    </div>

                                    <div class="mt-5">
                                        <button type="submit" class="btn btn-lg btn-primary w-100">Create Account</button>
                                    </div>
                                </form>

                                <div class="mt-5 text-muted">
                                    <span>Already have an account?</span>
                                    <a href="{{ route('login') }}" class="fw-bold">Login</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 bg-primary">
                            <div class="h-100 d-flex align-items-center justify-content-center">
                                <img src="{{ asset('images/auth/auth-user.png') }}" alt="Auth user" class="img-fluid">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
