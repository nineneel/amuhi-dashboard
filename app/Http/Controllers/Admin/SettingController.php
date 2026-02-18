<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SettingUpdateRequest;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        $settings = SiteSetting::query()
            ->orderBy('group')
            ->orderBy('key')
            ->get()
            ->groupBy('group');

        return view('admin.settings.index', [
            'settings' => $settings,
        ]);
    }

    public function update(SettingUpdateRequest $request): RedirectResponse
    {
        foreach ($request->input('settings', []) as $key => $value) {
            SiteSetting::set($key, $value);
        }

        return redirect()
            ->route('admin.settings.index')
            ->with('success', __('ui.admin.settings_updated_successfully'));
    }
}
