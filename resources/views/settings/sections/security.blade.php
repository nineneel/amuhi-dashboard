<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">Change Password</h5>
    </div>
    <div class="card-body">
        <p class="text-muted mb-4">Ensure your account is using a secure password.</p>

        <form action="{{ route('settings.password') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-4">
                <div class="col-md-6">
                    <label for="current_password" class="form-label">Current Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required>
                    @error('current_password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12"></div>

                <div class="col-md-6">
                    <label for="password" class="form-label">New Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="password_confirmation" class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="feather-lock me-2"></i>Change Password
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">Two-Factor Authentication</h5>
    </div>
    <div class="card-body">
        <p class="text-muted mb-4">Add an extra layer of security to your account using two-factor authentication.</p>

        @if(auth()->user()->two_factor_enabled)
            <div class="alert alert-success d-flex align-items-center gap-3">
                <i class="feather-shield fs-3"></i>
                <div>
                    <strong>Two-factor authentication is enabled.</strong>
                    <p class="mb-0 fs-13">Your account is protected with 2FA.</p>
                </div>
            </div>
        @else
            <div class="alert alert-warning d-flex align-items-center gap-3">
                <i class="feather-alert-triangle fs-3"></i>
                <div>
                    <strong>Two-factor authentication is not enabled.</strong>
                    <p class="mb-0 fs-13">Enable 2FA to add an extra layer of security.</p>
                </div>
            </div>
        @endif

        <a href="{{ route('two-factor.index') }}" class="btn btn-outline-primary">
            <i class="feather-shield me-2"></i>Manage Two-Factor Authentication
        </a>
    </div>
</div>
