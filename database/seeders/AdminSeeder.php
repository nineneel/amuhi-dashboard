<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $adminEmail = (string) config('cms.admin.email', 'milenialumrahhaji@gmail.com');
        $adminName = (string) config('cms.admin.name', 'Admin AMUHI');
        $configuredPassword = config('cms.admin.password');

        $password = null;

        if (! is_string($configuredPassword) || $configuredPassword === '') {
            $existingUser = User::query()->where('email', $adminEmail)->first();

            if ($existingUser === null) {
                $password = Str::random(16);
            }
        } else {
            $password = $configuredPassword;
        }

        $attributes = [
            'name' => $adminName,
            'email_verified_at' => now(),
            'role' => Role::SuperAdmin,
        ];

        if ($password !== null) {
            $attributes['password'] = Hash::make($password);
        }

        User::query()->updateOrCreate(
            ['email' => $adminEmail],
            $attributes
        );

        if ($this->command !== null && $password !== null) {
            $this->command->info(sprintf('Super admin seeded: %s', $adminEmail));
            $this->command->line(sprintf('Password: %s', $password));
        }
    }
}
