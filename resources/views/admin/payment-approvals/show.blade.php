@php
    use App\Enums\PaymentApprovalStatus;
@endphp

@extends('admin.layouts.admin')

@section('content')
    @php
        $approvalStatus = $payment->currentApprovalStatus();
        $badgeType = match ($approvalStatus) {
            PaymentApprovalStatus::Approved => 'success',
            PaymentApprovalStatus::PendingReview, PaymentApprovalStatus::InsufficientNominal => 'warning',
            PaymentApprovalStatus::Rejected => 'danger',
            default => 'neutral',
        };
        $isPendingReview = $payment->isPendingReview();
        $proofImage = $payment->latestProofImagePath();
        $latestApprovalHistory = $payment->approvalHistories->first();
    @endphp

    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-title-sm font-semibold text-gray-800 dark:text-white/90">Payment Approval</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $payment->gateway_transaction_id ?? 'Payment #'.$payment->id }}</p>
            </div>
            <a href="{{ route('admin.payment-approvals.index') }}"
                class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-white/[0.03]">
                Back to List
            </a>
        </div>

        @if (session('success'))
            <div class="rounded-lg border border-success-200 bg-success-50 p-4 dark:border-success-800 dark:bg-success-900/20">
                <p class="text-sm text-success-700 dark:text-success-400">{{ session('success') }}</p>
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-lg border border-error-200 bg-error-50 p-4 dark:border-error-800 dark:bg-error-900/20">
                <p class="text-sm text-error-700 dark:text-error-400">{{ session('error') }}</p>
            </div>
        @endif

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            @component('admin.components.form-card', ['title' => 'Payment Details'])
                <div class="space-y-4">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Transaction ID</p>
                        <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $payment->gateway_transaction_id ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Amount</p>
                        <p class="text-lg font-bold text-brand-500">Rp {{ number_format((float) $payment->amount, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Selected Plan</p>
                        <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $payment->subscriptionPlan?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Created</p>
                        <p class="text-sm text-gray-800 dark:text-white/90">{{ $payment->created_at?->format('d M Y H:i') }}</p>
                    </div>
                </div>
            @endcomponent

            @component('admin.components.form-card', ['title' => 'Status'])
                <div class="space-y-4">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Approval Status</p>
                        <div class="mt-1">
                            @include('admin.components.badge', ['type' => $badgeType, 'text' => $approvalStatus?->label() ?? 'Unknown'])
                        </div>
                    </div>
                    @if ($latestApprovalHistory?->responded_at)
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Reviewed At</p>
                            <p class="text-sm text-gray-800 dark:text-white/90">{{ $latestApprovalHistory->responded_at?->format('d M Y H:i') }}</p>
                        </div>
                    @endif
                    @if ($latestApprovalHistory?->respondedBy)
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Reviewed By</p>
                            <p class="text-sm text-gray-800 dark:text-white/90">{{ $latestApprovalHistory->respondedBy->name }}</p>
                        </div>
                    @endif
                </div>
            @endcomponent

            @component('admin.components.form-card', ['title' => 'Member'])
                <div class="space-y-4">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Name</p>
                        <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $payment->invoice?->user?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Email</p>
                        <p class="text-sm text-gray-800 dark:text-white/90">{{ $payment->invoice?->user?->email ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Invoice</p>
                        <p class="text-sm text-gray-800 dark:text-white/90">{{ $payment->invoice?->invoice_number ?? '-' }}</p>
                    </div>
                </div>
            @endcomponent
        </div>

        {{-- Payment Proof --}}
        @component('admin.components.form-card', ['title' => 'Payment Proof'])
            @if ($proofImage)
                <div class="space-y-4">
                    <a href="{{ asset('storage/' . $proofImage) }}" target="_blank"
                        class="inline-block overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
                        <img src="{{ asset('storage/' . $proofImage) }}" alt="Payment proof"
                            class="max-h-96 w-auto object-contain" />
                    </a>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Click image to view full size</p>
                </div>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400">No proof image uploaded.</p>
            @endif
        @endcomponent

        {{-- Actions --}}
        @if ($isPendingReview)
            <div x-data="{ showApproveModal: false, showInsufficientModal: false, showRejectModal: false }">
                @component('admin.components.form-card', ['title' => 'Actions'])
                    <div class="flex flex-wrap gap-3">
                        <x-ui.button variant="success" type="button" @click="showApproveModal = true">
                            Approve
                        </x-ui.button>
                        <x-ui.button variant="warning" type="button" @click="showInsufficientModal = true">
                            Insufficient
                        </x-ui.button>
                        <x-ui.button variant="error" type="button" @click="showRejectModal = true">
                            Reject
                        </x-ui.button>
                    </div>
                @endcomponent

                {{-- Approve Modal --}}
                <x-ui.modal class="max-w-md" x-model="showApproveModal">
                    <div class="p-6">
                        <div class="flex items-center gap-3">
                            <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-success-100 text-success-600 dark:bg-success-500/20 dark:text-success-400">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </span>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Approve Payment</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">This will activate the user's subscription</p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('admin.payment-approvals.approve', $payment) }}" class="mt-6">
                            @csrf
                            <div>
                                <label for="approve_notes" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Notes (optional)</label>
                                <textarea name="admin_notes" id="approve_notes" rows="3"
                                    class="block w-full rounded-lg border-gray-300 px-4 py-3 shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white sm:text-sm"
                                    placeholder="Add any notes for the user..."></textarea>
                            </div>

                            <div class="mt-6 flex gap-3">
                                <x-ui.button variant="outline" type="button" className="flex-1" @click="showApproveModal = false">
                                    Cancel
                                </x-ui.button>
                                <x-ui.button variant="success" type="submit" className="flex-1">
                                    Approve
                                </x-ui.button>
                            </div>
                        </form>
                    </div>
                </x-ui.modal>

                {{-- Insufficient Modal --}}
                <x-ui.modal class="max-w-md" x-model="showInsufficientModal">
                    <div class="p-6">
                        <div class="flex items-center gap-3">
                            <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-warning-100 text-warning-600 dark:bg-warning-500/20 dark:text-warning-400">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </span>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Insufficient Nominal</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">User can re-upload with correct amount</p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('admin.payment-approvals.insufficient', $payment) }}" class="mt-6">
                            @csrf
                            <div>
                                <label for="insufficient_notes" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Reason <span class="text-error-500">*</span></label>
                                <textarea name="admin_notes" id="insufficient_notes" rows="3" required
                                    class="block w-full rounded-lg border-gray-300 px-4 py-3 shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white sm:text-sm"
                                    placeholder="Explain why the payment amount is insufficient..."></textarea>
                                @error('admin_notes')
                                    <p class="mt-2 text-sm text-error-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mt-6 flex gap-3">
                                <x-ui.button variant="outline" type="button" className="flex-1" @click="showInsufficientModal = false">
                                    Cancel
                                </x-ui.button>
                                <x-ui.button variant="warning" type="submit" className="flex-1">
                                    Mark Insufficient
                                </x-ui.button>
                            </div>
                        </form>
                    </div>
                </x-ui.modal>

                {{-- Reject Modal --}}
                <x-ui.modal class="max-w-md" x-model="showRejectModal">
                    <div class="p-6">
                        <div class="flex items-center gap-3">
                            <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-error-100 text-error-600 dark:bg-error-500/20 dark:text-error-400">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </span>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Reject Payment</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">User can re-upload with valid proof</p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('admin.payment-approvals.reject', $payment) }}" class="mt-6">
                            @csrf
                            <div>
                                <label for="reject_notes" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Reason <span class="text-error-500">*</span></label>
                                <textarea name="admin_notes" id="reject_notes" rows="3" required
                                    class="block w-full rounded-lg border-gray-300 px-4 py-3 shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white sm:text-sm"
                                    placeholder="Explain why the payment is being rejected..."></textarea>
                                @error('admin_notes')
                                    <p class="mt-2 text-sm text-error-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mt-6 flex gap-3">
                                <x-ui.button variant="outline" type="button" className="flex-1" @click="showRejectModal = false">
                                    Cancel
                                </x-ui.button>
                                <x-ui.button variant="error" type="submit" className="flex-1">
                                    Reject
                                </x-ui.button>
                            </div>
                        </form>
                    </div>
                </x-ui.modal>
            </div>
        @endif

        @component('admin.components.form-card', ['title' => 'Approval History'])
            @if ($payment->approvalHistories->isEmpty())
                <p class="text-sm text-gray-500 dark:text-gray-400">No approval history recorded yet.</p>
            @else
                <div class="w-full overflow-hidden">
                    <div class="custom-scrollbar w-full overflow-x-auto">
                        <table class="min-w-[1100px] w-full">
                            <thead>
                                <tr class="border-b border-gray-100 dark:border-gray-800">
                                    <th class="px-4 py-3 text-left text-xs font-medium whitespace-nowrap text-gray-500 dark:text-gray-400">Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium whitespace-nowrap text-gray-500 dark:text-gray-400">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium whitespace-nowrap text-gray-500 dark:text-gray-400">Type</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium whitespace-nowrap text-gray-500 dark:text-gray-400">From</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium whitespace-nowrap text-gray-500 dark:text-gray-400">By</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium whitespace-nowrap text-gray-500 dark:text-gray-400">Notes</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium whitespace-nowrap text-gray-500 dark:text-gray-400">Proof</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($payment->approvalHistories as $history)
                                    @php
                                        $historyBadgeType = match ($history->new_approval_status) {
                                            PaymentApprovalStatus::Approved => 'success',
                                            PaymentApprovalStatus::PendingReview, PaymentApprovalStatus::InsufficientNominal => 'warning',
                                            PaymentApprovalStatus::Rejected => 'danger',
                                            default => 'neutral',
                                        };
                                        $isReviewed = $history->responded_by_id !== null;
                                        $historyRowClass = match (true) {
                                            $isReviewed => 'bg-brand-50/30 dark:bg-brand-900/10',
                                            default => 'bg-transparent',
                                        };
                                        $actorLabel = match (true) {
                                            $isReviewed => 'Reviewed',
                                            default => 'Awaiting Review',
                                        };
                                        $actorBadgeType = match (true) {
                                            $isReviewed => 'info',
                                            default => 'warning',
                                        };
                                    @endphp
                                    <tr class="border-b border-gray-100 dark:border-gray-800 {{ $historyRowClass }}">
                                        <td class="px-4 py-3 text-sm whitespace-nowrap text-gray-700 dark:text-gray-300">{{ $history->created_at?->format('d M Y H:i') }}</td>
                                        <td class="px-4 py-3">
                                            @include('admin.components.badge', ['type' => $historyBadgeType, 'text' => $history->new_approval_status?->label() ?? 'Unknown'])
                                        </td>
                                        <td class="px-4 py-3">
                                            @include('admin.components.badge', ['type' => $actorBadgeType, 'text' => $actorLabel])
                                        </td>
                                        <td class="px-4 py-3 text-sm whitespace-nowrap text-gray-700 dark:text-gray-300">{{ $history->previous_approval_status?->label() ?? 'Initial Submission' }}</td>
                                        <td class="px-4 py-3 text-sm whitespace-nowrap text-gray-700 dark:text-gray-300">{{ $history->respondedBy?->name ?? '-' }}</td>
                                        <td class="max-w-[320px] px-4 py-3 text-sm whitespace-normal break-words text-gray-700 dark:text-gray-300">{{ $history->notes ?: '-' }}</td>
                                        <td class="px-4 py-3">
                                            @if ($history->proof_image)
                                                <a href="{{ asset('storage/' . $history->proof_image) }}" target="_blank"
                                                    class="inline-flex rounded-lg border border-gray-200 px-2 py-1 text-xs text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/[0.03]">
                                                    View
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
        @endcomponent

    </div>
@endsection
