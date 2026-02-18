@extends('layouts.fullscreen-layout')

@section('content')
    @php
        use App\Enums\PaymentApprovalStatus;

        $profile = $user->profile;
        $profilePhoto = $profile?->photo ? asset('storage/' . $profile->photo) : null;
        $userInitial = strtoupper(substr($user->name ?: 'U', 0, 1));

        $subscriptionStatus = $currentSubscription?->status?->value ?? \App\SubscriptionStatus::Unpaid->value;
        $isActiveSubscription =
            $subscriptionStatus === \App\SubscriptionStatus::Active->value &&
            ($currentSubscription?->ends_at === null || $currentSubscription->ends_at->isFuture());

        $badgeColorMap = [
            'active' => 'success',
            'pending' => 'warning',
            'expired' => 'warning',
            'unpaid' => 'error',
        ];
        $badgeColor = $badgeColorMap[$subscriptionStatus] ?? 'error';

        $subscriptionStatusKey = 'ui.subscriptions.status.' . $subscriptionStatus;
        $subscriptionStatusLabel = trans()->has($subscriptionStatusKey)
            ? __($subscriptionStatusKey)
            : ucfirst($subscriptionStatus);

        $hasPendingPayment = isset($pendingPayment) && $pendingPayment !== null;
        $canReupload = $hasPendingPayment && $pendingPayment->canReupload();
        $approvalHistories = $hasPendingPayment ? $pendingPayment->approvalHistories : collect();
        $latestProofImage = $hasPendingPayment ? $pendingPayment->latestProofImagePath() : null;
    @endphp

    <div class="mx-auto w-full max-w-4xl p-4 md:p-8">
        {{-- User info card --}}
        <div class="mb-6 rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-4">
                    <span
                        class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-full bg-brand-50 text-sm font-semibold text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
                        @if ($profilePhoto)
                            <img src="{{ $profilePhoto }}" alt="Profile photo" class="h-full w-full object-cover" />
                        @else
                            {{ $userInitial }}
                        @endif
                    </span>

                    <div>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $user->name }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $user->email }}</p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('profile.index') }}">
                        <x-ui.button variant="outline" size="sm">
                            {{ __('ui.dashboard.view_profile') }}
                        </x-ui.button>
                    </a>
                </div>
            </div>
        </div>

        {{-- Page title --}}
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ __('ui.payments.title') }}</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('ui.payments.subtitle') }}
                    {{ $shouldChargeRegistrationFee ? __('ui.payments.one_time_plus_annual') : __('ui.payments.annual_renewal') }}
                </p>
            </div>
        </div>

        {{-- Flash messages --}}
        @if (session('success'))
            <x-ui.alert variant="success" :title="__('ui.common.success')" :message="session('success')" class="mb-6" />
        @endif

        @if (session('warning'))
            <x-ui.alert variant="warning" :title="__('ui.common.notice')" :message="session('warning')" class="mb-6" />
        @endif

        @if ($errors->any())
            <x-ui.alert variant="error" :title="__('ui.payments.payment_error')" :message="$errors->first()" class="mb-6" />
        @endif

        @if (!$isActiveSubscription && $hasPendingPayment)
            <div class="mb-6 border-t border-gray-200 pt-6 dark:border-gray-800">
                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="flex items-start gap-4">
                        @php
                            $approvalStatus = $pendingPayment->currentApprovalStatus();
                            $needsAttention = in_array($approvalStatus, [PaymentApprovalStatus::InsufficientNominal, PaymentApprovalStatus::Rejected], true);
                            $statusIcon = match ($approvalStatus) {
                                PaymentApprovalStatus::PendingReview => '<svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
                                PaymentApprovalStatus::InsufficientNominal => '<svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>',
                                PaymentApprovalStatus::Rejected => '<svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
                                default => '<svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
                            };
                            $statusBgColor = match ($approvalStatus) {
                                PaymentApprovalStatus::PendingReview => 'bg-warning-50 text-warning-500 dark:bg-warning-500/10 dark:text-warning-400',
                                PaymentApprovalStatus::InsufficientNominal => 'bg-warning-50 text-warning-500 dark:bg-warning-500/10 dark:text-warning-400',
                                PaymentApprovalStatus::Rejected => 'bg-error-50 text-error-500 dark:bg-error-500/10 dark:text-error-400',
                                default => 'bg-success-50 text-success-500 dark:bg-success-500/10 dark:text-success-400',
                            };
                            $statusBadgeClass = match ($approvalStatus) {
                                PaymentApprovalStatus::PendingReview => 'bg-warning-100 text-warning-800 ring-warning-200 dark:bg-warning-500/20 dark:text-warning-300 dark:ring-warning-500/30',
                                PaymentApprovalStatus::InsufficientNominal => 'bg-warning-100 text-warning-800 ring-warning-200 dark:bg-warning-500/20 dark:text-warning-300 dark:ring-warning-500/30',
                                PaymentApprovalStatus::Rejected => 'bg-error-100 text-error-800 ring-error-200 dark:bg-error-500/20 dark:text-error-300 dark:ring-error-500/30',
                                default => 'bg-success-100 text-success-800 ring-success-200 dark:bg-success-500/20 dark:text-success-300 dark:ring-success-500/30',
                            };
                            $latestApprovalHistory = $approvalHistories->first();
                            $latestAdminNote = $latestApprovalHistory?->notes;
                        @endphp
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl {{ $statusBgColor }}">
                            {!! $statusIcon !!}
                        </span>
                        <div class="flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="font-semibold text-gray-800 text-theme-xl dark:text-white/90">
                                    {{ __('ui.payments.payment_status') }}
                                </h3>
                                <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-sm font-semibold ring-1 {{ $statusBadgeClass }}">
                                    @if ($needsAttention)
                                        <span class="relative inline-flex h-2.5 w-2.5">
                                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-current opacity-70"></span>
                                            <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-current"></span>
                                        </span>
                                    @endif
                                    {{ $approvalStatus?->label() ?? '-' }}
                                </span>
                            </div>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                {{ __('ui.payments.uploaded_on') }} {{ $pendingPayment->created_at->format('d M Y H:i') }}
                            </p>
                        </div>
                    </div>

                    @if ($latestProofImage || $canReupload)
                        <div class="mt-5">
                            @if ($latestProofImage || !empty($latestAdminNote))
                                <div class="w-full">
                                    @if (!empty($latestAdminNote))
                                        <div class="mb-3 border-t border-gray-200 pt-3 dark:border-gray-700">
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('ui.payments.admin_notes') }}</p>
                                            <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">{{ $latestAdminNote }}</p>
                                        </div>
                                    @endif

                                    @if ($latestProofImage)
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('ui.payments.proof_image') }}</p>
                                        <a href="{{ asset('storage/' . $latestProofImage) }}" target="_blank"
                                            class="mt-2 inline-block overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
                                            <img src="{{ asset('storage/' . $latestProofImage) }}" alt="Payment proof"
                                                class="h-36 w-28 object-cover transition hover:scale-105" />
                                        </a>
                                    @endif

                                    @if ($needsAttention)
                                        <x-ui.alert variant="warning"
                                            message="{{ __('ui.payments.reupload_instruction') }}"
                                            class="mt-2 w-full py-2 [&>div]:items-center [&_p]:text-xs [&_p]:leading-5 [&_p]:whitespace-normal sm:[&_p]:whitespace-nowrap" />
                                    @endif
                                </div>
                            @endif

                            @if ($canReupload)
                                <div x-data="{ showReuploadModal: false }" class="mt-4 flex justify-end">
                                    <x-ui.button variant="primary" type="button" className="w-full sm:w-auto" @click="showReuploadModal = true">
                                        {{ __('ui.payments.reupload_proof') }}
                                    </x-ui.button>

                                    <x-ui.modal class="max-w-lg" x-model="showReuploadModal">
                                        <div class="p-6 sm:p-8">
                                            <h3 class="text-xl font-semibold text-gray-800 dark:text-white/90">
                                                {{ __('ui.payments.reupload_proof') }}
                                            </h3>
                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                                {{ __('ui.payments.reupload_instruction') }}
                                            </p>

                                            <form method="POST" action="{{ route('payments.reupload-proof') }}" enctype="multipart/form-data" class="mt-6">
                                                @csrf

                                                <div x-data="{ fileName: '', previewUrl: null }">
                                                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                        {{ __('ui.payments.upload_proof') }}
                                                    </label>
                                                    <div class="relative">
                                                        <input type="file" name="proof_image" accept="image/jpeg,image/png,image/jpg"
                                                            class="hidden" id="reupload_proof_image"
                                                            @change="fileName = $event.target.files[0]?.name || ''; previewUrl = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null">
                                                        <label for="reupload_proof_image"
                                                            class="flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 p-6 transition hover:border-brand-500 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:hover:border-brand-500 dark:hover:bg-gray-700">
                                                            <template x-if="previewUrl">
                                                                <img :src="previewUrl" class="mb-3 h-32 w-auto rounded-lg object-cover" />
                                                            </template>
                                                            <template x-if="!previewUrl">
                                                                <svg class="mb-3 h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                                </svg>
                                                            </template>
                                                            <span class="text-sm text-gray-600 dark:text-gray-400" x-text="fileName || '{{ __('ui.payments.click_to_upload') }}'"></span>
                                                            <span class="mt-1 text-xs text-gray-500 dark:text-gray-500">JPG, JPEG, PNG (max 5MB)</span>
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="mt-6 flex gap-3">
                                                    <x-ui.button variant="outline" type="button" className="flex-1" @click="showReuploadModal = false">
                                                        {{ __('ui.common.cancel') }}
                                                    </x-ui.button>
                                                    <x-ui.button variant="primary" type="submit" className="flex-1">
                                                        {{ __('ui.payments.submit_proof') }}
                                                    </x-ui.button>
                                                </div>
                                            </form>
                                        </div>
                                    </x-ui.modal>
                                </div>
                            @endif
                        </div>
                    @endif

                    @if ($approvalHistories->isNotEmpty())
                        <div class="mt-6 border-t border-gray-200 pt-6 dark:border-gray-700">
                            <h4 class="font-semibold text-gray-800 dark:text-white/90">
                                {{ __('ui.payments.approval_history') }}
                            </h4>
                            <div class="custom-scrollbar mt-4 max-w-full overflow-x-auto">
                                <table class="w-full min-w-[1100px]">
                                    <thead>
                                        <tr class="border-b border-gray-100 dark:border-gray-800">
                                            <th class="px-4 py-3 text-left text-xs font-medium whitespace-nowrap text-gray-500 dark:text-gray-400">Date</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium whitespace-nowrap text-gray-500 dark:text-gray-400">Status</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium whitespace-nowrap text-gray-500 dark:text-gray-400">Type</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium whitespace-nowrap text-gray-500 dark:text-gray-400">{{ __('ui.payments.previous_status') }}</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium whitespace-nowrap text-gray-500 dark:text-gray-400">{{ __('ui.payments.reviewed_by') }}</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium whitespace-nowrap text-gray-500 dark:text-gray-400">{{ __('ui.payments.admin_notes') }}</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium whitespace-nowrap text-gray-500 dark:text-gray-400">Proof</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($approvalHistories as $history)
                                            @php
                                                $isResponded = $history->responded_by_id !== null;
                                                $historyRowClass = match (true) {
                                                    $isResponded => 'bg-brand-50/30 dark:bg-brand-900/10',
                                                    default => 'bg-transparent',
                                                };
                                                $actorLabel = match (true) {
                                                    $isResponded => __('ui.payments.history_actor_admin'),
                                                    default => __('ui.payments.history_actor_user'),
                                                };
                                                $actorBadgeColor = match (true) {
                                                    $isResponded => 'info',
                                                    default => 'success',
                                                };
                                            @endphp
                                                        <tr class="border-b border-gray-100 dark:border-gray-800 {{ $historyRowClass }}">
                                                            <td class="px-4 py-3 text-sm whitespace-nowrap text-gray-700 dark:text-gray-300">{{ $history->created_at?->format('d M Y H:i') }}</td>
                                                            <td class="px-4 py-3">
                                                                <x-ui.badge :color="$history->new_approval_status?->badgeColor() ?? 'neutral'" variant="light" size="sm">
                                                                    {{ $history->new_approval_status?->label() ?? '-' }}
                                                    </x-ui.badge>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <x-ui.badge :color="$actorBadgeColor" variant="light" size="sm">
                                                        {{ $actorLabel }}
                                                    </x-ui.badge>
                                                </td>
                                                            <td class="px-4 py-3 text-sm whitespace-nowrap text-gray-700 dark:text-gray-300">{{ $history->previous_approval_status?->label() ?? __('ui.payments.initial_submission') }}</td>
                                                            <td class="px-4 py-3 text-sm whitespace-nowrap text-gray-700 dark:text-gray-300">{{ $history->respondedBy?->name ?? '-' }}</td>
                                                            <td class="px-4 py-3 text-sm whitespace-nowrap text-gray-700 dark:text-gray-300">
                                                                @if ($history->notes)
                                                                    {{ $history->notes }}
                                                                @else
                                                        -
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3">
                                                    @if ($history->proof_image)
                                                        <a href="{{ asset('storage/' . $history->proof_image) }}" target="_blank"
                                                            class="inline-flex rounded-lg border border-gray-200 px-2 py-1 text-xs text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/[0.03]">
                                                            {{ __('ui.payments.view_proof') }}
                                                        </a>
                                                    @else
                                                        <span class="text-xs text-gray-500 dark:text-gray-400">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-12">
            {{-- Main content --}}
            <div class="space-y-6 lg:col-span-8">
                @if (!$isActiveSubscription)
                    {{-- Membership package --}}
                    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                        @if ($shouldChargeRegistrationFee)
                            <div>
                                <span class="block font-semibold text-gray-800 text-theme-xl dark:text-white/90">
                                    {{ $registerPlan?->name ?? __('ui.payments.register_plan_default') }}
                                </span>
                                <h2 class="mt-1 font-bold text-gray-800 text-title-md dark:text-white/90">
                                    Rp {{ number_format((float) ($registerPlan?->price ?? 0), 0, ',', '.') }}
                                </h2>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $registerPlan?->description ?? __('ui.payments.register_plan_desc_default') }}
                                </p>
                            </div>

                            <div class="my-6 h-px w-full bg-gray-200 dark:bg-gray-800"></div>
                        @endif

                        <div>
                            <span class="block font-semibold text-gray-800 text-theme-xl dark:text-white/90">
                                {{ $annualPlan?->name ?? __('ui.payments.annual_plan_default') }}
                            </span>
                            <h2 class="mt-1 font-bold text-gray-800 text-title-md dark:text-white/90">
                                Rp {{ number_format((float) ($annualPlan?->price ?? 0), 0, ',', '.') }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                {{ $annualPlan?->description ?? __('ui.payments.annual_plan_desc_default') }}
                            </p>

                            @if (!empty($annualPlan?->features))
                                <ul class="mt-6 space-y-3">
                                    @foreach ($annualPlan->features as $feature)
                                        <li class="flex items-center gap-3 text-sm text-gray-500 dark:text-gray-400">
                                            <svg class="shrink-0 text-success-500" width="16" height="16"
                                                viewBox="0 0 16 16" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path d="M13.4017 4.35986L6.12166 11.6399L2.59833 8.11657"
                                                    stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg>
                                            <span>{{ $feature }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>

                    {{-- Order summary + Pay button --}}
                    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                        <h3 class="font-semibold text-gray-800 text-theme-xl dark:text-white/90">
                            {{ __('ui.payments.order_summary') }}
                        </h3>

                        <div class="mt-4 space-y-3">
                            @if ($shouldChargeRegistrationFee)
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">
                                        {{ $registerPlan?->name ?? __('ui.payments.register_plan_default') }}
                                    </span>
                                    <span class="font-medium text-gray-800 dark:text-white/90">
                                        Rp {{ number_format((float) ($registerPlan?->price ?? 0), 0, ',', '.') }}
                                    </span>
                                </div>
                            @endif

                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">
                                    {{ $annualPlan?->name ?? __('ui.payments.annual_plan_default') }}
                                </span>
                                <span class="font-medium text-gray-800 dark:text-white/90">
                                    Rp {{ number_format((float) ($annualPlan?->price ?? 0), 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        <div class="my-4 h-px w-full bg-gray-200 dark:bg-gray-800"></div>

                        <div class="flex items-center justify-between">
                            <span class="font-semibold text-gray-800 dark:text-white/90">
                                {{ __('ui.payments.total') }}
                            </span>
                            <span class="font-bold text-gray-800 text-title-sm dark:text-white/90">
                                Rp {{ number_format((float) $totalAmount, 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="mt-6" x-data="{ showPaymentModal: false }">
                            <x-ui.button variant="primary" type="button" className="w-full" :disabled="!$annualPlan || $hasPendingPayment"
                                @click="showPaymentModal = true">
                                {{ __('ui.payments.pay_now') }}
                            </x-ui.button>

                            {{-- Payment Modal --}}
                            <x-ui.modal class="max-w-lg" x-model="showPaymentModal">
                                <div class="p-6 sm:p-8">
                                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white/90">
                                        {{ __('ui.payments.bank_transfer') }}
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        {{ __('ui.payments.transfer_instruction') }}
                                    </p>

                                    {{-- Bank Details --}}
                                    <div class="mt-6 rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                                        <div class="space-y-3">
                                            <div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('ui.payments.bank_name') }}</p>
                                                <p class="text-sm font-medium text-gray-800 dark:text-white/90">Bank Mandiri</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('ui.payments.account_number') }}</p>
                                                <p class="text-sm font-medium text-gray-800 dark:text-white/90">006-00-1423369-0</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('ui.payments.account_name') }}</p>
                                                <p class="text-sm font-medium text-gray-800 dark:text-white/90">AMUHI</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('ui.payments.amount_to_transfer') }}</p>
                                                <p class="text-lg font-bold text-brand-500">
                                                    Rp {{ number_format((float) $totalAmount, 0, ',', '.') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Upload Form --}}
                                    <form method="POST" action="{{ route('payments.upload-proof') }}" enctype="multipart/form-data" class="mt-6">
                                        @csrf
                                        @if ($annualPlan)
                                            <input type="hidden" name="plan_id" value="{{ $annualPlan->id }}">
                                        @endif

                                        <div x-data="{ fileName: '', previewUrl: null }">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                {{ __('ui.payments.upload_proof') }}
                                            </label>
                                            <div class="relative">
                                                <input type="file" name="proof_image" accept="image/jpeg,image/png,image/jpg"
                                                    class="hidden" id="proof_image"
                                                    @change="fileName = $event.target.files[0]?.name || ''; previewUrl = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null">
                                                <label for="proof_image"
                                                    class="flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 p-6 transition hover:border-brand-500 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:hover:border-brand-500 dark:hover:bg-gray-700">
                                                    <template x-if="previewUrl">
                                                        <img :src="previewUrl" class="mb-3 h-32 w-auto rounded-lg object-cover" />
                                                    </template>
                                                    <template x-if="!previewUrl">
                                                        <svg class="mb-3 h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                        </svg>
                                                    </template>
                                                    <span class="text-sm text-gray-600 dark:text-gray-400" x-text="fileName || '{{ __('ui.payments.click_to_upload') }}'"></span>
                                                    <span class="mt-1 text-xs text-gray-500 dark:text-gray-500">JPG, JPEG, PNG (max 5MB)</span>
                                                </label>
                                            </div>
                                            @error('proof_image')
                                                <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="mt-6 flex gap-3">
                                            <x-ui.button variant="outline" type="button" className="flex-1" @click="showPaymentModal = false">
                                                {{ __('ui.common.cancel') }}
                                            </x-ui.button>
                                            <x-ui.button variant="primary" type="submit" className="flex-1">
                                                {{ __('ui.payments.submit_proof') }}
                                            </x-ui.button>
                                        </div>
                                    </form>
                                </div>
                            </x-ui.modal>
                        </div>
                    </div>

                @else
                    {{-- Active subscription state --}}
                    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                        <div class="flex items-start gap-4">
                            <span
                                class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-success-50 text-success-500 dark:bg-success-500/10 dark:text-success-400">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="2.5"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                            <div>
                                <h3 class="font-semibold text-gray-800 text-theme-xl dark:text-white/90">
                                    {{ __('ui.payments.subscription_active') }}
                                </h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('ui.payments.subscription_active_subtitle') }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                            <a href="{{ route('dashboard') }}">
                                <x-ui.button variant="primary" size="md">
                                    {{ __('ui.payments.go_to_dashboard') }}
                                </x-ui.button>
                            </a>
                            <a href="{{ route('invoices.index') }}">
                                <x-ui.button variant="outline" size="md">
                                    {{ __('ui.payments.view_invoices') }}
                                </x-ui.button>
                            </a>
                        </div>
                    </div>
                @endif

            </div>

            {{-- Sidebar: Current subscription status --}}
            <div class="space-y-6 lg:col-span-4">
                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                    <div>
                        <h3 class="font-semibold text-gray-800 text-theme-xl dark:text-white/90">
                            {{ __('ui.payments.current_subscription') }}
                        </h3>
                        <div class="mt-2">
                            <x-ui.badge :color="$badgeColor" variant="light" size="sm">
                                {{ $subscriptionStatusLabel }}
                            </x-ui.badge>
                        </div>
                    </div>

                    <div class="my-4 h-px w-full bg-gray-200 dark:bg-gray-800"></div>

                    <dl class="space-y-4">
                        <div>
                            <dt class="text-xs text-gray-500 dark:text-gray-400">{{ __('ui.payments.plan') }}</dt>
                            <dd class="text-sm font-medium text-gray-800 dark:text-white/90">
                                {{ $currentSubscription?->subscriptionPlan?->name ?? __('ui.common.none') }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500 dark:text-gray-400">{{ __('ui.payments.starts') }}</dt>
                            <dd class="text-sm font-medium text-gray-800 dark:text-white/90">
                                {{ optional($currentSubscription?->starts_at)->format('d M Y') ?? '-' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500 dark:text-gray-400">{{ __('ui.payments.ends') }}</dt>
                            <dd class="text-sm font-medium text-gray-800 dark:text-white/90">
                                {{ optional($currentSubscription?->ends_at)->format('d M Y') ?? '-' }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

    </div>
@endsection
