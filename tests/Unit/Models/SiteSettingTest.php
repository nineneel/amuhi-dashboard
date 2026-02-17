<?php

use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('gets and sets values via static helpers', function () {
    $payload = [
        'hero_title' => 'AMUHI Portal',
        'hero_subtitle' => 'Kolaborasi untuk layanan umrah dan haji lebih baik',
    ];

    SiteSetting::set('homepage.hero', $payload);

    expect(SiteSetting::get('homepage.hero'))->toBe($payload)
        ->and(SiteSetting::get('unknown.key', 'fallback'))->toBe('fallback');
});

it('casts value attribute to array', function () {
    $setting = SiteSetting::query()->create([
        'key' => 'contact',
        'value' => [
            'email' => 'admin@amuhi.id',
            'phone' => '+62-21-5555-0101',
        ],
        'group' => 'contact',
    ]);

    expect($setting->value)->toBeArray()
        ->and($setting->value)->toMatchArray([
            'email' => 'admin@amuhi.id',
            'phone' => '+62-21-5555-0101',
        ]);
});
