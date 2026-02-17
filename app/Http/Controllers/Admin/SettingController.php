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
        return view('admin.settings.index', [
            'settings' => [
                'site_name' => (string) SiteSetting::get('site_name', config('app.name')),
                'site_tagline' => (string) SiteSetting::get('site_tagline', ''),
                'contact_email' => (string) SiteSetting::get('contact_email', ''),
                'contact_phone' => (string) SiteSetting::get('contact_phone', ''),
                'contact_address' => (string) SiteSetting::get('contact_address', ''),
                'seo' => [
                    'meta_title' => (string) SiteSetting::get('seo.meta_title', ''),
                    'meta_description' => (string) SiteSetting::get('seo.meta_description', ''),
                    'meta_keywords' => SiteSetting::get('seo.meta_keywords', []),
                ],
            ],
        ]);
    }

    public function update(SettingUpdateRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $this->saveSetting('site_name', $data['site_name'], 'general');
        $this->saveSetting('site_tagline', $data['site_tagline'] ?? '', 'general');
        $this->saveSetting('contact_email', $data['contact_email'] ?? '', 'contact');
        $this->saveSetting('contact_phone', $data['contact_phone'] ?? '', 'contact');
        $this->saveSetting('contact_address', $data['contact_address'] ?? '', 'contact');
        $this->saveSetting('seo.meta_title', data_get($data, 'seo.meta_title', ''), 'seo');
        $this->saveSetting('seo.meta_description', data_get($data, 'seo.meta_description', ''), 'seo');
        $this->saveSetting('seo.meta_keywords', data_get($data, 'seo.meta_keywords', []), 'seo');

        return redirect()
            ->route('admin.settings.index')
            ->with('status', 'Site settings updated successfully.');
    }

    private function saveSetting(string $key, mixed $value, string $group): void
    {
        SiteSetting::query()->updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'group' => $group,
            ]
        );
    }
}
