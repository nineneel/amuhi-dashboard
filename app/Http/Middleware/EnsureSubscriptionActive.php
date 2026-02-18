<?php

namespace App\Http\Middleware;

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

        $user->syncExpiredSubscriptions();

        if (! $user->canAccessPortalFeatures()) {
            return redirect()
                ->route('payments.show')
                ->with('warning', 'Subscription required. Please complete payment to access this section.');
        }

        return $next($request);
    }
}
