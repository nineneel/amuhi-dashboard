<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows dashboard stats to admin users', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertSuccessful();
});

it('shows dashboard to super admin', function () {
    $superAdmin = User::factory()->superAdmin()->create();

    $this->actingAs($superAdmin)
        ->get(route('admin.dashboard'))
        ->assertSuccessful();
});

it('redirects guest to admin login', function () {
    $this->get(route('admin.dashboard'))
        ->assertRedirect(route('admin.login'));
});

it('denies member access to dashboard', function () {
    $member = User::factory()->create();

    $this->actingAs($member)
        ->get(route('admin.dashboard'))
        ->assertRedirect(route('dashboard'));
});
