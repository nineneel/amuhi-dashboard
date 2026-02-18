<?php

use App\Enums\Role;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('redirects an unpaid member to payment when opening dashboard', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertRedirect(route('payments.show'))
        ->assertSessionHas('warning');
});

it('redirects an unpaid member to payment when opening events', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $this->actingAs($user)
        ->get(route('events.index'))
        ->assertRedirect(route('payments.show'))
        ->assertSessionHas('warning');
});

it('redirects an unpaid member to payment when opening programs', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $this->actingAs($user)
        ->get(route('programs.index'))
        ->assertRedirect(route('payments.show'))
        ->assertSessionHas('warning');
});

it('redirects an unpaid member to payment when opening forum', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $this->actingAs($user)
        ->get(route('forum.index'))
        ->assertRedirect(route('payments.show'))
        ->assertSessionHas('warning');
});

it('allows admin and super admin users to open subscribed portal features without payment', function (Role $role) {
    $user = User::factory()->create([
        'email_verified_at' => now(),
        'role' => $role,
    ]);

    $this->actingAs($user);

    foreach (['dashboard', 'events.index', 'events.calendar', 'programs.index', 'forum.index', 'invoices.index'] as $routeName) {
        $this->get(route($routeName))->assertSuccessful();
    }

    $this->get(route('dashboard'))
        ->assertSuccessful()
        ->assertDontSee(__('ui.dashboard.subscription_required'));
})->with([
    Role::Admin,
    Role::SuperAdmin,
]);

it('redirects verified admin and super admin users to dashboard from verification notice', function (Role $role) {
    $user = User::factory()->create([
        'email_verified_at' => now(),
        'role' => $role,
    ]);

    $this->actingAs($user)
        ->get(route('verification.notice'))
        ->assertRedirect(route('dashboard'));
})->with([
    Role::Admin,
    Role::SuperAdmin,
]);

it('shows go to admin button in portal dropdown for admin and super admin users', function (Role $role) {
    $user = User::factory()->create([
        'email_verified_at' => now(),
        'role' => $role,
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertSuccessful()
        ->assertSee(__('ui.nav.go_to_admin'))
        ->assertSee(route('admin.dashboard'), false);
})->with([
    Role::Admin,
    Role::SuperAdmin,
]);

it('does not show go to admin button in portal dropdown for members', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    Subscription::factory()->for($user)->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertSuccessful()
        ->assertDontSee(__('ui.nav.go_to_admin'))
        ->assertDontSee(route('admin.dashboard'), false);
});
