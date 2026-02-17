<?php

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('has expected enum values', function () {
    expect(Role::SuperAdmin->value)->toBe('super_admin')
        ->and(Role::Admin->value)->toBe('admin')
        ->and(Role::Member->value)->toBe('member');
});

it('casts user role to enum', function () {
    $user = User::factory()->create([
        'role' => Role::SuperAdmin,
    ]);

    expect($user->role)->toBeInstanceOf(Role::class)
        ->and($user->role)->toBe(Role::SuperAdmin);
});
