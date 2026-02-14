<?php

use App\Models\User;
use App\Notifications\CompleteRegistrationNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

it('registers via API and sends a complete-registration email', function () {
    Notification::fake();

    $payload = [
        'member_type' => 'personal',
        'name' => 'Test User',
        'phone' => '081234567890',
        'email' => 'test-api-register@example.com',
        'company_name' => null,
        'terms' => true,
    ];

    $this->postJson('/api/v1/register', $payload)
        ->assertStatus(202)
        ->assertJsonStructure(['message']);

    $user = User::query()->where('email', $payload['email'])->first();
    expect($user)->not->toBeNull();
    expect($user->profile)->not->toBeNull();
    expect($user->settings)->not->toBeNull();

    Notification::assertSentTo($user, CompleteRegistrationNotification::class);
});

it('validates required fields for API registration', function () {
    $this->postJson('/api/v1/register', [
        'member_type' => 'personal',
        'name' => 'Test User',
        'phone' => '081234567890',
        'email' => 'missing-terms@example.com',
    ])
        ->assertUnprocessable()
        ->assertJsonStructure(['message', 'errors']);
});

it('rate limits API registration to reduce abuse', function () {
    Notification::fake();

    for ($i = 0; $i < 10; $i++) {
        $this->postJson('/api/v1/register', [
            'member_type' => 'personal',
            'name' => 'Test User',
            'phone' => '081234567890',
            'email' => "rate-limit-$i@example.com",
            'terms' => true,
        ])->assertStatus(202);
    }

    $this->postJson('/api/v1/register', [
        'member_type' => 'personal',
        'name' => 'Test User',
        'phone' => '081234567890',
        'email' => 'rate-limit-11@example.com',
        'terms' => true,
    ])->assertStatus(429);
});

