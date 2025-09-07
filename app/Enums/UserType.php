<?php

namespace App\Enums;

enum UserType: string
{
    case ADMIN = 'admin';
    case HACKATHON_ADMIN = 'hackathon_admin';
    case TRACK_SUPERVISOR = 'track_supervisor';
    case TEAM_LEADER = 'team_leader';
    case TEAM_MEMBER = 'team_member';
    case VISITOR = 'visitor';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'System Admin',
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
                'manage_system',
                'manage_users',
                'manage_hackathons',
                'manage_settings',
                'view_reports',
            ],
            self::HACKATHON_ADMIN => [
                'manage_hackathon',
                'manage_tracks',
                'manage_teams',
                'manage_workshops',
                'manage_news',
                'view_reports',
            ],
            self::TRACK_SUPERVISOR => [
                'evaluate_ideas',
                'manage_track',
                'communicate_teams',
                'view_track_reports',
            ],
            self::TEAM_LEADER => [
                'create_team',
                'manage_team',
                'submit_idea',
                'register_workshops',
            ],
            self::TEAM_MEMBER => [
                'join_team',
                'contribute_idea',
                'register_workshops',
            ],
            self::VISITOR => [
                'register_workshops',
                'view_public_content',
            ],
        };
    }
}
