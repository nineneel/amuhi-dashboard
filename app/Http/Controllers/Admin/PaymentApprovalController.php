<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PaymentApprovalStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ApprovePaymentRequest;
use App\Http\Requests\Admin\RejectPaymentRequest;
use App\InvoiceStatus;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\PaymentApproval;
use App\PaymentStatus;
use App\SubscriptionStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PaymentApprovalController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $status = (string) $request->query('status', '');
        $perPage = (int) $request->query('per_page', 15);

        $latestApprovalStatusSubQuery = PaymentApproval::query()
            ->select('new_approval_status')
            ->whereColumn('payment_id', 'payments.id')
            ->latest()
            ->limit(1);

        $payments = Payment::query()
            ->with(['invoice.user', 'invoice.subscription.subscriptionPlan', 'latestApprovalHistory.actor', 'latestApprovalHistory.respondedBy'])
            ->addSelect(['latest_approval_status' => $latestApprovalStatusSubQuery])
            ->where('payment_method', 'manual')
            ->whereHas('latestApprovalHistory')
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($q) use ($search): void {
                    $q->where('gateway_transaction_id', 'like', "%{$search}%")
                        ->orWhereHas('invoice.user', function ($uq) use ($search): void {
                            $uq->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->when($status !== '', function ($query) use ($status): void {
                $query->whereHas('latestApprovalHistory', function ($historyQuery) use ($status): void {
                    $historyQuery->where('new_approval_status', $status);
                });
            })
            ->orderByRaw("CASE WHEN latest_approval_status = 'pending_review' THEN 0 ELSE 1 END")
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.payment-approvals.index', [
            'payments' => $payments,
        ]);
    }

    public function show(Payment $payment): View
    {
        $payment->load([
            'invoice.user.profile',
            'invoice.subscription.subscriptionPlan',
            'subscriptionPlan',
            'latestApprovalHistory.actor',
            'latestApprovalHistory.respondedBy',
            'approvalHistories.actor',
            'approvalHistories.respondedBy',
        ]);

        return view('admin.payment-approvals.show', [
            'payment' => $payment,
        ]);
    }

    public function approve(ApprovePaymentRequest $request, Payment $payment): RedirectResponse
    {
        if (! $payment->isPendingReview()) {
            return redirect()
                ->route('admin.payment-approvals.show', $payment)
                ->with('error', __('ui.admin.this_payment_cannot_be_approved'));
        }

        $admin = $request->user();

        DB::transaction(function () use ($payment, $admin, $request): void {
            $adminNotes = $request->validated('admin_notes');

            $payment->update([
                'status' => PaymentStatus::Success,
                'paid_at' => now(),
            ]);

            $payment->respondLatestApprovalHistory(
                newStatus: PaymentApprovalStatus::Approved,
                adminId: $admin->id,
                adminRole: $admin->role->value,
                notes: $adminNotes,
            );

            $user = $payment->invoice->user;
            $plan = $payment->subscriptionPlan;

            // Create or update subscription
            $subscription = $user->currentSubscription();
            if ($subscription) {
                $subscription->update([
                    'subscription_plan_id' => $plan->id,
                    'status' => SubscriptionStatus::Active,
                    'starts_at' => now(),
                    'ends_at' => $plan->duration_days ? now()->addDays($plan->duration_days) : null,
                ]);
            } else {
                $subscription = $user->subscriptions()->create([
                    'subscription_plan_id' => $plan->id,
                    'status' => SubscriptionStatus::Active,
                    'starts_at' => now(),
                    'ends_at' => $plan->duration_days ? now()->addDays($plan->duration_days) : null,
                ]);
            }

            // Link invoice to subscription
            $payment->invoice->update([
                'status' => InvoiceStatus::Paid,
                'subscription_id' => $subscription->id,
            ]);

            ActivityLog::query()->create([
                'user_id' => $admin->id,
                'action' => __('ui.admin.payment_approved_action'),
                'description' => __('ui.admin.payment_payment_id_for_invoice_invoice_number_was_approved', [
                    'payment_id' => $payment->id,
                    'invoice_number' => $payment->invoice->invoice_number,
                ]),
                'subject_type' => Payment::class,
                'subject_id' => $payment->id,
                'ip_address' => $request->ip(),
            ]);

            Notification::query()->create([
                'user_id' => $payment->invoice->user_id,
                'type' => 'payment_status',
                'title' => __('ui.admin.payment_approved'),
                'message' => __('ui.admin.your_payment_for_invoice_invoice_number_has_been_approved_your_subscription_is_now_active', [
                    'invoice_number' => $payment->invoice->invoice_number,
                ]),
                'sent_via' => 'app',
            ]);
        });

        return redirect()
            ->route('admin.payment-approvals.index')
            ->with('success', __('ui.admin.payment_approved_successfully'));
    }

    public function markInsufficient(RejectPaymentRequest $request, Payment $payment): RedirectResponse
    {
        if (! $payment->isPendingReview()) {
            return redirect()
                ->route('admin.payment-approvals.show', $payment)
                ->with('error', __('ui.admin.this_payment_status_cannot_be_changed'));
        }

        $admin = $request->user();

        DB::transaction(function () use ($payment, $admin, $request): void {
            $adminNotes = $request->validated('admin_notes');

            $payment->respondLatestApprovalHistory(
                newStatus: PaymentApprovalStatus::InsufficientNominal,
                adminId: $admin->id,
                adminRole: $admin->role->value,
                notes: $adminNotes,
            );

            ActivityLog::query()->create([
                'user_id' => $admin->id,
                'action' => __('ui.admin.payment_marked_insufficient'),
                'description' => __('ui.admin.payment_payment_id_for_invoice_invoice_number_was_marked_as_insufficient', [
                    'payment_id' => $payment->id,
                    'invoice_number' => $payment->invoice->invoice_number,
                ]),
                'subject_type' => Payment::class,
                'subject_id' => $payment->id,
                'ip_address' => $request->ip(),
            ]);

            Notification::query()->create([
                'user_id' => $payment->invoice->user_id,
                'type' => 'payment_status',
                'title' => __('ui.admin.insufficient_payment'),
                'message' => __('ui.admin.your_payment_for_invoice_invoice_number_has_insufficient_nominal_please_re_upload_with_the_correct_amount', [
                    'invoice_number' => $payment->invoice->invoice_number,
                ]),
                'sent_via' => 'app',
            ]);
        });

        return redirect()
            ->route('admin.payment-approvals.index')
            ->with('success', __('ui.admin.payment_marked_as_insufficient'));
    }

    public function reject(RejectPaymentRequest $request, Payment $payment): RedirectResponse
    {
        if (! $payment->isPendingReview()) {
            return redirect()
                ->route('admin.payment-approvals.show', $payment)
                ->with('error', __('ui.admin.this_payment_cannot_be_rejected'));
        }

        $admin = $request->user();

        DB::transaction(function () use ($payment, $admin, $request): void {
            $adminNotes = $request->validated('admin_notes');

            $payment->update([
                'status' => PaymentStatus::Failed,
            ]);

            $payment->respondLatestApprovalHistory(
                newStatus: PaymentApprovalStatus::Rejected,
                adminId: $admin->id,
                adminRole: $admin->role->value,
                notes: $adminNotes,
            );

            ActivityLog::query()->create([
                'user_id' => $admin->id,
                'action' => __('ui.admin.payment_rejected_action'),
                'description' => __('ui.admin.payment_payment_id_for_invoice_invoice_number_was_rejected', [
                    'payment_id' => $payment->id,
                    'invoice_number' => $payment->invoice->invoice_number,
                ]),
                'subject_type' => Payment::class,
                'subject_id' => $payment->id,
                'ip_address' => $request->ip(),
            ]);

            Notification::query()->create([
                'user_id' => $payment->invoice->user_id,
                'type' => 'payment_status',
                'title' => __('ui.admin.payment_rejected'),
                'message' => __('ui.admin.your_payment_for_invoice_invoice_number_has_been_rejected_please_re_upload_valid_proof', [
                    'invoice_number' => $payment->invoice->invoice_number,
                ]),
                'sent_via' => 'app',
            ]);
        });

        return redirect()
            ->route('admin.payment-approvals.index')
            ->with('success', __('ui.admin.payment_rejected_success'));
    }
}
