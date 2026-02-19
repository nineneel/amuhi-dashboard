<?php

use App\Models\User;
use App\Notifications\CompleteRegistrationNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

it('registers via API and sends a complete-registration email', function () {
    Notification::fake();
    config()->set('app.url', 'https://portal.amuhi.id');

    $payload = [
        'member_type' => 'personal',
        'name' => 'Test User',
        'phone' => '081234567890',
        'email' => 'test-api-register@example.com',
        'company_name' => null,
        'terms' => true,
    ];

    $this->withServerVariables([
        'HTTP_HOST' => '127.0.0.1',
    ])->postJson('/api/v1/register', $payload)
        ->assertAccepted()
        ->assertJsonStructure(['message']);

    $user = User::query()->where('email', $payload['email'])->first();
    expect($user)->not->toBeNull();
    expect($user->profile)->not->toBeNull();
    expect($user->settings)->not->toBeNull();

    Notification::assertSentTo(
        $user,
        CompleteRegistrationNotification::class,
        function (CompleteRegistrationNotification $notification) use ($user): bool {
            $mail = $notification->toMail($user);
            $host = parse_url($mail->actionUrl, PHP_URL_HOST);

            expect($host)->toBe('portal.amuhi.id');

            return true;
        }
    );
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

it('returns localized unique email validation messages for API registration', function (string $locale, string $expectedMessage) {
    config()->set('app.locale', $locale);

    User::factory()->create([
        'email' => 'existing-user@example.com',
    ]);

    $this->postJson('/api/v1/register', [
        'member_type' => 'personal',
        'name' => 'Test User',
        'phone' => '081234567890',
        'email' => 'existing-user@example.com',
        'terms' => true,
    ])
        ->assertUnprocessable()
        ->assertJsonPath('errors.email.0', $expectedMessage);
})->with([
    'indonesian' => ['id', 'Kolom email sudah digunakan.'],
    'english' => ['en', 'The email has already been taken.'],
]);

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
