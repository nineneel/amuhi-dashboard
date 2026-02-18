<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\MemberType;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(AdminSeeder::class);
        $this->call(SubscriptionPlanSeeder::class);
        $this->call(EventSeeder::class);
        $this->call(NewsSeeder::class);
        $this->call(TestimonySeeder::class);
        $this->call(SiteSettingSeeder::class);

        $user = User::query()->updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]
        );

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'member_type' => MemberType::PpuiPihk,
                'company_name' => 'PT AMUHI Nusantara',
                'phone' => '+62 812-3456-7890',
                'address' => 'Jl. Jend. Sudirman No. 10, Jakarta',
                'bio' => 'AMUHI member focused on compliance, service excellence, and member collaboration.',
            ]
        );

        $user->settings()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'notification_email' => true,
                'notification_app' => true,
                'language' => 'id',
                'theme' => 'dark',
                'privacy_settings' => [
                    'profile_visible' => true,
                    'show_email' => false,
                    'show_phone' => true,
                ],
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'sjrnl27@gmail.com'],
            [
                'name' => 'Member SJRNL27',
                'email_verified_at' => now(),
                'password' => Hash::make('12345678'),
                'role' => Role::Member,
            ]
        );
    }
}
