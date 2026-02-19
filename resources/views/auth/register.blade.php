@extends('layouts.fullscreen-layout')

@section('content')
    <div class="relative min-h-screen overflow-hidden bg-[#dcebf6] dark:bg-[#040b1f]">
        <div class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">
            <div class="auth-flare-orb auth-flare-orb-one"></div>
            <div class="auth-flare-orb auth-flare-orb-two"></div>
            <div class="auth-flare-orb auth-flare-orb-three"></div>
            <div class="auth-flare-beam auth-flare-beam-one"></div>
            <div class="auth-flare-beam auth-flare-beam-two"></div>
        </div>

        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_15%_25%,rgba(255,255,255,0.72),transparent_48%),radial-gradient(circle_at_82%_24%,rgba(149,217,240,0.35),transparent_45%),linear-gradient(115deg,rgba(207,227,243,0.55)_0%,rgba(231,242,249,0.48)_52%,rgba(215,232,244,0.55)_100%)] dark:bg-[radial-gradient(circle_at_16%_20%,rgba(56,108,196,0.42),transparent_42%),radial-gradient(circle_at_82%_22%,rgba(17,148,212,0.26),transparent_44%),linear-gradient(115deg,rgba(2,9,32,0.94)_0%,rgba(4,15,45,0.9)_52%,rgba(6,24,63,0.94)_100%)]"></div>

        <div class="relative mx-auto flex min-h-screen w-full max-w-[1440px] flex-col items-center justify-center gap-10 px-6 py-10 lg:flex-row lg:items-stretch lg:gap-16 lg:px-12">
            <section class="flex w-full max-w-2xl flex-col justify-center">
                <div class="w-full max-w-2xl">
                    <h1 class="text-title-sm sm:text-title-md mb-2 font-semibold text-[#2c58a8] dark:text-[#9dc8ff]">
                        {{ __('ui.auth.create_account_title') }}
                    </h1>
                    <p class="text-sm text-[#2c58a8]/90 dark:text-[#d7e7ff]/80">
                        {{ __('ui.auth.create_account_subtitle') }}
                    </p>

                    @if (session('status'))
                        <x-ui.alert
                            variant="success"
                            :title="__('ui.auth.check_your_email')"
                            :message="session('status')"
                            class="mt-6"
                        />
                    @endif

                    @if (session('mail_error'))
                        <x-ui.alert
                            variant="warning"
                            :title="__('ui.auth.delivery_issue')"
                            :message="session('mail_error')"
                            class="mt-6"
                        />
                    @endif

                    @if ($errors->any())
                        <x-ui.alert
                            variant="error"
                            :title="__('ui.auth.registration_failed')"
                            :message="$errors->first()"
                            class="mt-6"
                        />
                    @endif

                    <form action="{{ route('register') }}" method="POST" class="mt-8">
                        @csrf

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            <div class="sm:col-span-2 [&_label]:!text-[#2c58a8] dark:[&_label]:!text-[#b8d7ff]">
                                <x-form.select
                                    :label="__('ui.forms.member_type')"
                                    name="member_type"
                                    id="member_type"
                                    required
                                    class="!border-[#2c58a8]/25 !bg-white/80 !text-[#2c58a8] focus:!border-[#2c58a8]/55 focus:!ring-[#2c58a8]/20 dark:!border-[#82a7e5]/35 dark:!bg-[#091838]/85 dark:!text-[#e6f0ff] dark:focus:!border-[#95d9f0]/70 dark:focus:!ring-[#95d9f0]/20"
                                >
                                    <option value="">{{ __('ui.forms.select_member_type') }}</option>
                                    <option value="ppui_pihk" @selected(old('member_type') === 'ppui_pihk')>PPIU PIHK</option>
                                    <option value="pt" @selected(old('member_type') === 'pt')>PT</option>
                                    <option value="personal" @selected(old('member_type') === 'personal')>Personal</option>
                                </x-form.select>
                            </div>

                            <div class="[&_label]:!text-[#2c58a8] dark:[&_label]:!text-[#b8d7ff]">
                                <x-form.input
                                    :label="__('ui.forms.name')"
                                    type="text"
                                    name="name"
                                    id="name"
                                    :placeholder="__('ui.forms.placeholder.enter_full_name')"
                                    :value="old('name')"
                                    required
                                    class="!border-[#2c58a8]/25 !bg-white/80 !text-[#2c58a8] !placeholder:text-[#2c58a8]/65 focus:!border-[#2c58a8]/55 focus:!ring-[#2c58a8]/20 dark:!border-[#82a7e5]/35 dark:!bg-[#091838]/85 dark:!text-[#e6f0ff] dark:!placeholder:text-[#afc2e0]/70 dark:focus:!border-[#95d9f0]/70 dark:focus:!ring-[#95d9f0]/20"
                                />
                            </div>

                            <div class="[&_label]:!text-[#2c58a8] dark:[&_label]:!text-[#b8d7ff]">
                                <x-form.input
                                    :label="__('ui.forms.phone_number')"
                                    type="text"
                                    name="phone"
                                    id="phone"
                                    :placeholder="__('ui.forms.placeholder.enter_phone')"
                                    :value="old('phone')"
                                    required
                                    class="!border-[#2c58a8]/25 !bg-white/80 !text-[#2c58a8] !placeholder:text-[#2c58a8]/65 focus:!border-[#2c58a8]/55 focus:!ring-[#2c58a8]/20 dark:!border-[#82a7e5]/35 dark:!bg-[#091838]/85 dark:!text-[#e6f0ff] dark:!placeholder:text-[#afc2e0]/70 dark:focus:!border-[#95d9f0]/70 dark:focus:!ring-[#95d9f0]/20"
                                />
                            </div>

                            <div class="[&_label]:!text-[#2c58a8] dark:[&_label]:!text-[#b8d7ff]">
                                <x-form.input
                                    :label="__('ui.forms.email')"
                                    type="email"
                                    name="email"
                                    id="email"
                                    :placeholder="__('ui.forms.placeholder.enter_email')"
                                    :value="old('email')"
                                    required
                                    class="!border-[#2c58a8]/25 !bg-white/80 !text-[#2c58a8] !placeholder:text-[#2c58a8]/65 focus:!border-[#2c58a8]/55 focus:!ring-[#2c58a8]/20 dark:!border-[#82a7e5]/35 dark:!bg-[#091838]/85 dark:!text-[#e6f0ff] dark:!placeholder:text-[#afc2e0]/70 dark:focus:!border-[#95d9f0]/70 dark:focus:!ring-[#95d9f0]/20"
                                />
                            </div>

                            <div class="[&_label]:!text-[#2c58a8] dark:[&_label]:!text-[#b8d7ff]">
                                <x-form.input
                                    :label="__('ui.forms.pt_name_optional')"
                                    type="text"
                                    name="company_name"
                                    id="company_name"
                                    :placeholder="__('ui.forms.placeholder.enter_company')"
                                    :value="old('company_name')"
                                    class="!border-[#2c58a8]/25 !bg-white/80 !text-[#2c58a8] !placeholder:text-[#2c58a8]/65 focus:!border-[#2c58a8]/55 focus:!ring-[#2c58a8]/20 dark:!border-[#82a7e5]/35 dark:!bg-[#091838]/85 dark:!text-[#e6f0ff] dark:!placeholder:text-[#afc2e0]/70 dark:focus:!border-[#95d9f0]/70 dark:focus:!ring-[#95d9f0]/20"
                                />
                            </div>

                            <div class="sm:col-span-2">
                                <x-form.checkbox
                                    name="terms"
                                    id="terms"
                                    :checked="(bool) old('terms')"
                                    required
                                    containerClass="flex cursor-pointer items-start gap-2 text-sm text-[#2c58a8] dark:text-[#b8d7ff]"
                                    labelClass="font-normal text-[#2c58a8] dark:text-[#b8d7ff]"
                                >
                                    {{ __('ui.auth.terms') }}
                                </x-form.checkbox>
                            </div>

                            <div class="sm:col-span-2">
                                <button
                                    type="submit"
                                    class="shadow-theme-xs flex w-full items-center justify-center rounded-lg bg-[#2c58a8] px-4 py-3 text-sm font-medium text-white transition hover:bg-[#23498d] dark:bg-[#4864ff] dark:hover:bg-[#5973ff]"
                                >
                                    {{ __('ui.buttons.complete_registration') }}
                                </button>
                            </div>
                        </div>
                    </form>

                    <p class="mt-5 text-sm font-normal text-[#2c58a8]/85 sm:text-start dark:text-[#d0e2ff]/80">
                        {{ __('ui.auth.already_have_account') }}
                        <a href="{{ route('login') }}" class="font-semibold text-[#2c58a8] hover:text-[#1f3f7b] dark:text-[#95d9f0] dark:hover:text-[#b7e8f8]">
                            {{ __('ui.buttons.sign_in') }}
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

