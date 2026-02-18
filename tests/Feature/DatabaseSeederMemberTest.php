<?php

use App\Enums\Role;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('seeds sjrnl27 member account with expected credentials', function () {
    $this->seed(DatabaseSeeder::class);

    $member = User::query()->where('email', 'sjrnl27@gmail.com')->first();

    expect($member)->not->toBeNull();
    expect($member->role)->toBe(Role::Member);
    expect(Hash::check('12345678', $member->password))->toBeTrue();
});
