<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentSimulationRequest;
use App\Http\Requests\SubscriptionStatusUpdateRequest;
use App\Models\SubscriptionPlan;
use App\SubscriptionStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function show(): View
    {
        $user = auth()->user();
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

        if ($subscription) {
            $subscription->update([
                'subscription_plan_id' => $plan->id,
                'status' => $status,
                'starts_at' => $dates['starts_at'],
                'ends_at' => $dates['ends_at'],
            ]);
        } else {
            $user->subscriptions()->create([
                'subscription_plan_id' => $plan->id,
                'status' => $status,
                'starts_at' => $dates['starts_at'],
                'ends_at' => $dates['ends_at'],
            ]);
        }

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
