<?php

namespace App\Http\Middleware;

use App\SubscriptionStatus;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSubscriptionActive
{
    public function handle(Request $request, \Closure $next): Response
    {
        $user = $request->user();

        if ($user === null) {
            return redirect()->route('login');
        }

        $hasActiveSubscription = $user->subscriptions()
            ->where('status', SubscriptionStatus::Active->value)
            ->where(function ($query) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>', now());
            })
            ->exists();

        if (! $hasActiveSubscription) {
            return redirect()
                ->route('dashboard')
                ->with('warning', 'Subscription required. Please complete payment to access this section.');
        }

        return $next($request);
    }
}
