<?php

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates a new admin user', function () {
    $email = 'new-admin@example.com';

    $this->artisan('make:admin', ['email' => $email])
        ->expectsOutputToContain('Password:')
        ->assertSuccessful();

    $admin = User::query()->where('email', $email)->first();

    expect($admin)->not->toBeNull()
        ->and($admin?->role)->toBe(Role::Admin);
});

it('promotes an existing user to admin', function () {
    $user = User::factory()->create([
        'role' => Role::Member,
    ]);

    $this->artisan('make:admin', ['email' => $user->email])
        ->assertSuccessful();

    expect($user->refresh()->role)->toBe(Role::Admin);
});

it('creates a super admin when super flag is provided', function () {
    $email = 'super-admin@example.com';

    $this->artisan('make:admin', ['email' => $email, '--super' => true])
        ->assertSuccessful();

    $superAdmin = User::query()->where('email', $email)->first();

    expect($superAdmin)->not->toBeNull()
        ->and($superAdmin?->role)->toBe(Role::SuperAdmin);
});
