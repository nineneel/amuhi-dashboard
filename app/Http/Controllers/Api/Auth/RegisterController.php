<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RegisterRequest;
use App\Models\User;
use App\Notifications\CompleteRegistrationNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class RegisterController extends Controller
{
    public function store(RegisterRequest $request): JsonResponse
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
                'theme' => 'light',
                'privacy_settings' => [],
            ]);

            return $user;
        });

        try {
            $token = Password::broker()->createToken($user);
            $expiresInMinutes = (int) config('auth.passwords.users.expire', 60);
            $user->notify(new CompleteRegistrationNotification($token, $expiresInMinutes));
        } catch (TransportExceptionInterface $exception) {
            Log::warning('Complete registration email send failed (API).', [
                'user_id' => $user->id,
                'email' => $user->email,
                'exception' => $exception->getMessage(),
            ]);

            $user->delete();

            return response()->json([
                'message' => 'We could not send the email right now. Please try again later.',
            ], 503);
        }

        return response()->json([
            'message' => 'We emailed you a link to complete your registration. Please check your inbox.',
        ], 202);
    }
}
