<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only(['email', 'password']);
        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->withInput($request->only('email'));
        }

        $request->session()->regenerate();

        /** @var User $user */
        $user = Auth::user();

        if ($user->two_factor_enabled) {
            Auth::logout();

            $request->session()->put('2fa:user:id', $user->getKey());
            $request->session()->put('2fa:remember', $remember);

            return redirect()->route('two-factor.challenge');
        }

        return redirect()->intended(route('dashboard'));
    }
}
