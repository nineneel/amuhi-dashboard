@php
    $privacySettings = $user->settings?->privacy_settings ?? [];
@endphp

<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">Privacy Settings</h5>
    </div>
    <div class="card-body">
        <p class="text-muted mb-4">Control your profile visibility and what information is shared with others.</p>

        <form action="{{ route('settings.privacy') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" role="switch" id="profile_visible" name="profile_visible" value="1" {{ ($privacySettings['profile_visible'] ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="profile_visible">
                        <strong>Public Profile</strong>
                        <p class="text-muted mb-0 fs-13">Allow other members to view your profile information.</p>
                    </label>
                </div>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" role="switch" id="show_email" name="show_email" value="1" {{ ($privacySettings['show_email'] ?? false) ? 'checked' : '' }}>
                    <label class="form-check-label" for="show_email">
                        <strong>Show Email Address</strong>
                        <p class="text-muted mb-0 fs-13">Display your email address on your public profile.</p>
                    </label>
                </div>

                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="show_phone" name="show_phone" value="1" {{ ($privacySettings['show_phone'] ?? false) ? 'checked' : '' }}>
                    <label class="form-check-label" for="show_phone">
                        <strong>Show Phone Number</strong>
                        <p class="text-muted mb-0 fs-13">Display your phone number on your public profile.</p>
                    </label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="feather-save me-2"></i>Save Changes
            </button>
        </form>
    </div>
</div>
