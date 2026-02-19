@extends('layouts.app')

@section('content')
    @php
        $profile = $user->profile;
        $currentPhotoUrl = $profile?->photo ? asset('storage/' . $profile->photo) : null;
    @endphp

    <x-common.page-breadcrumb
        :pageTitle="__('ui.profile.edit_profile')"
        :items="[
            ['label' => __('ui.profile.title'), 'href' => route('profile.index')],
            ['label' => __('ui.profile.edit_profile')],
        ]"
    />

    @if ($errors->any())
        <x-ui.alert variant="error" :title="__('ui.profile.unable_to_update')" :message="$errors->first()" class="mb-6" />
    @endif

    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="grid gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <x-form.select :label="__('ui.forms.member_type')" name="member_type" id="member_type" required>
                        <option value="ppui_pihk" @selected(old('member_type', $profile?->member_type?->value) === 'ppui_pihk')>PPIU PIHK</option>
                        <option value="pt" @selected(old('member_type', $profile?->member_type?->value) === 'pt')>PT</option>
                        <option value="personal" @selected(old('member_type', $profile?->member_type?->value) === 'personal')>Personal</option>
                    </x-form.select>
                </div>

                <x-form.input :label="__('ui.forms.name')" type="text" name="name" id="name" :value="old('name', $user->name)" required />
                <x-form.input :label="__('ui.forms.email')" type="email" name="email" id="email" :value="old('email', $user->email)" required />
                <x-form.input :label="__('ui.profile.phone')" type="text" name="phone" id="phone" :value="old('phone', $profile?->phone)" required />
                <x-form.input :label="__('ui.profile.company_name')" type="text" name="company_name" id="company_name" :value="old('company_name', $profile?->company_name)" />

                <div class="sm:col-span-2">
                    <label for="address" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('ui.profile.address') }}</label>
                    <textarea id="address" name="address" rows="3"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">{{ old('address', $profile?->address) }}</textarea>
                </div>

                <div class="sm:col-span-2">
                    <label for="bio" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('ui.profile.bio') }}</label>
                    <textarea id="bio" name="bio" rows="4"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">{{ old('bio', $profile?->bio) }}</textarea>
                </div>

                <div class="sm:col-span-2"
                    x-data="{
                        previewUrl: @js($currentPhotoUrl),
                        selectedObjectUrl: null,
                        updatePreview(event) {
                            const file = event.target.files[0] ?? null;

                            if (this.selectedObjectUrl) {
                                URL.revokeObjectURL(this.selectedObjectUrl);
                                this.selectedObjectUrl = null;
                            }

                            if (file) {
                                this.selectedObjectUrl = URL.createObjectURL(file);
                                this.previewUrl = this.selectedObjectUrl;
                                return;
                            }

                            this.previewUrl = @js($currentPhotoUrl);
                        }
                    }">
                    <label for="photo" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('ui.profile.profile_photo') }}</label>
                    <div class="mt-3 flex flex-col gap-4 sm:flex-row sm:items-center">
                        <div class="flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-full border border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-900">
                            <img x-show="previewUrl" x-cloak :src="previewUrl" alt="Profile photo preview"
                                class="h-full w-full object-cover" />
                            <span x-show="!previewUrl" class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('ui.profile.no_photo') }}</span>
                        </div>
                        <div class="w-full">
                            <x-form.input type="file" name="photo" id="photo" accept="image/png,image/jpeg,image/jpg"
                                x-on:change="updatePreview($event)" />
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('ui.profile.accepted_formats') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                <button type="submit"
                    class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center rounded-lg px-4 py-2.5 text-sm font-medium text-white transition">
                    {{ __('ui.common.save_changes') }}
                </button>
                <a href="{{ route('profile.index') }}"
                    class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5">
                    {{ __('ui.common.cancel') }}
                </a>
            </div>
        </form>
    </div>
@endsection
