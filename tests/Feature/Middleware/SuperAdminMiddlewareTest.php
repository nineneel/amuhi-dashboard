<?php

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('redirects admins away from super admin routes', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.admins.index'))
        ->assertRedirect(route('admin.dashboard'));
});

it('allows super admins to access super admin routes', function () {
    $superAdmin = User::factory()->superAdmin()->create();

    $this->actingAs($superAdmin)
        ->get(route('admin.admins.index'))
        ->assertSuccessful()
        ->assertSee(__('ui.admin.admin_management'));
});

it('forbids members from accessing super admin routes', function () {
    $member = User::factory()->create([
        'role' => Role::Member,
    ]);

    $this->actingAs($member)
        ->get(route('admin.admins.index'))
        ->assertForbidden();
});
