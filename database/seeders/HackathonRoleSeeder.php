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

        // Create comprehensive permissions
        $permissions = [
            // System Admin exclusive permissions
            'manage_system',
            'manage_all_editions',
            'manage_all_users',
            'manage_system_settings',
            'manage_roles_permissions',
            'view_all_analytics',
            'manage_backups',
            'manage_database',
            'view_audit_logs',
            'system_maintenance',

            // Edition Management (Hackathon Admin & Track Supervisor)
            'manage_current_edition',
            'view_edition',
            'export_edition_data',
            'view_edition_analytics',

            // User Management
            'manage_edition_users',
            'create_users',
            'edit_users',
            'delete_users',
            'view_users',
            'assign_roles',

            // Team Management
            'manage_teams',
            'create_team',
            'edit_team',
            'delete_team',
            'view_teams',
            'approve_teams',
            'manage_team_members',
            'add_team_members',
            'remove_team_members',

            // Idea Management
            'manage_ideas',
            'create_idea',
            'edit_idea',
            'delete_idea',
            'view_ideas',
            'review_ideas',
            'approve_ideas',
            'reject_ideas',
            'provide_feedback',
            'score_ideas',

            // Track Management
            'manage_tracks',
            'create_track',
            'edit_track',
            'delete_track',
            'view_tracks',
            'assign_track_supervisors',

            // Workshop Management
            'manage_workshops',
            'create_workshop',
            'edit_workshop',
            'delete_workshop',
            'view_workshops',
            'manage_workshop_attendees',
            'manage_checkins',

            // News Management
            'manage_news',
            'create_news',
            'edit_news',
            'delete_news',
            'publish_news',
            'view_news',

            // Organization & Speaker Management
            'manage_organizations',
            'manage_speakers',

            // Communication
            'send_notifications',
            'send_emails',
            'manage_templates',

            // Reports & Analytics
            'view_reports',
            'export_reports',
            'view_analytics',
            'generate_reports',

            // Team Leader specific
            'manage_own_team',
            'submit_idea',
            'update_own_idea',
            'invite_members',

            // Team Member specific
            'view_team',
            'view_idea',
            'contribute_idea',

            // Common permissions
            'register_workshops',
            'update_profile',
            'view_public_content',
            'access_dashboard',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $roles = [
            'system_admin' => Permission::all(), // System admin gets ALL permissions

            'hackathon_admin' => [
                // Edition Management
                'manage_current_edition',
                'view_edition',
                'export_edition_data',
                'view_edition_analytics',

                // User Management (within edition)
                'manage_edition_users',
                'create_users',
                'edit_users',
                'delete_users',
                'view_users',
                'assign_roles',

                // Full Team Management
                'manage_teams',
                'create_team',
                'edit_team',
                'delete_team',
                'view_teams',
                'approve_teams',
                'manage_team_members',
                'add_team_members',
                'remove_team_members',

                // Full Idea Management
                'manage_ideas',
                'create_idea',
                'edit_idea',
                'delete_idea',
                'view_ideas',
                'review_ideas',
                'approve_ideas',
                'reject_ideas',
                'provide_feedback',
                'score_ideas',

                // Full Track Management
                'manage_tracks',
                'create_track',
                'edit_track',
                'delete_track',
                'view_tracks',
                'assign_track_supervisors',

                // Full Workshop Management
                'manage_workshops',
                'create_workshop',
                'edit_workshop',
                'delete_workshop',
                'view_workshops',
                'manage_workshop_attendees',
                'manage_checkins',

                // News Management
                'manage_news',
                'create_news',
                'edit_news',
                'delete_news',
                'publish_news',
                'view_news',

                // Organization & Speaker Management
                'manage_organizations',
                'manage_speakers',

                // Communication
                'send_notifications',
                'send_emails',
                'manage_templates',

                // Reports & Analytics
                'view_reports',
                'export_reports',
                'view_analytics',
                'generate_reports',

                // Common
                'access_dashboard',
                'view_public_content',
                'update_profile',
            ],

            'track_supervisor' => [
                // Edition access (read-only)
                'view_edition',
                'view_edition_analytics',
                'export_edition_data',

                // User Management (view only)
                'view_users',

                // Full Team Management for assigned tracks
                'manage_teams',
                'create_team',
                'edit_team',
                'delete_team',
                'view_teams',
                'approve_teams',
                'manage_team_members',
                'add_team_members',
                'remove_team_members',

                // Full Idea Management for assigned tracks
                'manage_ideas',
                'create_idea',
                'edit_idea',
                'delete_idea',
                'view_ideas',
                'review_ideas',
                'approve_ideas',
                'reject_ideas',
                'provide_feedback',
                'score_ideas',

                // Track Management (view only, manage assigned)
                'view_tracks',

                // Full Workshop Management for edition
                'manage_workshops',
                'create_workshop',
                'edit_workshop',
                'delete_workshop',
                'view_workshops',
                'manage_workshop_attendees',
                'manage_checkins',

                // News Management
                'manage_news',
                'create_news',
                'edit_news',
                'delete_news',
                'publish_news',
                'view_news',

                // Organization & Speaker Management
                'manage_organizations',
                'manage_speakers',

                // Communication
                'send_notifications',
                'send_emails',

                // Reports & Analytics for assigned tracks
                'view_reports',
                'export_reports',
                'view_analytics',
                'generate_reports',

                // Common
                'access_dashboard',
                'view_public_content',
                'update_profile',
            ],

            'workshop_supervisor' => [
                // Workshop Management for assigned workshops
                'manage_workshops',
                'edit_workshop',
                'view_workshops',
                'manage_workshop_attendees',
                'manage_checkins',

                // View permissions
                'view_edition',
                'view_users',
                'view_teams',
                'view_ideas',
                'view_tracks',
                'view_news',

                // Reports for workshops
                'view_reports',
                'export_reports',

                // Common
                'access_dashboard',
                'view_public_content',
                'update_profile',
            ],

            'team_leader' => [
                'create_team',
                'manage_own_team',
                'manage_team_members',
                'invite_members',
                'submit_idea',
                'update_own_idea',
                'view_team',
                'view_idea',
                'view_teams',
                'view_ideas',
                'view_workshops',
                'register_workshops',
                'view_news',
                'view_public_content',
                'update_profile',
                'access_dashboard',
            ],

            'team_member' => [
                'view_team',
                'view_idea',
                'contribute_idea',
                'view_workshops',
                'register_workshops',
                'view_news',
                'view_public_content',
                'update_profile',
                'access_dashboard',
            ],

            'visitor' => [
                'view_workshops',
                'register_workshops',
                'view_news',
                'view_public_content',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);

            if ($roleName === 'system_admin') {
                // System admin gets ALL permissions
                $role->syncPermissions(Permission::all());
            } else {
                // Other roles get specific permissions
                $role->syncPermissions($rolePermissions);
            }
        }

        $this->command->info('✅ Hackathon roles and permissions created successfully.');
        $this->command->info('🔐 Permission Summary:');
        $this->command->info('   - System Admin: Full system access (ALL permissions)');
        $this->command->info('   - Hackathon Admin: Full edition management');
        $this->command->info('   - Track Supervisor: Full access to assigned tracks (teams, ideas, workshops)');
        $this->command->info('   - Workshop Supervisor: Manage assigned workshops');
        $this->command->info('   - Team Leader: Manage own team and idea');
        $this->command->info('   - Team Member: View and contribute to team');
        $this->command->info('   - Visitor: View public content only');
    }
}