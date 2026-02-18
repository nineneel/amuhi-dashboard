<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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
    }
}
