<?php

namespace App\Http\Middleware;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerified
{
    public function handle(Request $request, \Closure $next): Response
    {
        $user = $request->user();

        if ($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Your email address is not verified.'], Response::HTTP_FORBIDDEN)
                : redirect()->route('verification.notice');
        }

        return $next($request);
    }
}
