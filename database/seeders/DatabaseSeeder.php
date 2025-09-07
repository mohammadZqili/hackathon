<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Core system seeders (must run first)
            RoleSeeder::class,
            SettingSeeder::class,
            PermissionRoleSeeder::class,
            
            // Hackathon-specific role and permission seeder
            HackathonRoleSeeder::class,
            
            // User seeder (creates users with roles)
            UserSeeder::class,
            
            // Hackathon core data
            HackathonEditionSeeder::class,
            TrackSeeder::class,
            
            // Teams and related data
            TeamSeeder::class,
            
            // Content seeders
            NewsSeeder::class,
            WorkshopSeeder::class,
            
            // Optional: Sample data for dashboard
            FinancialMetricsSeeder::class,
        ]);
    }
}
