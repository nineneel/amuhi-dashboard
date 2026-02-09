<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">Account Settings</h5>
    </div>
    <div class="card-body">
        <div class="mb-4">
            <h6 class="mb-3">Profile</h6>
            <p class="text-muted mb-3">Manage your personal information and profile photo.</p>
            <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary">
                <i class="feather-edit me-2"></i>Edit Profile
            </a>
        </div>

        <hr>

        <form action="{{ route('settings.account') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <h6 class="mb-3">Language</h6>
                <p class="text-muted mb-3">Select your preferred language for the dashboard.</p>
                <select class="form-select @error('language') is-invalid @enderror" name="language" style="max-width: 300px;">
                    <option value="en" {{ ($user->settings?->language ?? 'en') === 'en' ? 'selected' : '' }}>English</option>
                    <option value="id" {{ ($user->settings?->language ?? 'en') === 'id' ? 'selected' : '' }}>Bahasa Indonesia</option>
                </select>
                @error('language')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="feather-save me-2"></i>Save Changes
            </button>
        </form>
    </div>
</div>
