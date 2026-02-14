<?php

use App\Models\User;
use App\Notifications\CompleteRegistrationNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

uses(RefreshDatabase::class);

it('creates a user and emails a complete registration link', function () {
    Notification::fake();

    $payload = [
        'member_type' => 'pt',
        'name' => 'Test User',
        'phone' => '08123456789',
        'email' => 'test@example.com',
        'company_name' => 'Test PT',
        'terms' => 'on',
    ];

    $this->post(route('register'), $payload)
        ->assertRedirect(route('register'))
        ->assertSessionHas('status');

    $this->assertGuest();

    $user = User::query()->where('email', $payload['email'])->first();

    expect($user)->not->toBeNull();
    expect($user->email_verified_at)->toBeNull();
    expect($user->profile)->not->toBeNull();
    expect($user->settings)->not->toBeNull();

    Notification::assertSentTo($user, CompleteRegistrationNotification::class);
});

it('completes registration by setting a password and verifying the email', function () {
    $user = User::factory()->unverified()->create([
        'email' => 'complete@example.com',
    ]);

    $token = Password::broker()->createToken($user);

    $this->get(route('registration.complete', ['token' => $token, 'email' => $user->email]))
        ->assertSuccessful()
        ->assertSee('Complete Registration');

    $this->post(route('registration.complete.store'), [
        'token' => $token,
        'email' => $user->email,
        'password' => 'new-password-123',
        'password_confirmation' => 'new-password-123',
    ])->assertRedirect(route('login'))
        ->assertSessionHas('status');

    $this->assertGuest();

    $user->refresh();

    expect($user->email_verified_at)->not->toBeNull();
    expect(Hash::check('new-password-123', $user->password))->toBeTrue();
});

