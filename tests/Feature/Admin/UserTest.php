<?php

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows users index for admin', function () {
    $admin = User::factory()->admin()->create();
    $member = User::factory()->create(['name' => 'Member User']);

    $this->actingAs($admin)
        ->get(route('admin.users.index'))
        ->assertSuccessful()
        ->assertSee('Users')
        ->assertSee('Member User')
        ->assertSee($member->email);
});

it('shows a specific user details page', function () {
    $admin = User::factory()->admin()->create();
    $member = User::factory()->create();

    $this->actingAs($admin)
        ->get(route('admin.users.show', $member))
        ->assertSuccessful()
        ->assertSee('User Details')
        ->assertSee($member->email);
});

it('updates a user record', function () {
    $admin = User::factory()->admin()->create();
    $member = User::factory()->create([
        'name' => 'Before Name',
        'role' => Role::Member,
    ]);

    $this->actingAs($admin)
        ->put(route('admin.users.update', $member), [
            'name' => 'After Name',
            'email' => 'after@example.com',
            'role' => Role::Admin->value,
        ])
        ->assertRedirect(route('admin.users.edit', $member));

    $member->refresh();

    expect($member->name)->toBe('After Name');
    expect($member->email)->toBe('after@example.com');
    expect($member->role)->toBe(Role::Admin);
});

it('deletes a user record', function () {
    $admin = User::factory()->admin()->create();
    $member = User::factory()->create();

    $this->actingAs($admin)
        ->delete(route('admin.users.destroy', $member))
        ->assertRedirect(route('admin.users.index'));

    $this->assertModelMissing($member);
});
