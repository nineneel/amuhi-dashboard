<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'site_name',
                'value' => ['text' => 'AMUHI'],
                'group' => 'general',
            ],
            [
                'key' => 'site_tagline',
                'value' => ['text' => 'Asosiasi Muslim Penyelenggara Haji dan Umrah Indonesia'],
                'group' => 'general',
            ],
            [
                'key' => 'contact_email',
                'value' => ['email' => 'admin@amuhi.id'],
                'group' => 'contact',
            ],
            [
                'key' => 'contact_phone',
                'value' => ['number' => '+62-21-5555-0101'],
                'group' => 'contact',
            ],
            [
                'key' => 'seo_defaults',
                'value' => [
                    'meta_title' => 'AMUHI Official',
                    'meta_description' => 'Portal resmi AMUHI untuk program, berita, dan informasi anggota.',
                    'meta_keywords' => ['amuhi', 'umrah', 'haji', 'asosiasi'],
                ],
                'group' => 'seo',
            ],
        ];

        foreach ($settings as $setting) {
            SiteSetting::query()->updateOrCreate(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'group' => $setting['group'],
                ]
            );
        }
    }
}
