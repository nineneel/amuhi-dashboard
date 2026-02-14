@extends('layouts.fullscreen-layout')

@section('content')
    <div class="relative z-1 min-h-screen overflow-hidden bg-white dark:bg-gray-900">
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute inset-0 bg-gradient-to-b from-gray-50/90 to-white/90 dark:from-gray-950/90 dark:to-gray-900/90"></div>
            <div class="absolute -left-28 -top-28 h-72 w-72 rounded-full bg-brand-500/10 blur-3xl"></div>
            <div class="absolute -bottom-28 -right-28 h-72 w-72 rounded-full bg-brand-500/10 blur-3xl"></div>
        </div>

        <div class="relative flex min-h-screen w-full flex-col lg:flex-row">
            <div class="flex w-full flex-1 items-center justify-center px-4 py-10 sm:px-6 lg:w-7/12 lg:px-10">
                <div class="w-full max-w-lg lg:max-w-2xl">
                    <a href="{{ url('/') }}"
                        class="inline-flex items-center gap-2 text-sm text-gray-500 transition-colors hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                        <svg class="stroke-current" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <path d="M12.7083 5L7.5 10.2083L12.7083 15.4167" stroke="" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Back to home
                    </a>

                    <div class="mt-6 rounded-2xl border border-gray-200 bg-white/80 p-6 shadow-theme-lg backdrop-blur dark:border-gray-800 dark:bg-gray-900/60 sm:p-8">
                        <div class="mb-6">
                            <h1 class="text-title-sm sm:text-title-md mb-2 font-semibold text-gray-800 dark:text-white/90">
                                Create Account
                            </h1>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Enter your details and we will email you a link to set your password and finish registration.
                            </p>
                        </div>

                        @if (session('status'))
                            <x-ui.alert variant="success" title="Check your email" :message="session('status')" class="mb-5" />
                        @endif

                        @if (session('mail_error'))
                            <x-ui.alert variant="warning" title="Delivery issue" :message="session('mail_error')" class="mb-5" />
                        @endif

                        @if ($errors->any())
                            <x-ui.alert variant="error" title="Registration failed" :message="$errors->first()" class="mb-5" />
                        @endif

                        <form action="{{ route('register') }}" method="POST">
                            @csrf
                            <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                                <div class="lg:col-span-2">
                                    <x-form.select label="Member Type" name="member_type" id="member_type" required>
                                        <option value="">Select member type</option>
                                        <option value="ppui_pihk" @selected(old('member_type') === 'ppui_pihk')>PPUI PIHK</option>
                                        <option value="pt" @selected(old('member_type') === 'pt')>PT</option>
                                        <option value="personal" @selected(old('member_type') === 'personal')>Personal</option>
                                    </x-form.select>
                                </div>

                                <div>
                                    <x-form.input label="Name" type="text" name="name" id="name" placeholder="Enter your full name"
                                        :value="old('name')" required />
                                </div>

                                <div>
                                    <x-form.input label="Phone Number" type="text" name="phone" id="phone"
                                        placeholder="Enter your phone number" :value="old('phone')" required />
                                </div>

                                <div>
                                    <x-form.input label="Email" type="email" name="email" id="email" placeholder="Enter your email"
                                        :value="old('email')" required />
                                </div>

                                <div>
                                    <x-form.input label="PT Name (Optional)" type="text" name="company_name" id="company_name"
                                        placeholder="Enter company or PT name" :value="old('company_name')" />
                                </div>

                                <div class="lg:col-span-2">
                                    <x-form.checkbox name="terms" id="terms" :checked="(bool) old('terms')" required>
                                        I agree to the terms and conditions.
                                    </x-form.checkbox>
                                </div>

                                <div class="lg:col-span-2">
                                    <button type="submit"
                                        class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 flex w-full items-center justify-center rounded-lg px-4 py-3 text-sm font-medium text-white transition">
                                        Complete Registration
                                    </button>
                                </div>
                            </div>
                        </form>

                        <div class="mt-6 border-t border-gray-200 pt-5 dark:border-gray-800">
                            <p class="text-center text-sm font-normal text-gray-700 sm:text-start dark:text-gray-400">
                                Already have an account?
                                <a href="{{ route('login') }}" class="text-brand-500 hover:text-brand-600 dark:text-brand-400">
                                    Sign In
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-brand-950 relative hidden min-h-screen w-full items-center lg:grid lg:w-5/12 dark:bg-white/5">
                <div class="z-1 flex items-center justify-center">
                    <x-common.common-grid-shape />
                    <div class="flex max-w-xs flex-col items-center">
                        <a href="{{ url('/') }}" class="mb-4 block">
                            <img src="/images/logo/auth-logo.png" alt="Logo" />
                        </a>
                        {{-- <p class="text-center text-gray-400 dark:text-white/60">
                            Verify your email after signup to activate your account.
                        </p> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
