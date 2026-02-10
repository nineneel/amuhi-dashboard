@extends('layouts.app')

@section('title', __('profile.edit.title'))
@section('header', __('profile.edit.title'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('profile.edit.breadcrumb.home') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('profile.index') }}">{{ __('profile.edit.breadcrumb.profile') }}</a></li>
    <li class="breadcrumb-item">{{ __('profile.edit.breadcrumb.edit') }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-xxl-8 col-xl-10 mx-auto">
            <div class="card stretch stretch-full">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">{{ __('profile.edit.title') }}</h5>
                    <a href="{{ route('profile.index') }}" class="btn btn-sm btn-light">
                        <i class="feather-arrow-left me-1"></i>{{ __('profile.edit.back') }}
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- Photo Upload --}}
                        <div class="mb-4 text-center">
                            <div class="position-relative d-inline-block">
                                @if($user->profile?->photo)
                                    <img src="{{ Storage::url($user->profile->photo) }}" alt="{{ $user->name }}" class="img-fluid rounded-circle" style="width: 100px; height: 100px; object-fit: cover;" id="preview-photo">
                                @else
                                    <div class="avatar-text rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 100px; height: 100px; font-size: 2rem;" id="preview-photo-placeholder">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <img src="" alt="" class="img-fluid rounded-circle d-none" style="width: 100px; height: 100px; object-fit: cover;" id="preview-photo">
                                @endif
                                <label for="photo" class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-2 cursor-pointer" style="cursor: pointer;">
                                    <i class="feather-camera fs-12"></i>
                                </label>
                                <input type="file" name="photo" id="photo" class="d-none" accept="image/*" onchange="previewImage(this)">
                            </div>
                            <p class="text-muted fs-12 mt-2">{{ __('profile.edit.photo.helper') }}</p>
                            @error('photo')
                                <p class="text-danger fs-12">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="row g-4">
                            {{-- Name --}}
                            <div class="col-md-6">
                                <label for="name" class="form-label">{{ __('profile.edit.fields.full_name') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div class="col-md-6">
                                <label for="email" class="form-label">{{ __('profile.edit.fields.email') }} <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required readonly aria-readonly="true">
                                <small class="text-muted">{{ __('profile.edit.email_note') }}</small>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Phone --}}
                            <div class="col-md-6">
                                <label for="phone" class="form-label">{{ __('profile.edit.fields.phone') }} <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $user->profile?->phone) }}" required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Member Type --}}
                            <div class="col-md-6">
                                <label for="member_type" class="form-label">{{ __('profile.edit.fields.member_type') }} <span class="text-danger">*</span></label>
                                <select class="form-select @error('member_type') is-invalid @enderror" id="member_type" name="member_type" required>
                                    <option value="">{{ __('profile.edit.member_type_placeholder') }}</option>
                                    <option value="ppui_pihk" {{ old('member_type', $user->profile?->member_type?->value) === 'ppui_pihk' ? 'selected' : '' }}>{{ __('profile.edit.member_types.ppui_pihk') }}</option>
                                    <option value="pt" {{ old('member_type', $user->profile?->member_type?->value) === 'pt' ? 'selected' : '' }}>{{ __('profile.edit.member_types.pt') }}</option>
                                    <option value="personal" {{ old('member_type', $user->profile?->member_type?->value) === 'personal' ? 'selected' : '' }}>{{ __('profile.edit.member_types.personal') }}</option>
                                </select>
                                @error('member_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Company Name --}}
                            <div class="col-md-6">
                                <label for="company_name" class="form-label">{{ __('profile.edit.fields.company_name') }}</label>
                                <input type="text" class="form-control @error('company_name') is-invalid @enderror" id="company_name" name="company_name" value="{{ old('company_name', $user->profile?->company_name) }}">
                                @error('company_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Address --}}
                            <div class="col-12">
                                <label for="address" class="form-label">{{ __('profile.edit.fields.address') }}</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="2">{{ old('address', $user->profile?->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Bio --}}
                            <div class="col-12">
                                <label for="bio" class="form-label">{{ __('profile.edit.fields.bio') }}</label>
                                <textarea class="form-control @error('bio') is-invalid @enderror" id="bio" name="bio" rows="3" maxlength="1000">{{ old('bio', $user->profile?->bio) }}</textarea>
                                <small class="form-text text-muted">{{ __('profile.edit.bio_note') }}</small>
                                @error('bio')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('profile.index') }}" class="btn btn-light">{{ __('profile.edit.actions.cancel') }}</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="feather-save me-1"></i>{{ __('profile.edit.actions.save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var placeholder = document.getElementById('preview-photo-placeholder');
            var preview = document.getElementById('preview-photo');
            if (placeholder) {
                placeholder.classList.add('d-none');
            }
            preview.src = e.target.result;
            preview.classList.remove('d-none');
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
