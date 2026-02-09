<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VerificationController extends Controller
{
    public function notice(Request $request): View|RedirectResponse
    {
        if ($request->user()?->hasVerifiedEmail()) {
            return $this->redirectAfterVerification($request->user());
        }

        return view('auth.verify-email');
    }

    public function verify(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()?->hasVerifiedEmail()) {
            return $this->redirectAfterVerification($request->user());
        }

        $request->fulfill();

        return $this->redirectAfterVerification($request->user())
            ->with('status', 'Email verified successfully.');
    }

    public function resend(Request $request): RedirectResponse
    {
        if ($request->user()?->hasVerifiedEmail()) {
            return $this->redirectAfterVerification($request->user());
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'Verification link sent.');
    }

    private function redirectAfterVerification(?User $user): RedirectResponse
    {
        if (! $user) {
            return redirect()->route('login');
        }

        if (! $user->hasActiveSubscription()) {
            return redirect()->route('payments.show');
        }

        return redirect()->route('dashboard');
    }
}
