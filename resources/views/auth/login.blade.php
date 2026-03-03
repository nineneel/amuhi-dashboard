@extends('layouts.fullscreen-layout')

@section('content')
    <div class="relative min-h-screen overflow-hidden bg-[#040b1f]">
        <video
            class="pointer-events-none absolute inset-0 h-full w-full object-cover"
            autoplay
            loop
            muted
            playsinline
            preload="metadata"
            poster="{{ asset('images/auth/auth-poster-white.svg') }}"
            aria-hidden="true"
        >
            <source src="{{ asset('images/auth/amuhi-auth-background.mp4') }}" type="video/mp4">
        </video>
        <div class="relative z-10 mx-auto flex min-h-screen w-full max-w-[1440px] flex-col items-center justify-center gap-10 px-6 py-10 lg:flex-row lg:items-stretch lg:gap-16 lg:px-12">
            <section class="flex w-full max-w-2xl flex-col justify-center">
                <div class="w-full max-w-xl">
                    <h1 class="text-title-sm sm:text-title-md mb-2 font-semibold text-[#2c58a8] dark:text-[#9dc8ff]">
                        {{ __('ui.auth.sign_in_title') }}
                    </h1>
                    <p class="text-sm text-[#2c58a8]/90 dark:text-[#d7e7ff]/80">
                        {{ __('ui.auth.sign_in_subtitle') }}
                    </p>

                    @if (session('status'))
                        <x-ui.alert
                            variant="success"
                            :title="__('ui.common.success')"
                            :message="session('status')"
                            class="mt-6"
                        />
                    @endif

                    @if ($errors->any())
                        <x-ui.alert
                            variant="error"
                            :title="__('ui.auth.unable_to_sign_in')"
                            :message="$errors->first()"
                            class="mt-6"
                        />
                    @endif

                    <form action="{{ route('login') }}" method="POST" class="mt-8">
                        @csrf
                        <div class="space-y-5">
                            <div class="[&_label]:!text-[#2c58a8] dark:[&_label]:!text-[#b8d7ff]">
                                <x-form.input
                                    :label="__('ui.forms.email')"
                                    type="email"
                                    name="email"
                                    id="email"
                                    :placeholder="__('ui.forms.placeholder.enter_email')"
                                    :value="old('email')"
                                    required
                                    autofocus
                                    class="!border-[#2c58a8]/25 !bg-white/80 !text-[#2c58a8] !placeholder:text-[#2c58a8]/65 focus:!border-[#2c58a8]/55 focus:!ring-[#2c58a8]/20 dark:!border-[#82a7e5]/35 dark:!bg-[#091838]/85 dark:!text-[#e6f0ff] dark:!placeholder:text-[#afc2e0]/70 dark:focus:!border-[#95d9f0]/70 dark:focus:!ring-[#95d9f0]/20"
                                />
                            </div>

                            <div class="[&_label]:!text-[#2c58a8] dark:[&_label]:!text-[#b8d7ff]">
                                <x-form.input
                                    :label="__('ui.forms.password')"
                                    type="password"
                                    name="password"
                                    id="password"
                                    :placeholder="__('ui.forms.placeholder.enter_password')"
                                    required
                                    class="!border-[#2c58a8]/25 !bg-white/80 !text-[#2c58a8] !placeholder:text-[#2c58a8]/65 focus:!border-[#2c58a8]/55 focus:!ring-[#2c58a8]/20 dark:!border-[#82a7e5]/35 dark:!bg-[#091838]/85 dark:!text-[#e6f0ff] dark:!placeholder:text-[#afc2e0]/70 dark:focus:!border-[#95d9f0]/70 dark:focus:!ring-[#95d9f0]/20"
                                />
                            </div>

                            <div class="flex items-center justify-between">
                                <x-form.checkbox
                                    name="remember"
                                    id="remember"
                                    :checked="(bool) old('remember')"
                                    containerClass="flex cursor-pointer items-center gap-2 text-sm text-[#2c58a8] dark:text-[#b8d7ff]"
                                    labelClass="font-normal text-[#2c58a8] dark:text-[#b8d7ff]"
                                >
                                    {{ __('ui.forms.remember_me') }}
                                </x-form.checkbox>

                                <a href="{{ route('password.request') }}" class="text-sm text-[#2c58a8] hover:text-[#1f3f7b] dark:text-[#95d9f0] dark:hover:text-[#b7e8f8]">
                                    {{ __('ui.forms.forgot_password') }}
                                </a>
                            </div>

                            <div>
                                <button
                                    type="submit"
                                    class="shadow-theme-xs flex w-full items-center justify-center rounded-lg bg-[#2c58a8] px-4 py-3 text-sm font-medium text-white transition hover:bg-[#23498d] dark:bg-[#4864ff] dark:hover:bg-[#5973ff]"
                                >
                                    {{ __('ui.buttons.sign_in') }}
                                </button>
                            </div>
                        </div>
                    </form>

                    <p class="mt-5 text-sm font-normal text-[#2c58a8]/85 sm:text-start dark:text-[#d0e2ff]/80">
                        {{ __('ui.auth.no_account') }}
                        <a href="{{ route('register') }}" class="font-semibold text-[#2c58a8] hover:text-[#1f3f7b] dark:text-[#95d9f0] dark:hover:text-[#b7e8f8]">
                            {{ __('ui.buttons.sign_up') }}
                        </a>
                    </p>
                </div>
            </section>

            <aside class="flex w-full max-w-xl items-center justify-center">
                <img
                    src="{{ asset('images/logo/amuhi-logo.png') }}"
                    alt="AMUHI Logo"
                    class="w-full max-w-[520px] object-contain"
                >
            </aside>
        </div>
    </div>
@endsection
