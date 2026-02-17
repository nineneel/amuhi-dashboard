<?php

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('allows super admin to access admin management', function () {
    $superAdmin = User::factory()->superAdmin()->create();

    $this->actingAs($superAdmin)
        ->get(route('admin.admins.index'))
        ->assertSuccessful()
        ->assertSee('Admin Management');
});

it('prevents admin users from accessing admin management', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.admins.index'))
        ->assertRedirect(route('admin.dashboard'));
});

it('creates a new admin user', function () {
    $superAdmin = User::factory()->superAdmin()->create();

    $this->actingAs($superAdmin)
        ->post(route('admin.admins.store'), [
            'name' => 'Created Admin',
            'email' => 'created-admin@example.com',
            'password' => 'password123',
            'role' => Role::Admin->value,
        ])
        ->assertRedirect(route('admin.admins.index'));

    $created = User::query()->where('email', 'created-admin@example.com')->first();

    expect($created)->not->toBeNull();
    expect($created?->role)->toBe(Role::Admin);
});

it('updates an admin role', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $admin = User::factory()->admin()->create();

    $this->actingAs($superAdmin)
        ->put(route('admin.admins.update', $admin), [
            'role' => Role::SuperAdmin->value,
        ])
        ->assertRedirect(route('admin.admins.index'));

    expect($admin->fresh()->role)->toBe(Role::SuperAdmin);
});

it('cannot remove own admin access', function () {
    $superAdmin = User::factory()->superAdmin()->create();

    $this->actingAs($superAdmin)
        ->from(route('admin.admins.index'))
        ->delete(route('admin.admins.destroy', $superAdmin))
        ->assertRedirect(route('admin.admins.index'));

    expect($superAdmin->fresh()->role)->toBe(Role::SuperAdmin);
});
