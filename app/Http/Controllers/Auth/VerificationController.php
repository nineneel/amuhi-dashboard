<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

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

        try {
            $request->user()->sendEmailVerificationNotification();
        } catch (TransportExceptionInterface $exception) {
            Log::warning('Email verification resend failed.', [
                'user_id' => $request->user()?->id,
                'email' => $request->user()?->email,
                'exception' => $exception->getMessage(),
            ]);

            return back()->with('mail_error', 'We could not send the verification email. Please try again later.');
        }

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
