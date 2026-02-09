<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;
use PragmaRX\Google2FALaravel\Google2FA;

class TwoFactorController extends Controller
{
    public function show(Request $request, Google2FA $google2fa): View
    {
        $user = $request->user();
        $secret = $user->two_factor_enabled ? null : $request->session()->get('2fa:secret');

        if ($secret === null && ! $user->two_factor_enabled) {
            $secret = $google2fa->generateSecretKey();
            $request->session()->put('2fa:secret', $secret);
        }

        $qrCodeUrl = $secret !== null
            ? $google2fa->getQRCodeUrl(config('app.name', 'Amuhi Dashboard'), $user->email, $secret)
            : null;

        $recoveryCodes = $user->settings?->two_factor_recovery_codes ?? [];

        return view('auth.two-factor', [
            'secret' => $secret,
            'qrCodeUrl' => $qrCodeUrl,
            'recoveryCodes' => $recoveryCodes,
            'twoFactorEnabled' => $user->two_factor_enabled,
        ]);
    }

    public function enable(Request $request, Google2FA $google2fa): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string'],
        ]);

        $secret = $request->session()->get('2fa:secret');

        if ($secret === null) {
            return back()->withErrors(['code' => 'Two-factor setup has expired. Please start again.']);
        }

        if (! $google2fa->verifyKey($secret, (string) $request->input('code'))) {
            return back()->withErrors(['code' => 'Invalid authentication code.']);
        }

        $user = $request->user();
        $user->forceFill([
            'two_factor_secret' => encrypt($secret),
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
                'language' => $user->settings?->language ?? 'en',
                'theme' => $user->settings?->theme ?? 'light',
                'privacy_settings' => $user->settings?->privacy_settings ?? [],
                'two_factor_recovery_codes' => $recoveryCodes,
            ]
        );

        $request->session()->forget('2fa:secret');

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

        $request->session()->forget('2fa:secret');

        return back()->with('status', 'Two-factor authentication disabled.');
    }

    public function challenge(): View|RedirectResponse
    {
        if (! session()->has('2fa:user:id')) {
            return redirect()->route('login')->withErrors([
                'email' => 'Your login session has expired. Please log in again.',
            ]);
        }

        return view('auth.two-factor-challenge');
    }

    public function verifyChallenge(Request $request, Google2FA $google2fa): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string'],
        ]);

        $userId = $request->session()->get('2fa:user:id');

        if ($userId === null) {
            return redirect()->route('login')->withErrors(['email' => 'Your login session has expired.']);
        }

        /** @var User|null $user */
        $user = User::query()->find($userId);

        if ($user === null || $user->two_factor_secret === null) {
            $this->forgetTwoFactorSession($request);

            return redirect()->route('login')->withErrors(['email' => 'Two-factor authentication is not configured.']);
        }

        $secret = decrypt($user->two_factor_secret);
        $code = (string) $request->input('code');

        $recoveryCodes = $user->settings?->two_factor_recovery_codes ?? [];

        $isRecoveryCode = in_array($code, $recoveryCodes, true);
        $isValidOtp = $google2fa->verifyKey($secret, $code);

        if (! $isRecoveryCode && ! $isValidOtp) {
            return back()->withErrors(['code' => 'Invalid authentication code.']);
        }

        if ($isRecoveryCode) {
            $user->settings?->update([
                'two_factor_recovery_codes' => array_values(array_diff($recoveryCodes, [$code])),
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
            '2fa:secret',
        ]);
    }
}
