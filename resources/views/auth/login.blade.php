@extends('layouts.fullscreen-layout')

@section('content')
    <div class="relative z-1 bg-white p-6 sm:p-0 dark:bg-gray-900">
        <div class="relative flex min-h-screen w-full flex-col justify-center sm:p-0 lg:flex-row dark:bg-gray-900">
            <div class="flex w-full flex-1 flex-col lg:w-1/2">
                {{-- <div class="mx-auto w-full max-w-md pt-10">
                    <a href="{{ url('/') }}"
                        class="inline-flex items-center text-sm text-gray-500 transition-colors hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                        <svg class="stroke-current" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <path d="M12.7083 5L7.5 10.2083L12.7083 15.4167" stroke="" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Back to home
                    </a>
                </div> --}}

                <div class="mx-auto flex w-full max-w-md flex-1 flex-col justify-center">
                    <div class="mb-5 sm:mb-8">
                        <h1 class="text-title-sm sm:text-title-md mb-2 font-semibold text-gray-800 dark:text-white/90">
                            Sign In
                        </h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Enter your email and password to continue.
                        </p>
                    </div>

                    @if (session('status'))
                        <x-ui.alert variant="success" title="Success" :message="session('status')" class="mb-5" />
                    @endif

                    @if ($errors->any())
                        <x-ui.alert variant="error" title="Unable to sign in" :message="$errors->first()" class="mb-5" />
                    @endif

                    <form action="{{ route('login') }}" method="POST">
                        @csrf
                        <div class="space-y-5">
                            <div>
                                <x-form.input label="Email" type="email" name="email" id="email" placeholder="Enter your email"
                                    :value="old('email')" required autofocus />
                            </div>

                            <div>
                                <x-form.input label="Password" type="password" name="password" id="password"
                                    placeholder="Enter your password" required />
                            </div>

                            <div class="flex items-center justify-between">
                                <label for="remember" class="flex cursor-pointer items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                    <input id="remember" name="remember" type="checkbox" value="1"
                                        class="h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500/20 dark:border-gray-700"
                                        @checked(old('remember'))>
                                    Remember me
                                </label>

                                <a href="{{ route('password.request') }}"
                                    class="text-sm text-brand-500 hover:text-brand-600 dark:text-brand-400">
                                    Forgot password?
                                </a>
                            </div>

                            <div>
                                <button type="submit"
                                    class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 flex w-full items-center justify-center rounded-lg px-4 py-3 text-sm font-medium text-white transition">
                                    Sign In
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="mt-5">
                        <p class="text-center text-sm font-normal text-gray-700 sm:text-start dark:text-gray-400">
                            Don't have an account?
                            <a href="{{ route('register') }}" class="text-brand-500 hover:text-brand-600 dark:text-brand-400">
                                Sign Up
                            </a>
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-brand-950 relative hidden min-h-screen w-full items-center lg:grid lg:w-1/2 dark:bg-white/5">
                <div class="z-1 flex items-center justify-center">
                    <x-common.common-grid-shape />
                    <div class="flex max-w-xs flex-col items-center">
                        <a href="{{ url('/') }}" class="mb-4 block">
                            <img src="/images/logo/auth-logo.svg" alt="Logo" />
                        </a>
                        <p class="text-center text-gray-400 dark:text-white/60">
                            Manage your AMUHI member account securely.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
