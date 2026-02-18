<?php

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('returns true for isSuperAdmin on super admin users', function () {
    $user = User::factory()->superAdmin()->create();

    expect($user->isSuperAdmin())->toBeTrue();
});

it('returns true for isAdmin for admin and super admin users', function (Role $role) {
    $user = User::factory()->create(['role' => $role]);

    expect($user->isAdmin())->toBeTrue();
})->with([
    Role::Admin,
    Role::SuperAdmin,
]);

it('returns true for isMember for member users', function () {
    $user = User::factory()->create(['role' => Role::Member]);

    expect($user->isMember())->toBeTrue();
});

it('defaults role to member', function () {
    $user = User::factory()->create();

    expect($user->role)->toBe(Role::Member);
});
