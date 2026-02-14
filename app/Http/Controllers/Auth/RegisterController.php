<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use App\Notifications\CompleteRegistrationNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class RegisterController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $user = DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                // User sets their password via the "complete registration" email link.
                'password' => Str::random(64),
            ]);

            $user->profile()->create([
                'member_type' => $data['member_type'],
                'company_name' => $data['company_name'] ?? null,
                'phone' => $data['phone'],
            ]);

            $user->settings()->create([
                'notification_email' => true,
                'notification_app' => true,
                'language' => 'id',
                'theme' => 'dark',
                'privacy_settings' => [],
            ]);

            return $user;
        });

        $mailError = null;

        try {
            $token = Password::broker()->createToken($user);
            $expiresInMinutes = (int) config('auth.passwords.users.expire', 60);
            $user->notify(new CompleteRegistrationNotification($token, $expiresInMinutes));
        } catch (TransportExceptionInterface $exception) {
            Log::warning('Complete registration email send failed.', [
                'user_id' => $user->id,
                'email' => $user->email,
                'exception' => $exception->getMessage(),
            ]);

            // Keep the flow simple: if we can't deliver the link, remove the new account so they can try again.
            $user->delete();

            $mailError = 'We could not send the email right now. Please try again later.';
        }

        $redirect = redirect()->route('register')
            ->with('status', 'We emailed you a link to complete your registration. Please check your inbox.');

        if ($mailError) {
            return redirect()->route('register')->with('mail_error', $mailError);
        }

        return $redirect;
    }
}
