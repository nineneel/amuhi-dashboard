<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function create(): View
    {
        return view('admin.auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only(['email', 'password']);
        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            return back()->withErrors([
                'email' => __('ui.admin.the_provided_credentials_do_not_match_our_records'),
            ])->withInput($request->only('email'));
        }

        $request->session()->regenerate();

        /** @var User $user */
        $user = Auth::user();

        if (! $user->isAdmin()) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => __('ui.admin.you_do_not_have_access_to_the_admin_area'),
            ])->withInput($request->only('email'));
        }

        if ($user->two_factor_enabled) {
            Auth::logout();

            $request->session()->put('2fa:user:id', $user->getKey());
            $request->session()->put('2fa:remember', $remember);
            $request->session()->put('url.intended', route('admin.dashboard'));

            return redirect()->route('two-factor.challenge');
        }

        return redirect()->intended(route('admin.dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
