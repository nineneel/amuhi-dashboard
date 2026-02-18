<?php

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('allows super admin to access admins list', function () {
    $superAdmin = User::factory()->superAdmin()->create();

    $this->actingAs($superAdmin)
        ->get(route('admin.admins.index'))
        ->assertSuccessful();
});

it('denies admin access to admins list', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.admins.index'))
        ->assertRedirect(route('admin.dashboard'));
});

it('shows create admin form to super admin', function () {
    $superAdmin = User::factory()->superAdmin()->create();

    $this->actingAs($superAdmin)
        ->get(route('admin.admins.create'))
        ->assertSuccessful();
});

it('can create a new admin', function () {
    $superAdmin = User::factory()->superAdmin()->create();

    $this->actingAs($superAdmin)
        ->post(route('admin.admins.store'), [
            'name' => 'New Admin',
            'email' => 'newadmin@example.com',
            'password' => 'password123',
            'role' => 'admin',
        ])
        ->assertRedirect(route('admin.admins.index'));

    $user = User::query()->where('email', 'newadmin@example.com')->first();
    expect($user)->not->toBeNull()
        ->and($user->role)->toBe(Role::Admin);
});

it('can create a new super admin', function () {
    $superAdmin = User::factory()->superAdmin()->create();

    $this->actingAs($superAdmin)
        ->post(route('admin.admins.store'), [
            'name' => 'Super Admin 2',
            'email' => 'super2@example.com',
            'password' => 'password123',
            'role' => 'super_admin',
        ])
        ->assertRedirect(route('admin.admins.index'));

    $user = User::query()->where('email', 'super2@example.com')->first();
    expect($user->role)->toBe(Role::SuperAdmin);
});

it('shows edit admin form to super admin', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $admin = User::factory()->admin()->create();

    $this->actingAs($superAdmin)
        ->get(route('admin.admins.edit', $admin))
        ->assertSuccessful();
});

it('can update admin role', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $admin = User::factory()->admin()->create();

    $this->actingAs($superAdmin)
        ->put(route('admin.admins.update', $admin), [
            'role' => 'super_admin',
        ])
        ->assertRedirect(route('admin.admins.index'));

    expect($admin->fresh()->role)->toBe(Role::SuperAdmin);
});

it('cannot remove own admin access', function () {
    $superAdmin = User::factory()->superAdmin()->create();

    $this->actingAs($superAdmin)
        ->delete(route('admin.admins.destroy', $superAdmin))
        ->assertRedirect(route('admin.admins.index'))
        ->assertSessionHas('error');

    expect($superAdmin->fresh()->role)->toBe(Role::SuperAdmin);
});

it('can remove admin access from another admin', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $admin = User::factory()->admin()->create();

    $this->actingAs($superAdmin)
        ->delete(route('admin.admins.destroy', $admin))
        ->assertRedirect(route('admin.admins.index'));

    expect($admin->fresh()->role)->toBe(Role::Member);
});
