<div class="card border-danger">
    <div class="card-header bg-soft-danger">
        <h5 class="card-title mb-0 text-danger">Danger Zone</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-danger mb-4">
            <div class="d-flex gap-3">
                <i class="feather-alert-triangle fs-3"></i>
                <div>
                    <strong>Warning!</strong>
                    <p class="mb-0 fs-13">Once you delete your account, there is no going back. Please be certain.</p>
                </div>
            </div>
        </div>

        <h6 class="mb-3">Delete Account</h6>
        <p class="text-muted mb-4">Permanently delete your account and all associated data. This action cannot be undone.</p>

        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
            <i class="feather-trash-2 me-2"></i>Delete My Account
        </button>
    </div>
</div>

{{-- Delete Account Modal --}}
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title text-danger" id="deleteAccountModalLabel">
                    <i class="feather-alert-triangle me-2"></i>Delete Account
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('settings.destroy') }}" method="POST">
                @csrf
                @method('DELETE')

                <div class="modal-body">
                    <p>Are you sure you want to delete your account? This action is permanent and cannot be undone.</p>
                    <p class="text-muted fs-13">All your data, including profile information, settings, and activity history will be permanently deleted.</p>

                    <div class="mb-0">
                        <label for="delete_password" class="form-label">Enter your password to confirm</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="delete_password" name="password" required placeholder="Your current password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="feather-trash-2 me-2"></i>Yes, Delete My Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
