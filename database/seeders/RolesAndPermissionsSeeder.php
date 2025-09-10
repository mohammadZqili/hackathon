<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Enums\UserType;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create comprehensive permissions list for System Admin (SUPER USER)
        $permissions = [
            // System-wide management (Super Admin exclusive)
            'manage_system',
            'manage_users',
            'manage_roles',
            'manage_permissions',
            'manage_settings',
            'manage_database',
            'manage_backups',
            'manage_logs',
            'manage_api_keys',
            'manage_integrations',
            'view_system_reports',
            'view_audit_logs',
            'export_all_data',
            'import_data',
            'system_maintenance',
            
            // Hackathon management
            'manage_hackathons',
            'create_hackathon',
            'edit_hackathon',
            'delete_hackathon',
            'archive_hackathon',
            'manage_hackathon',
            'manage_tracks',
            'create_track',
            'edit_track',
            'delete_track',
            'assign_supervisors',
            'manage_teams',
            'manage_workshops',
            'create_workshop',
            'edit_workshop',
            'delete_workshop',
            'manage_speakers',
            'manage_organizations',
            'manage_news',
            'create_news',
            'edit_news',
            'delete_news',
            'publish_news',
            'manage_social_media',
            'view_hackathon_reports',
            'export_hackathon_data',
            
            // Ideas management
            'view_all_ideas',
            'evaluate_ideas',
            'approve_ideas',
            'reject_ideas',
            'request_idea_modifications',
            'delete_ideas',
            'export_ideas',
            
            // Track supervision
            'manage_track',
            'evaluate_track_ideas',
            'communicate_teams',
            'schedule_meetings',
            'view_track_reports',
            'export_track_data',
            
            // Team management
            'create_team',
            'manage_team',
            'delete_team',
            'manage_team_members',
            'submit_idea',
            'edit_idea',
            'delete_idea',
            'upload_files',
            'delete_files',
            
            // Team membership
            'join_team',
            'leave_team',
            'contribute_idea',
            'view_team_data',
            
            // Workshop management
            'register_workshops',
            'manage_workshop_registrations',
            'scan_attendance',
            'view_attendance_reports',
            'export_attendance_data',
            'manage_workshop_capacity',
            
            // User management
            'create_users',
            'edit_users',
            'delete_users',
            'suspend_users',
            'activate_users',
            'reset_passwords',
            'impersonate_users',
            'view_user_activity',
            'export_user_data',
            
            // Communication
            'send_notifications',
            'send_emails',
            'send_sms',
            'manage_templates',
            'view_communication_logs',
            
            // Reports and Analytics
            'view_reports',
            'view_analytics',
            'generate_reports',
            'export_reports',
            'view_statistics',
            'view_dashboards',
            
            // Content management
            'manage_content',
            'manage_pages',
            'manage_media',
            'manage_files',
            
            // General permissions
            'view_public_content',
            'access_admin_panel',
            'access_api',
            
            // Security permissions
            'manage_security',
            'view_security_logs',
            'manage_2fa',
            'manage_ip_restrictions',
            'manage_rate_limits',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $rolePermissions = [
            // SYSTEM ADMIN - SUPER USER with ALL permissions
            UserType::ADMIN->value => $permissions, // Gets ALL permissions

            UserType::HACKATHON_ADMIN->value => [
                'manage_hackathon',
                'manage_tracks',
                'create_track',
                'edit_track',
                'delete_track',
                'assign_supervisors',
                'manage_teams',
                'manage_workshops',
                'create_workshop',
                'edit_workshop',
                'delete_workshop',
                'manage_speakers',
                'manage_organizations',
                'manage_news',
                'create_news',
                'edit_news',
                'delete_news',
                'publish_news',
                'view_hackathon_reports',
                'export_hackathon_data',
                'view_all_ideas',
                'evaluate_ideas',
                'send_notifications',
                'send_emails',
                'view_reports',
                'view_analytics',
                'manage_content',
                'access_admin_panel',
                'register_workshops',
                'view_public_content',
            ],

            UserType::TRACK_SUPERVISOR->value => [
                'evaluate_ideas',
                'evaluate_track_ideas',
                'manage_track',
                'communicate_teams',
                'schedule_meetings',
                'view_track_reports',
                'export_track_data',
                'register_workshops',
                'view_public_content',
                'send_notifications',
                'access_admin_panel',
            ],

            UserType::TEAM_LEADER->value => [
                'create_team',
                'manage_team',
                'manage_team_members',
                'submit_idea',
                'edit_idea',
                'upload_files',
                'register_workshops',
                'view_team_data',
                'view_public_content',
            ],

            UserType::TEAM_MEMBER->value => [
                'join_team',
                'contribute_idea',
                'view_team_data',
                'register_workshops',
                'view_public_content',
                'upload_files',
            ],

            UserType::VISITOR->value => [
                'register_workshops',
                'view_public_content',
            ],
        ];

        foreach ($rolePermissions as $roleName => $rolePerms) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($rolePerms);
        }

        $this->command->info('Roles and permissions created successfully!');
        $this->command->info('System Admin (admin) role has been granted ALL permissions as SUPER USER!');
    }
}
