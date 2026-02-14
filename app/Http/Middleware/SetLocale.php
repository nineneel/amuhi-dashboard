<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            // Force Indonesian for guests (login/register/forgot password/etc),
            // even if an old session locale exists.
            $locale = 'id';
        } else {
            $locale = $request->user()?->settings?->language
                ?? $request->session()->get('locale')
                ?? config('app.locale');
        }

        if (in_array($locale, ['en', 'id'], true)) {
            app()->setLocale($locale);
            $request->session()->put('locale', $locale);
        } else {
            app()->setLocale('id');
            $request->session()->put('locale', 'id');
        }

        return $next($request);
    }
}
