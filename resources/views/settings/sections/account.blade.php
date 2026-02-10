<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">{{ __('settings.account.title') }}</h5>
    </div>
    <div class="card-body">
        <div class="mb-4">
            <h6 class="mb-3">{{ __('settings.account.profile.title') }}</h6>
            <p class="text-muted mb-3">{{ __('settings.account.profile.description') }}</p>
            <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary">
                <i class="feather-edit me-2"></i>{{ __('settings.account.profile.edit') }}
            </a>
        </div>

        <hr>

        <form action="{{ route('settings.account') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <h6 class="mb-3">{{ __('settings.account.language.title') }}</h6>
                <p class="text-muted mb-3">{{ __('settings.account.language.description') }}</p>
                <select class="form-select @error('language') is-invalid @enderror" name="language" style="max-width: 300px;">
                    <option value="en" {{ ($user->settings?->language ?? 'id') === 'en' ? 'selected' : '' }}>{{ __('settings.account.language.options.en') }}</option>
                    <option value="id" {{ ($user->settings?->language ?? 'id') === 'id' ? 'selected' : '' }}>{{ __('settings.account.language.options.id') }}</option>
                </select>
                @error('language')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="feather-save me-2"></i>{{ __('settings.account.save') }}
            </button>
        </form>
    </div>
</div>
