<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">Notification Settings</h5>
    </div>
    <div class="card-body">
        <p class="text-muted mb-4">Control how you receive notifications from the application.</p>

        <form action="{{ route('settings.notifications') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" role="switch" id="notification_email" name="notification_email" value="1" {{ ($user->settings?->notification_email ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="notification_email">
                        <strong>Email Notifications</strong>
                        <p class="text-muted mb-0 fs-13">Receive important updates and notifications via email.</p>
                    </label>
                </div>

                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="notification_app" name="notification_app" value="1" {{ ($user->settings?->notification_app ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="notification_app">
                        <strong>In-App Notifications</strong>
                        <p class="text-muted mb-0 fs-13">Show notifications within the application dashboard.</p>
                    </label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="feather-save me-2"></i>Save Changes
            </button>
        </form>
    </div>
</div>
