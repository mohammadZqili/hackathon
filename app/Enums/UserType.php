<?php

namespace App\Enums;

enum UserType: string
{
    case ADMIN = 'system_admin';
    case HACKATHON_ADMIN = 'hackathon_admin';
    case TRACK_SUPERVISOR = 'track_supervisor';
    case TEAM_LEADER = 'team_leader';
    case TEAM_MEMBER = 'team_member';
    case VISITOR = 'visitor';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'System Admin (Super User)',
            self::HACKATHON_ADMIN => 'Hackathon Admin',
            self::TRACK_SUPERVISOR => 'Track Supervisor',
            self::TEAM_LEADER => 'Team Leader',
            self::TEAM_MEMBER => 'Team Member',
            self::VISITOR => 'Visitor',
        };
    }

    public function permissions(): array
    {
        return match ($this) {
            self::ADMIN => [
                // System Admin has ALL permissions - SUPER USER
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
                'view_all_ideas',
                'evaluate_ideas',
                'approve_ideas',
                'reject_ideas',
                'request_idea_modifications',
                'delete_ideas',
                'export_ideas',
                'manage_track',
                'evaluate_track_ideas',
                'communicate_teams',
                'schedule_meetings',
                'view_track_reports',
                'export_track_data',
                'create_team',
                'manage_team',
                'delete_team',
                'manage_team_members',
                'submit_idea',
                'edit_idea',
                'delete_idea',
                'upload_files',
                'delete_files',
                'join_team',
                'leave_team',
                'contribute_idea',
                'view_team_data',
                'register_workshops',
                'manage_workshop_registrations',
                'scan_attendance',
                'view_attendance_reports',
                'export_attendance_data',
                'manage_workshop_capacity',
                'create_users',
                'edit_users',
                'delete_users',
                'suspend_users',
                'activate_users',
                'reset_passwords',
                'impersonate_users',
                'view_user_activity',
                'export_user_data',
                'send_notifications',
                'send_emails',
                'send_sms',
                'manage_templates',
                'view_communication_logs',
                'view_reports',
                'view_analytics',
                'generate_reports',
                'export_reports',
                'view_statistics',
                'view_dashboards',
                'manage_content',
                'manage_pages',
                'manage_media',
                'manage_files',
                'view_public_content',
                'access_admin_panel',
                'access_api',
                'manage_security',
                'view_security_logs',
                'manage_2fa',
                'manage_ip_restrictions',
                'manage_rate_limits',
            ],
            self::HACKATHON_ADMIN => [
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
            self::TRACK_SUPERVISOR => [
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
            self::TEAM_LEADER => [
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
            self::TEAM_MEMBER => [
                'join_team',
                'contribute_idea',
                'view_team_data',
                'register_workshops',
                'view_public_content',
                'upload_files',
            ],
            self::VISITOR => [
                'register_workshops',
                'view_public_content',
            ],
        };

    }

    /**
     * Check if this user type is a super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this === self::ADMIN;
    }

    /**
     * Check if this user type has administrative privileges
     */
    public function isAdmin(): bool
    {
        return in_array($this, [self::ADMIN, self::HACKATHON_ADMIN]);
    }

    /**
     * Check if this user type can manage teams
     */
    public function canManageTeams(): bool
    {
        return in_array($this, [self::ADMIN, self::HACKATHON_ADMIN, self::TEAM_LEADER]);
    }

    /**
     * Check if this user type can evaluate ideas
     */
    public function canEvaluateIdeas(): bool
    {
        return in_array($this, [self::ADMIN, self::HACKATHON_ADMIN, self::TRACK_SUPERVISOR]);
    }
}
