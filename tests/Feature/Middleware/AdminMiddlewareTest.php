<?php

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('redirects unauthenticated users to admin login', function () {
    $this->get(route('admin.dashboard'))
        ->assertRedirect(route('admin.login'));
});

it('prevents members from accessing admin routes', function () {
    $member = User::factory()->create([
        'role' => Role::Member,
    ]);

    $this->actingAs($member)
        ->get(route('admin.dashboard'))
        ->assertRedirect(route('dashboard'));
});

it('allows admins to access admin routes', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertSuccessful()
        ->assertSee('Admin Dashboard');
});

it('allows super admins to access admin routes', function () {
    $superAdmin = User::factory()->superAdmin()->create();

    $this->actingAs($superAdmin)
        ->get(route('admin.dashboard'))
        ->assertSuccessful()
        ->assertSee('Admin Dashboard');
});