<style>
    .auth-flare-orb {
        position: absolute;
        border-radius: 9999px;
        filter: blur(80px);
        opacity: 0.5;
        transform: translate3d(0, 0, 0);
        animation: authFlareFloat 16s ease-in-out infinite;
        will-change: transform, opacity;
    }

    .auth-flare-orb-one {
        left: -8%;
        top: -16%;
        height: min(42vw, 520px);
        width: min(42vw, 520px);
        background: radial-gradient(circle, rgba(149, 217, 240, 0.8) 0%, rgba(149, 217, 240, 0) 68%);
    }

    .auth-flare-orb-two {
        right: -12%;
        top: 12%;
        height: min(38vw, 470px);
        width: min(38vw, 470px);
        background: radial-gradient(circle, rgba(44, 88, 168, 0.46) 0%, rgba(44, 88, 168, 0) 72%);
        animation-delay: -5s;
    }

    .auth-flare-orb-three {
        left: 32%;
        bottom: -24%;
        height: min(46vw, 580px);
        width: min(46vw, 580px);
        background: radial-gradient(circle, rgba(120, 198, 240, 0.35) 0%, rgba(120, 198, 240, 0) 70%);
        animation-delay: -9s;
    }

    .auth-flare-beam {
        position: absolute;
        width: 26%;
        min-width: 220px;
        border-radius: 9999px;
        filter: blur(18px);
        opacity: 0.28;
        transform: rotate(-26deg);
        animation: authFlareSweep 18s linear infinite;
        will-change: transform, opacity;
    }

    .auth-flare-beam-one {
        top: -15%;
        right: 12%;
        height: 160%;
        background: linear-gradient(180deg, rgba(149, 217, 240, 0) 8%, rgba(149, 217, 240, 0.5) 52%, rgba(149, 217, 240, 0) 95%);
    }

    .auth-flare-beam-two {
        top: -28%;
        left: 8%;
        height: 180%;
        background: linear-gradient(180deg, rgba(44, 88, 168, 0) 14%, rgba(44, 88, 168, 0.34) 50%, rgba(44, 88, 168, 0) 89%);
        animation-delay: -7s;
    }

    .dark .auth-flare-orb-one {
        background: radial-gradient(circle, rgba(56, 132, 255, 0.42) 0%, rgba(56, 132, 255, 0) 70%);
        opacity: 0.42;
    }

    .dark .auth-flare-orb-two {
        background: radial-gradient(circle, rgba(64, 212, 255, 0.28) 0%, rgba(64, 212, 255, 0) 73%);
        opacity: 0.44;
    }

    .dark .auth-flare-orb-three {
        background: radial-gradient(circle, rgba(62, 104, 235, 0.24) 0%, rgba(62, 104, 235, 0) 68%);
        opacity: 0.38;
    }

    .dark .auth-flare-beam-one {
        background: linear-gradient(180deg, rgba(71, 166, 255, 0) 8%, rgba(71, 166, 255, 0.3) 54%, rgba(71, 166, 255, 0) 95%);
        opacity: 0.24;
    }

    .dark .auth-flare-beam-two {
        background: linear-gradient(180deg, rgba(70, 95, 255, 0) 10%, rgba(70, 95, 255, 0.22) 52%, rgba(70, 95, 255, 0) 90%);
        opacity: 0.22;
    }

    @keyframes authFlareFloat {
        0%,
        100% {
            transform: translate3d(0, 0, 0) scale(1);
            opacity: 0.45;
        }

        35% {
            transform: translate3d(3.5%, -5.5%, 0) scale(1.06);
            opacity: 0.62;
        }

        70% {
            transform: translate3d(-4%, 4.5%, 0) scale(0.96);
            opacity: 0.38;
        }
    }

    @keyframes authFlareSweep {
        0% {
            transform: translate3d(0, 0, 0) rotate(-26deg);
            opacity: 0.14;
        }

        40% {
            transform: translate3d(10%, -4%, 0) rotate(-24deg);
            opacity: 0.3;
        }

        75% {
            transform: translate3d(-8%, 3%, 0) rotate(-28deg);
            opacity: 0.2;
        }

        100% {
            transform: translate3d(0, 0, 0) rotate(-26deg);
            opacity: 0.14;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .auth-flare-orb,
        .auth-flare-beam {
            animation: none;
        }
    }
</style>
