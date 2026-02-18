<?php

namespace App\Console\Commands;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MakeAdminCommand extends Command
{
    protected $signature = 'make:admin {email} {--super}';

    protected $description = 'Create or promote a user to admin or super admin.';

    public function handle(): int
    {
        $email = strtolower(trim((string) $this->argument('email')));
        $role = $this->option('super') ? Role::SuperAdmin : Role::Admin;

        $user = User::query()->where('email', $email)->first();

        if ($user !== null) {
            $user->forceFill([
                'role' => $role,
            ])->save();

            $this->info(sprintf('User [%s] promoted to [%s].', $email, $role->value));

            return self::SUCCESS;
        }

        $generatedPassword = Str::random(16);
        $name = Str::title(str_replace(['.', '_', '-'], ' ', Str::before($email, '@')));

        User::query()->create([
            'name' => $name === '' ? 'Admin User' : $name,
            'email' => $email,
            'email_verified_at' => now(),
            'password' => Hash::make($generatedPassword),
            'role' => $role,
        ]);

        $this->info(sprintf('Created new [%s] user.', $role->value));
        $this->line(sprintf('Email: %s', $email));
        $this->line(sprintf('Password: %s', $generatedPassword));

        return self::SUCCESS;
    }
}
