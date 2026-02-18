<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders admin layout shell for admin users without theme toggles', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertSuccessful()
        ->assertSee('Admin Dashboard')
        ->assertSee('CMS Dashboard')
        ->assertSee($admin->name)
        ->assertSee('Dashboard')
        ->assertSee('Content')
        ->assertSee('Portal')
        ->assertSee('Settings')
        ->assertDontSee('Admins')
        ->assertDontSee('$store.theme.toggle()', false);
});

it('shows admin management menu item only for super admins', function () {
    $admin = User::factory()->admin()->create();
    $superAdmin = User::factory()->superAdmin()->create();

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertSuccessful()
        ->assertDontSee('Admins');

    $this->actingAs($superAdmin)
        ->get(route('admin.dashboard'))
        ->assertSuccessful()
        ->assertSee('Admins');
});

it('renders reusable admin components in admin management page', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $admin = User::factory()->admin()->create();

    $this->actingAs($superAdmin)
        ->get(route('admin.admins.index'))
        ->assertSuccessful()
        ->assertSee('Admin Management')
        ->assertSee('Search')
        ->assertSee('Apply')
        ->assertSee('Access Rules')
        ->assertSee('Admin Access Rules')
        ->assertSee($superAdmin->email)
        ->assertSee($admin->email);
});
