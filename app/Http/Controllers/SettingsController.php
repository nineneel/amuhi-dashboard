<?php

namespace App\Http\Controllers;

use App\Http\Requests\PasswordUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $user->load('settings');

        $section = $request->query('section', 'account');
        $allowedSections = ['account', 'notifications', 'privacy', 'appearance', 'security', 'danger'];

        if (! in_array($section, $allowedSections)) {
            $section = 'account';
        }

        return view('settings.index', compact('user', 'section'));
    }

    public function updateAccount(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'language' => ['required', 'in:en,id'],
        ]);

        $user = auth()->user();
        $user->settings()->updateOrCreate(
            ['user_id' => $user->id],
            ['language' => $data['language']]
        );

        return redirect()->route('settings.index', ['section' => 'account'])
            ->with('success', 'Account settings updated successfully.');
    }

    public function updateNotifications(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'notification_email' => ['nullable', 'boolean'],
            'notification_app' => ['nullable', 'boolean'],
        ]);

        $user = auth()->user();
        $user->settings()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'notification_email' => $request->boolean('notification_email'),
                'notification_app' => $request->boolean('notification_app'),
            ]
        );

        return redirect()->route('settings.index', ['section' => 'notifications'])
            ->with('success', 'Notification settings updated successfully.');
    }

    public function updatePrivacy(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'profile_visible' => ['nullable', 'boolean'],
            'show_email' => ['nullable', 'boolean'],
            'show_phone' => ['nullable', 'boolean'],
        ]);

        $user = auth()->user();
        $privacySettings = [
            'profile_visible' => $request->boolean('profile_visible'),
            'show_email' => $request->boolean('show_email'),
            'show_phone' => $request->boolean('show_phone'),
        ];

        $user->settings()->updateOrCreate(
            ['user_id' => $user->id],
            ['privacy_settings' => $privacySettings]
        );

        return redirect()->route('settings.index', ['section' => 'privacy'])
            ->with('success', 'Privacy settings updated successfully.');
    }

    public function updateAppearance(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'theme' => ['required', 'in:light,dark,system'],
        ]);

        $user = auth()->user();
        $user->settings()->updateOrCreate(
            ['user_id' => $user->id],
            ['theme' => $data['theme']]
        );

        return redirect()->route('settings.index', ['section' => 'appearance'])
            ->with('success', 'Appearance settings updated successfully.');
    }

    public function updatePassword(PasswordUpdateRequest $request): RedirectResponse
    {
        $user = auth()->user();
        $user->update([
            'password' => Hash::make($request->validated()['password']),
        ]);

        return redirect()->route('settings.index', ['section' => 'security'])
            ->with('success', 'Password changed successfully.');
    }

    public function destroyAccount(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = auth()->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Your account has been deleted.');
    }
}
