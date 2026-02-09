<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentSimulationRequest;
use App\Http\Requests\SubscriptionStatusUpdateRequest;
use App\InvoiceStatus;
use App\Models\ActivityLog;
use App\Models\Invoice;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\SubscriptionPlan;
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
        $plans = SubscriptionPlan::query()
            ->where('is_active', true)
            ->orderBy('price')
            ->get();

        $currentSubscription = $user->currentSubscription();
        $selectedPlanId = $currentSubscription?->subscription_plan_id ?? $plans->first()?->id;

        return view('payments.show', [
            'user' => $user,
            'plans' => $plans,
            'currentSubscription' => $currentSubscription,
            'selectedPlanId' => $selectedPlanId,
        ]);
    }

    public function simulate(PaymentSimulationRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();

        $plan = SubscriptionPlan::query()
            ->whereKey($data['plan_id'])
            ->where('is_active', true)
            ->firstOrFail();

        $status = SubscriptionStatus::Active;
        $dates = $this->resolveSubscriptionDates($plan, $status);
        $subscription = $user->currentSubscription();

        DB::transaction(function () use ($user, $plan, $status, $dates, &$subscription, $request) {
            if ($subscription) {
                $subscription->update([
                    'subscription_plan_id' => $plan->id,
                    'status' => $status,
                    'starts_at' => $dates['starts_at'],
                    'ends_at' => $dates['ends_at'],
                ]);
            } else {
                $subscription = $user->subscriptions()->create([
                    'subscription_plan_id' => $plan->id,
                    'status' => $status,
                    'starts_at' => $dates['starts_at'],
                    'ends_at' => $dates['ends_at'],
                ]);
            }

            $invoice = Invoice::query()->create([
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'invoice_number' => 'INV-'.now()->format('Ymd').'-'.Str::upper(Str::random(6)),
                'amount' => $plan->price,
                'due_date' => now()->toDateString(),
                'status' => InvoiceStatus::Paid->value,
            ]);

            Payment::query()->create([
                'invoice_id' => $invoice->id,
                'gateway_transaction_id' => 'demo-pay-'.Str::upper(Str::random(8)),
                'amount' => $invoice->amount,
                'status' => PaymentStatus::Success->value,
                'paid_at' => now(),
            ]);

            ActivityLog::query()->create([
                'user_id' => $user->id,
                'action' => 'Invoice paid',
                'description' => 'Invoice #'.$invoice->invoice_number.' was paid successfully.',
                'subject_type' => Invoice::class,
                'subject_id' => $invoice->id,
                'ip_address' => $request->ip(),
            ]);

            Notification::query()->create([
                'user_id' => $user->id,
                'type' => 'invoice',
                'title' => 'Payment received',
                'message' => 'Invoice #'.$invoice->invoice_number.' has been paid.',
                'sent_via' => 'app',
            ]);
        });

        return redirect()->route('dashboard')
            ->with('success', 'Payment successful! (Demo Mode)');
    }

    public function updateStatus(SubscriptionStatusUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();

        $plan = SubscriptionPlan::query()
            ->whereKey($data['plan_id'])
            ->where('is_active', true)
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
