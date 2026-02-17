<?php

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the admin login page', function () {
    $this->get(route('admin.login'))
        ->assertSuccessful()
        ->assertSee('Admin Sign In');
});

it('allows admin users to login with valid credentials', function () {
    $admin = User::factory()->admin()->create();

    $this->post(route('admin.login.store'), [
        'email' => $admin->email,
        'password' => 'password',
    ])->assertRedirect(route('admin.dashboard'));

    $this->assertAuthenticatedAs($admin);
});

it('allows super admins to login with valid credentials', function () {
    $superAdmin = User::factory()->superAdmin()->create();

    $this->post(route('admin.login.store'), [
        'email' => $superAdmin->email,
        'password' => 'password',
    ])->assertRedirect(route('admin.dashboard'));

    $this->assertAuthenticatedAs($superAdmin);
});

it('prevents members from logging in to admin', function () {
    $member = User::factory()->create([
        'role' => Role::Member,
    ]);

    $this->post(route('admin.login.store'), [
        'email' => $member->email,
        'password' => 'password',
    ])->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('shows an error for invalid admin credentials', function () {
    $admin = User::factory()->admin()->create();

    $this->post(route('admin.login.store'), [
        'email' => $admin->email,
        'password' => 'invalid-password',
    ])->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('allows admins to logout from admin area', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post(route('admin.logout'))
        ->assertRedirect(route('admin.login'));

    $this->assertGuest();
});
