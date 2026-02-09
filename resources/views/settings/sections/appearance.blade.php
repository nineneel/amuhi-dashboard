<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">Appearance Settings</h5>
    </div>
    <div class="card-body">
        <p class="text-muted mb-4">Customize the look and feel of your dashboard.</p>

        <form action="{{ route('settings.appearance') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <h6 class="mb-3">Theme</h6>
                <div class="row g-3">
                    <div class="col-auto">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="theme" id="theme_light" value="light" {{ ($user->settings?->theme ?? 'light') === 'light' ? 'checked' : '' }}>
                            <label class="form-check-label" for="theme_light">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="feather-sun text-warning"></i>
                                    <span>Light</span>
                                </div>
                            </label>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="theme" id="theme_dark" value="dark" {{ ($user->settings?->theme ?? 'light') === 'dark' ? 'checked' : '' }}>
                            <label class="form-check-label" for="theme_dark">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="feather-moon text-info"></i>
                                    <span>Dark</span>
                                </div>
                            </label>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="theme" id="theme_system" value="system" {{ ($user->settings?->theme ?? 'light') === 'system' ? 'checked' : '' }}>
                            <label class="form-check-label" for="theme_system">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="feather-monitor text-secondary"></i>
                                    <span>System</span>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
                @error('theme')
                    <div class="text-danger fs-13 mt-2">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="feather-save me-2"></i>Save Changes
            </button>
        </form>
    </div>
</div>
