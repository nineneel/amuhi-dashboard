<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function __construct(private Redirector $redirector) {}

    public function handle(Request $request, \Closure $next, ?string ...$guards): Response
    {
        $guards = $guards === [] ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();

                if (
                    ($request->is('admin') || $request->is('admin/*'))
                    && $user !== null
                    && method_exists($user, 'isAdmin')
                    && $user->isAdmin()
                ) {
                    return $this->redirector->to(route('admin.dashboard'));
                }

                return $this->redirector->intended(route('dashboard'));
            }
        }

        return $next($request);
    }
}
