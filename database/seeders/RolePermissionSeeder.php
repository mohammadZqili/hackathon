<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\Edition;
use App\Models\Track;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Create roles
        $systemAdmin = Role::firstOrCreate(['name' => 'system-admin']);
        $hackathonAdmin = Role::firstOrCreate(['name' => 'hackathon-admin']);
        $trackSupervisor = Role::firstOrCreate(['name' => 'track-supervisor']);
        $workshopSupervisor = Role::firstOrCreate(['name' => 'workshop-supervisor']);
        $teamLeader = Role::firstOrCreate(['name' => 'team-leader']);
        $teamMember = Role::firstOrCreate(['name' => 'team-member']);
        $visitor = Role::firstOrCreate(['name' => 'visitor']);
        
        // Create permissions
        $permissions = [
            'manage-all-editions',
            'manage-edition',
            'manage-tracks',
            'manage-teams',
            'manage-ideas',
            'review-ideas',
            'manage-workshops',
            'manage-checkins',
            'view-reports',
            'export-reports',
        ];
        
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
        
        // Assign permissions to roles
        $systemAdmin->givePermissionTo(Permission::all());
        
        $hackathonAdmin->givePermissionTo([
            'manage-edition',
            'manage-tracks',
            'manage-teams',
            'manage-ideas',
            'review-ideas',
            'manage-workshops',
            'manage-checkins',
            'view-reports',
            'export-reports',
        ]);
        
        $trackSupervisor->givePermissionTo([
            'manage-teams',
            'manage-ideas',
            'review-ideas',
            'view-reports',
            'export-reports',
        ]);
        
        $workshopSupervisor->givePermissionTo([
            'manage-workshops',
            'manage-checkins',
            'view-reports',
        ]);
        
        // Create sample users for testing
        $edition = Edition::first();
        $track = Track::first();
        
        // Create HackathonAdmin user
        $hackAdmin = User::firstOrCreate(
            ['email' => 'hackadmin@example.com'],
            [
                'name' => 'Hackathon Admin',
                'password' => bcrypt('password'),
                'edition_id' => $edition?->id,
            ]
        );
        $hackAdmin->assignRole('hackathon-admin');
        
        // Create TrackSupervisor user
        $trackSuper = User::firstOrCreate(
            ['email' => 'tracksupervisor@example.com'],
            [
                'name' => 'Track Supervisor',
                'password' => bcrypt('password'),
            ]
        );
        $trackSuper->assignRole('track-supervisor');
        
        // Assign track to supervisor if track exists
        if ($track) {
            $track->supervisors()->syncWithoutDetaching([$trackSuper->id]);
        }
        
        echo "Roles and permissions seeded successfully!\n";
        echo "Test users created:\n";
        echo "- HackathonAdmin: hackadmin@example.com (password: password)\n";
        echo "- TrackSupervisor: tracksupervisor@example.com (password: password)\n";
    }
}
