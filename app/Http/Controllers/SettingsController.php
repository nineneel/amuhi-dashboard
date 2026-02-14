<?php

namespace App\Http\Controllers;

use App\Http\Requests\PasswordUpdateRequest;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
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

        $user = $request->user();
        $user->settings()->updateOrCreate(
            ['user_id' => $user->id],
            ['language' => $data['language']]
        );

        app()->setLocale($data['language']);

        $languageLabel = $data['language'] === 'id' ? 'Bahasa Indonesia' : 'English';
        $this->recordSettingsActivity($request, 'Account settings updated', 'Language set to '.$languageLabel.'.');

        return redirect()->route('settings.index', ['section' => 'account'])
            ->with('success', __('ui.settings.account_saved'));
    }

    public function updateNotifications(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'notification_email' => ['nullable', 'boolean'],
            'notification_app' => ['nullable', 'boolean'],
        ]);

        $user = $request->user();
        $user->settings()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'notification_email' => $request->boolean('notification_email'),
                'notification_app' => $request->boolean('notification_app'),
            ]
        );

        $emailStatus = $request->boolean('notification_email') ? 'on' : 'off';
        $appStatus = $request->boolean('notification_app') ? 'on' : 'off';
        $this->recordSettingsActivity(
            $request,
            'Notification settings updated',
            'Email notifications '.$emailStatus.', in-app notifications '.$appStatus.'.'
        );

        return redirect()->route('settings.index', ['section' => 'notifications'])
            ->with('success', __('ui.settings.notifications_saved'));
    }

    public function updatePrivacy(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'profile_visible' => ['nullable', 'boolean'],
            'show_email' => ['nullable', 'boolean'],
            'show_phone' => ['nullable', 'boolean'],
        ]);

        $user = $request->user();
        $privacySettings = [
            'profile_visible' => $request->boolean('profile_visible'),
            'show_email' => $request->boolean('show_email'),
            'show_phone' => $request->boolean('show_phone'),
        ];

        $user->settings()->updateOrCreate(
            ['user_id' => $user->id],
            ['privacy_settings' => $privacySettings]
        );

        $profileVisibility = $request->boolean('profile_visible') ? 'visible' : 'hidden';
        $showEmail = $request->boolean('show_email') ? 'visible' : 'hidden';
        $showPhone = $request->boolean('show_phone') ? 'visible' : 'hidden';
        $this->recordSettingsActivity(
            $request,
            'Privacy settings updated',
            'Profile '.$profileVisibility.', email '.$showEmail.', phone '.$showPhone.'.'
        );

        return redirect()->route('settings.index', ['section' => 'privacy'])
            ->with('success', __('ui.settings.privacy_saved'));
    }

    public function updateAppearance(Request $request): RedirectResponse
    {
        $user = $request->user();
        $user->settings()->updateOrCreate(
            ['user_id' => $user->id],
            ['theme' => 'dark']
        );

        return redirect()->route('settings.index', ['section' => 'appearance'])
            ->with('success', __('ui.settings.appearance_saved'));
    }

    public function updatePassword(PasswordUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->update([
            'password' => Hash::make($request->validated()['password']),
        ]);

        $this->recordSettingsActivity($request, 'Security settings updated', 'Password was changed.');

        return redirect()->route('settings.index', ['section' => 'security'])
            ->with('success', __('ui.settings.password_saved'));
    }

    public function destroyAccount(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', __('ui.settings.account_deleted'));
    }

    private function recordSettingsActivity(Request $request, string $action, string $description): void
    {
        $user = $request->user();

        ActivityLog::query()->create([
            'user_id' => $user->id,
            'action' => $action,
            'description' => $description,
            'subject_type' => User::class,
            'subject_id' => $user->id,
            'ip_address' => $request->ip(),
        ]);
    }
}
