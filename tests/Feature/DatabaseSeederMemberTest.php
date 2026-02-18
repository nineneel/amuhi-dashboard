<?php

use App\Enums\Role;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('seeds only the default super admin user', function () {
    $this->seed(DatabaseSeeder::class);

    $admin = User::query()->where('email', 'milenialumrahhaji@gmail.com')->first();

    expect($admin)->not->toBeNull();
    expect($admin->role)->toBe(Role::SuperAdmin);
    expect($admin->name)->toBe('Admin AMUHI');
    expect(User::query()->where('email', 'test@example.com')->exists())->toBeFalse();
    expect(User::query()->where('email', 'sjrnl27@gmail.com')->exists())->toBeFalse();
});
