<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders admin layout shell for admin users without theme toggles', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertSuccessful()
        ->assertSee(__('ui.admin.admin_dashboard'))
        ->assertSee(__('ui.admin.cms_dashboard'))
        ->assertSee($admin->name)
        ->assertSee(__('ui.admin.dashboard'))
        ->assertSee(__('ui.admin.content'))
        ->assertSee(__('ui.admin.portal'))
        ->assertSee(__('ui.admin.settings'))
        ->assertDontSee('/admin/admins', false)
        ->assertDontSee('$store.theme.toggle()', false);
});

it('shows admin management menu item only for super admins', function () {
    $admin = User::factory()->admin()->create();
    $superAdmin = User::factory()->superAdmin()->create();

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertSuccessful()
        ->assertDontSee('/admin/admins', false);

    $this->actingAs($superAdmin)
        ->get(route('admin.dashboard'))
        ->assertSuccessful()
        ->assertSee('/admin/admins', false);
});

it('renders reusable admin components in admin management page', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $admin = User::factory()->admin()->create();

    $this->actingAs($superAdmin)
        ->get(route('admin.admins.index'))
        ->assertSuccessful()
        ->assertSee(__('ui.admin.admin_management'))
        ->assertSee(__('ui.admin.search'))
        ->assertSee(__('ui.admin.apply_filters'))
        ->assertSee(__('ui.admin.admin_access_rules'))
        ->assertSee($superAdmin->email)
        ->assertSee($admin->email);
});
