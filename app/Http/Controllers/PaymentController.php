<?php

namespace App\Http\Controllers;

use App\Enums\PaymentApprovalStatus;
use App\Enums\Role;
use App\Http\Requests\PaymentProofUploadRequest;
use App\Http\Requests\SubscriptionStatusUpdateRequest;
use App\InvoiceStatus;
use App\Models\ActivityLog;
use App\Models\Invoice;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\PaymentStatus;
use App\SubscriptionStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user();

        $user->syncExpiredSubscriptions();

        $currentSubscription = $user->currentSubscription();
        $shouldChargeRegistrationFee = ! $user->subscriptions()->exists();

        $annualPlan = SubscriptionPlan::query()
            ->where('name', 'Annual Membership')
            ->where('is_active', true)
            ->first();

        $registerPlan = SubscriptionPlan::query()
            ->where('name', 'Register as Member')
            ->where('is_active', true)
            ->first();

        $subscriptionPlans = SubscriptionPlan::query()
            ->where('is_active', true)
            ->where('duration_days', '>', 0)
            ->orderBy('price')
            ->get();

        if (! $annualPlan) {
            $annualPlan = $subscriptionPlans->first();
        }

        $registrationFeeAmount = $shouldChargeRegistrationFee ? (float) ($registerPlan?->price ?? 0) : 0.0;
        $totalAmount = (float) ($annualPlan?->price ?? 0) + $registrationFeeAmount;

        $pendingPayment = Payment::query()
            ->with(['invoice', 'latestApprovalHistory.respondedBy', 'approvalHistories.actor', 'approvalHistories.respondedBy'])
            ->whereHas('invoice', fn ($q) => $q->where('user_id', $user->id))
            ->whereHas('latestApprovalHistory', function ($query): void {
                $query->where('new_approval_status', '!=', PaymentApprovalStatus::Approved->value);
            })
            ->latest()
            ->first();

        return view('payments.show', [
            'user' => $user,
            'currentSubscription' => $currentSubscription,
            'annualPlan' => $annualPlan,
            'registerPlan' => $registerPlan,
            'subscriptionPlans' => $subscriptionPlans,
            'totalAmount' => $totalAmount,
            'shouldChargeRegistrationFee' => $shouldChargeRegistrationFee,
            'pendingPayment' => $pendingPayment,
        ]);
    }

    public function uploadProof(PaymentProofUploadRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();

        $user->syncExpiredSubscriptions();

        if ($user->hasActiveSubscription()) {
            return redirect()
                ->route('payments.show')
                ->with('warning', 'Your subscription is already active.');
        }

        $existingPendingPayment = Payment::query()
            ->whereHas('invoice', fn ($q) => $q->where('user_id', $user->id))
            ->whereHas('latestApprovalHistory', function ($query): void {
                $query->where('new_approval_status', PaymentApprovalStatus::PendingReview->value);
            })
            ->first();

        if ($existingPendingPayment) {
            return redirect()
                ->route('payments.show')
                ->with('warning', 'You already have a payment pending review.');
        }

        $shouldChargeRegistrationFee = ! $user->subscriptions()->exists();

        $plan = SubscriptionPlan::query()
            ->whereKey($data['plan_id'])
            ->where('is_active', true)
            ->where('duration_days', '>', 0)
            ->firstOrFail();

        $registerPlan = SubscriptionPlan::query()
            ->where('name', 'Register as Member')
            ->where('is_active', true)
            ->first();

        $registrationFeeAmount = $shouldChargeRegistrationFee ? (float) ($registerPlan?->price ?? 0) : 0.0;
        $totalAmount = (float) $plan->price + $registrationFeeAmount;

        $proofPath = $request->file('proof_image')->store('payment-proofs', 'public');

        DB::transaction(function () use ($user, $plan, $request, $totalAmount, $proofPath) {
            // Create invoice without subscription - subscription will be created on approval
            $invoice = Invoice::query()->create([
                'user_id' => $user->id,
                'subscription_id' => null,
                'invoice_number' => 'INV-'.now()->format('Ymd').'-'.Str::upper(Str::random(6)),
                'amount' => $totalAmount,
                'due_date' => now()->addDays(7)->toDateString(),
                'status' => InvoiceStatus::Pending->value,
            ]);

            $payment = Payment::query()->create([
                'invoice_id' => $invoice->id,
                'gateway_transaction_id' => 'MANUAL-'.Str::upper(Str::random(8)),
                'amount' => $invoice->amount,
                'status' => PaymentStatus::Pending->value,
                'payment_method' => 'manual',
                'subscription_plan_id' => $plan->id,
            ]);

            $payment->recordApprovalHistory(
                newStatus: PaymentApprovalStatus::PendingReview,
                proofImage: $proofPath,
                actorId: $user->id,
                actorRole: $user->role->value,
                notes: null,
            );

            ActivityLog::query()->create([
                'user_id' => $user->id,
                'action' => 'Payment proof uploaded',
                'description' => 'Payment proof uploaded for Invoice #'.$invoice->invoice_number,
                'subject_type' => Payment::class,
                'subject_id' => $payment->id,
                'ip_address' => $request->ip(),
            ]);

            $this->notifyAdmins($user, $invoice);
        });

        return redirect()->route('payments.show')
            ->with('success', 'Payment proof uploaded successfully. Please wait for admin review.');
    }

    public function reuploadProof(Request $request): RedirectResponse
    {
        $request->validate([
            'proof_image' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ], [
            'proof_image.required' => 'Please upload your payment proof image.',
            'proof_image.image' => 'The file must be an image.',
            'proof_image.mimes' => 'The image must be a JPG, JPEG, or PNG file.',
            'proof_image.max' => 'The image must not exceed 5MB.',
        ]);

        $user = $request->user();

        $payment = Payment::query()
            ->whereHas('invoice', fn ($q) => $q->where('user_id', $user->id))
            ->whereHas('latestApprovalHistory', function ($query): void {
                $query->whereIn('new_approval_status', [
                    PaymentApprovalStatus::Rejected->value,
                    PaymentApprovalStatus::InsufficientNominal->value,
                ]);
            })
            ->latest()
            ->first();

        if (! $payment) {
            return redirect()
                ->route('payments.show')
                ->with('error', 'No payment found that can be re-uploaded.');
        }

        $proofPath = $request->file('proof_image')->store('payment-proofs', 'public');
        $previousStatus = $payment->currentApprovalStatus();

        $payment->recordApprovalHistory(
            newStatus: PaymentApprovalStatus::PendingReview,
            previousStatus: $previousStatus,
            proofImage: $proofPath,
            actorId: $user->id,
            actorRole: $user->role->value,
            notes: null,
        );

        ActivityLog::query()->create([
            'user_id' => $user->id,
            'action' => 'Payment proof re-uploaded',
            'description' => 'Payment proof re-uploaded for Invoice #'.$payment->invoice->invoice_number,
            'subject_type' => Payment::class,
            'subject_id' => $payment->id,
            'ip_address' => $request->ip(),
        ]);

        $this->notifyAdmins($user, $payment->invoice);

        return redirect()->route('payments.show')
            ->with('success', 'Payment proof re-uploaded successfully. Please wait for admin review.');
    }

    private function notifyAdmins(User $user, Invoice $invoice): void
    {
        $admins = User::query()
            ->whereIn('role', [Role::Admin, Role::SuperAdmin])
            ->get();

        foreach ($admins as $admin) {
            Notification::query()->create([
                'user_id' => $admin->id,
                'type' => 'payment_approval',
                'title' => 'New Payment Proof Uploaded',
                'message' => "User {$user->name} has uploaded payment proof for Invoice #{$invoice->invoice_number}.",
                'sent_via' => 'app',
            ]);
        }
    }

    public function updateStatus(SubscriptionStatusUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();

        $plan = SubscriptionPlan::query()
            ->whereKey($data['plan_id'])
            ->where('is_active', true)
            ->where('duration_days', '>', 0)
            ->firstOrFail();

        $status = SubscriptionStatus::from($data['status']);
        $dates = $this->resolveSubscriptionDates($plan, $status);
        $subscription = $user->currentSubscription();

        $payload = [
            'subscription_plan_id' => $plan->id,
            'status' => $status,
            'starts_at' => $dates['starts_at'],
            'ends_at' => $dates['ends_at'],
        ];

        if ($subscription) {
            $subscription->update($payload);
        } else {
            $user->subscriptions()->create($payload);
        }

        return redirect()->route('payments.show')
            ->with('success', 'Subscription status updated. (Demo Mode)');
    }

    /**
     * @return array{starts_at: \Illuminate\Support\Carbon|null, ends_at: \Illuminate\Support\Carbon|null}
     */
    private function resolveSubscriptionDates(SubscriptionPlan $plan, SubscriptionStatus $status): array
    {
        $now = now();
        $startsAt = null;
        $endsAt = null;

        if ($status === SubscriptionStatus::Active) {
            $startsAt = $now->copy();
            $endsAt = $plan->duration_days ? $now->copy()->addDays($plan->duration_days) : null;
        } elseif ($status === SubscriptionStatus::Expired) {
            $endsAt = $now->copy()->subDay();
            $startsAt = $plan->duration_days ? $endsAt->copy()->subDays($plan->duration_days) : null;
        } elseif ($status === SubscriptionStatus::Pending) {
            $startsAt = $now->copy();
        }

        return [
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
        ];
    }
}
