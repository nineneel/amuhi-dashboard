<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows user index to admin', function () {
    $admin = User::factory()->admin()->create();
    User::factory()->count(3)->create();

    $this->actingAs($admin)
        ->get(route('admin.users.index'))
        ->assertSuccessful();
});

it('can search users by name', function () {
    $admin = User::factory()->admin()->create();
    User::factory()->create(['name' => 'John Doe']);
    User::factory()->create(['name' => 'Jane Smith']);

    $this->actingAs($admin)
        ->get(route('admin.users.index', ['search' => 'John']))
        ->assertSuccessful();
});

it('shows only member users on users index', function () {
    $admin = User::factory()->admin()->create();
    $member = User::factory()->create(['name' => 'Member User']);
    $adminUser = User::factory()->admin()->create(['name' => 'Admin User']);
    $superAdminUser = User::factory()->superAdmin()->create(['name' => 'Super Admin User']);

    $this->actingAs($admin)
        ->get(route('admin.users.index'))
        ->assertSuccessful()
        ->assertSee($member->name)
        ->assertDontSee($adminUser->name)
        ->assertDontSee($superAdminUser->name);
});

it('shows user detail', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();

    $this->actingAs($admin)
        ->get(route('admin.users.show', $user))
        ->assertSuccessful();
});

it('shows user edit form', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();

    $this->actingAs($admin)
        ->get(route('admin.users.edit', $user))
        ->assertSuccessful();
});

it('can update a user', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create(['name' => 'Old Name']);

    $this->actingAs($admin)
        ->put(route('admin.users.update', $user), [
            'name' => 'New Name',
            'email' => $user->email,
        ])
        ->assertRedirect(route('admin.users.index'));

    expect($user->fresh()->name)->toBe('New Name');
});

it('can delete a user', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();

    $this->actingAs($admin)
        ->delete(route('admin.users.destroy', $user))
        ->assertRedirect(route('admin.users.index'));

    $this->assertModelMissing($user);
});

it('validates required fields on user update', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();

    $this->actingAs($admin)
        ->put(route('admin.users.update', $user), [])
        ->assertSessionHasErrors(['name', 'email']);
});
