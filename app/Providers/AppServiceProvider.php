<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            $key = (string) ($request->user()?->id ?: $request->ip());

            return Limit::perMinute(60)->by($key);
        });

        RateLimiter::for('register', function (Request $request) {
            $ip = (string) $request->ip();
            $email = strtolower((string) $request->input('email', ''));
            $rateLimitResponse = fn () => response()->json([
                'message' => 'Too many requests. Please try again later.',
            ], 429);

            $byEmailKey = $email !== '' ? $ip.'|'.$email : $ip;

            return [
                // Generic per-IP throttle.
                Limit::perMinute(10)->by($ip)->response($rateLimitResponse),
                // Tighten bursts per email to reduce brute force / spam.
                Limit::perMinute(3)->by($byEmailKey)->response($rateLimitResponse),
            ];
        });
    }
}
