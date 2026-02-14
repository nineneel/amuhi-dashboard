<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\EnableTwoFactorRequest;
use App\Http\Requests\Auth\VerifyTwoFactorRequest;
use App\Models\User;
use App\Notifications\TwoFactorOtpNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TwoFactorController extends Controller
{
    private const ENABLE_CODE_KEY = '2fa:enable:code';

    private const ENABLE_EXPIRES_KEY = '2fa:enable:expires_at';

    private const CHALLENGE_CODE_KEY = '2fa:challenge:code';

    private const CHALLENGE_EXPIRES_KEY = '2fa:challenge:expires_at';

    private const CODE_EXPIRY_MINUTES = 30;

    public function show(Request $request): View
    {
        $user = $request->user();
        $recoveryCodes = $user->settings?->two_factor_recovery_codes ?? [];

        return view('auth.two-factor', [
            'recoveryCodes' => $recoveryCodes,
            'twoFactorEnabled' => $user->two_factor_enabled,
            'enableCodeSent' => $this->sessionCodeIsValid($request, self::ENABLE_CODE_KEY, self::ENABLE_EXPIRES_KEY),
            'maskedEmail' => $this->maskEmail($user->email),
        ]);
    }

    public function sendEnableCode(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->two_factor_enabled) {
            return back()->with('status', 'Two-factor authentication is already enabled.');
        }

        $sent = $this->issueEmailCode(
            $request,
            $user,
            self::ENABLE_CODE_KEY,
            self::ENABLE_EXPIRES_KEY,
            'enable'
        );

        if (! $sent) {
            return back()->withErrors(['email' => 'We could not send the verification code. Please try again later.']);
        }

        return back()->with('status', 'We sent a verification code to '.$this->maskEmail($user->email).'.');
    }

    public function enable(EnableTwoFactorRequest $request): RedirectResponse
    {
        $user = $request->user();
        $code = (string) $request->input('code');

        [$isValid, $message] = $this->validateSessionCode(
            $request,
            $code,
            self::ENABLE_CODE_KEY,
            self::ENABLE_EXPIRES_KEY
        );

        if (! $isValid) {
            return back()->withErrors(['code' => $message]);
        }

        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_enabled' => true,
        ])->save();

        $recoveryCodes = collect(range(1, 8))
            ->map(fn () => strtoupper(Str::random(10)))
            ->all();

        $user->settings()->updateOrCreate(
            ['user_id' => $user->getKey()],
            [
                'notification_email' => $user->settings?->notification_email ?? true,
                'notification_app' => $user->settings?->notification_app ?? true,
                'language' => $user->settings?->language ?? 'id',
                'theme' => $user->settings?->theme ?? 'dark',
                'privacy_settings' => $user->settings?->privacy_settings ?? [],
                'two_factor_recovery_codes' => $recoveryCodes,
            ]
        );

        $request->session()->forget([
            self::ENABLE_CODE_KEY,
            self::ENABLE_EXPIRES_KEY,
        ]);

        return back()->with('status', 'Two-factor authentication enabled.');
    }

    public function disable(Request $request): RedirectResponse
    {
        $user = $request->user();

        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_enabled' => false,
        ])->save();

        $user->settings?->update([
            'two_factor_recovery_codes' => null,
        ]);

        $request->session()->forget([
            self::ENABLE_CODE_KEY,
            self::ENABLE_EXPIRES_KEY,
            self::CHALLENGE_CODE_KEY,
            self::CHALLENGE_EXPIRES_KEY,
        ]);

        return back()->with('status', 'Two-factor authentication disabled.');
    }

    public function challenge(Request $request): View|RedirectResponse
    {
        if (! $request->session()->has('2fa:user:id')) {
            return redirect()->route('login')->withErrors([
                'email' => 'Your login session has expired. Please log in again.',
            ]);
        }

        $userId = $request->session()->get('2fa:user:id');

        /** @var User|null $user */
        $user = User::query()->find($userId);

        if ($user === null || ! $user->two_factor_enabled) {
            $this->forgetTwoFactorSession($request);

            return redirect()->route('login')->withErrors(['email' => 'Two-factor authentication is not configured.']);
        }

        $sendFailed = false;

        if (! $this->sessionCodeIsValid($request, self::CHALLENGE_CODE_KEY, self::CHALLENGE_EXPIRES_KEY)) {
            $sent = $this->issueEmailCode(
                $request,
                $user,
                self::CHALLENGE_CODE_KEY,
                self::CHALLENGE_EXPIRES_KEY,
                'login'
            );

            $sendFailed = ! $sent;
        }

        return view('auth.two-factor-challenge', [
            'maskedEmail' => $this->maskEmail($user->email),
        ])->withErrors($sendFailed ? ['email' => 'We could not send the verification code. Please try again later.'] : []);
    }

    public function resendChallenge(Request $request): RedirectResponse
    {
        $userId = $request->session()->get('2fa:user:id');

        if ($userId === null) {
            return redirect()->route('login')->withErrors(['email' => 'Your login session has expired.']);
        }

        /** @var User|null $user */
        $user = User::query()->find($userId);

        if ($user === null || ! $user->two_factor_enabled) {
            $this->forgetTwoFactorSession($request);

            return redirect()->route('login')->withErrors(['email' => 'Two-factor authentication is not configured.']);
        }

        $sent = $this->issueEmailCode(
            $request,
            $user,
            self::CHALLENGE_CODE_KEY,
            self::CHALLENGE_EXPIRES_KEY,
            'login'
        );

        if (! $sent) {
            return back()->withErrors(['email' => 'We could not send the verification code. Please try again later.']);
        }

        return back()->with('status', 'We sent a new verification code to '.$this->maskEmail($user->email).'.');
    }

    public function verifyChallenge(VerifyTwoFactorRequest $request): RedirectResponse
    {
        $userId = $request->session()->get('2fa:user:id');

        if ($userId === null) {
            return redirect()->route('login')->withErrors(['email' => 'Your login session has expired.']);
        }

        /** @var User|null $user */
        $user = User::query()->find($userId);

        if ($user === null || ! $user->two_factor_enabled) {
            $this->forgetTwoFactorSession($request);

            return redirect()->route('login')->withErrors(['email' => 'Two-factor authentication is not configured.']);
        }

        $code = (string) $request->input('code');

        $recoveryCodes = $user->settings?->two_factor_recovery_codes ?? [];

        $isRecoveryCode = in_array($code, $recoveryCodes, true);
        [$isValidEmailCode, $message] = $this->validateSessionCode(
            $request,
            $code,
            self::CHALLENGE_CODE_KEY,
            self::CHALLENGE_EXPIRES_KEY
        );

        if (! $isRecoveryCode && ! $isValidEmailCode) {
            return back()->withErrors(['code' => $message]);
        }

        if ($isRecoveryCode) {
            $user->settings?->update([
                'two_factor_recovery_codes' => array_values(array_diff($recoveryCodes, [$code])),
            ]);
        } else {
            $request->session()->forget([
                self::CHALLENGE_CODE_KEY,
                self::CHALLENGE_EXPIRES_KEY,
            ]);
        }

        Auth::login($user, (bool) $request->session()->get('2fa:remember', false));
        $request->session()->regenerate();

        $this->forgetTwoFactorSession($request);

        return redirect()->intended(route('dashboard'));
    }

    private function forgetTwoFactorSession(Request $request): void
    {
        $request->session()->forget([
            '2fa:user:id',
            '2fa:remember',
            self::CHALLENGE_CODE_KEY,
            self::CHALLENGE_EXPIRES_KEY,
        ]);
    }

    private function issueEmailCode(
        Request $request,
        User $user,
        string $codeKey,
        string $expiresKey,
        string $context
    ): bool {
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = now()->addMinutes(self::CODE_EXPIRY_MINUTES)->timestamp;

        $request->session()->put($codeKey, Hash::make($code));
        $request->session()->put($expiresKey, $expiresAt);

        try {
            $user->notify(new TwoFactorOtpNotification($code, $context, self::CODE_EXPIRY_MINUTES));

            return true;
        } catch (\Throwable $e) {
            $request->session()->forget([$codeKey, $expiresKey]);

            Log::error('Failed to send two-factor code email.', [
                'user_id' => $user->getKey(),
                'context' => $context,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    private function sessionCodeIsValid(Request $request, string $codeKey, string $expiresKey): bool
    {
        $hash = $request->session()->get($codeKey);
        $expiresAt = $request->session()->get($expiresKey);

        if (! $hash || ! $expiresAt) {
            return false;
        }

        return now()->timestamp <= (int) $expiresAt;
    }

    /**
     * @return array{0: bool, 1: string}
     */
    private function validateSessionCode(Request $request, string $code, string $codeKey, string $expiresKey): array
    {
        $hash = $request->session()->get($codeKey);
        $expiresAt = $request->session()->get($expiresKey);

        if (! $hash || ! $expiresAt) {
            return [false, 'A verification code has not been sent yet.'];
        }

        if (now()->timestamp > (int) $expiresAt) {
            $request->session()->forget([$codeKey, $expiresKey]);

            return [false, 'Your verification code has expired. Please request a new one.'];
        }

        if (! Hash::check($code, $hash)) {
            return [false, 'Invalid verification code.'];
        }

        return [true, ''];
    }

    private function maskEmail(string $email): string
    {
        [$local, $domain] = explode('@', $email.'@', 2);

        $maskedLocal = strlen($local) <= 2
            ? substr($local, 0, 1).'*'
            : substr($local, 0, 1).str_repeat('*', max(strlen($local) - 2, 1)).substr($local, -1);

        return $maskedLocal.'@'.$domain;
    }
}
