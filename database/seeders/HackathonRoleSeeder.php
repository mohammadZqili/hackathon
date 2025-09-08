<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class HackathonRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // System Admin permissions
            'manage_editions',
            'manage_all_users',
            'manage_system_settings',
            'view_all_analytics',
            'manage_backups',
            
            // Hackathon Admin permissions
            'manage_current_edition',
            'manage_teams',
            'manage_ideas',
            'manage_workshops',
            'manage_news',
            'approve_teams',
            'approve_ideas',
            'view_edition_analytics',
            
            // Track Supervisor permissions
            'manage_track_teams',
            'review_track_ideas',
            'provide_feedback',
            'export_track_data',
            
            // Team Leader permissions
            'create_team',
            'manage_own_team',
            'submit_idea',
            'update_idea',
            'invite_members',
            'register_workshops',
            
            // Team Member permissions
            'view_team',
            'view_idea',
            'view_workshops',
            'update_profile',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $roles = [
            'system_admin' => [
                'manage_editions',
                'manage_all_users',
                'manage_system_settings',
                'view_all_analytics',
                'manage_backups',
                'manage_current_edition',
                'manage_teams',
                'manage_ideas',
                'manage_workshops',
                'manage_news',
                'approve_teams',
                'approve_ideas',
                'view_edition_analytics',
            ],
            'hackathon_admin' => [
                'manage_current_edition',
                'manage_teams',
                'manage_ideas',
                'manage_workshops',
                'manage_news',
                'approve_teams',
                'approve_ideas',
                'view_edition_analytics',
                'provide_feedback',
            ],
            'track_supervisor' => [
                'manage_track_teams',
                'review_track_ideas',
                'provide_feedback',
                'export_track_data',
                'view_team',
                'view_idea',
                'view_workshops',
            ],
            'team_leader' => [
                'create_team',
                'manage_own_team',
                'submit_idea',
                'update_idea',
                'invite_members',
                'register_workshops',
                'view_team',
                'view_idea',
                'view_workshops',
                'update_profile',
            ],
            'team_member' => [
                'view_team',
                'view_idea',
                'view_workshops',
                'update_profile',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($rolePermissions); // Use sync to avoid duplicates
        }

        $this->command->info('Hackathon roles and permissions created successfully.');
    }
}
