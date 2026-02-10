<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
                'password' => $data['password'],
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
                'theme' => 'light',
                'privacy_settings' => [],
            ]);

            return $user;
        });

        $mailError = null;

        try {
            event(new Registered($user));
        } catch (TransportExceptionInterface $exception) {
            Log::warning('Email verification send failed during registration.', [
                'user_id' => $user->id,
                'email' => $user->email,
                'exception' => $exception->getMessage(),
            ]);

            $mailError = 'We could not send a verification email right now. You can resend it from this page.';
        }

        Auth::login($user);

        $redirect = redirect()->route('verification.notice');

        if ($mailError) {
            return $redirect->with('mail_error', $mailError);
        }

        return $redirect;
    }
}
