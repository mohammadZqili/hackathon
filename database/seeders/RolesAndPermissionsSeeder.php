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

        // Create permissions
        $permissions = [
            // System management
            'manage_system',
            'manage_users',
            'manage_hackathons',
            'manage_settings',
            'view_reports',

            // Hackathon management
            'manage_hackathon',
            'manage_tracks',
            'manage_teams',
            'manage_workshops',
            'manage_news',
            'view_hackathon_reports',

            // Supervision
            'evaluate_ideas',
            'manage_track',
            'communicate_teams',
            'view_track_reports',

            // Team leadership
            'create_team',
            'manage_team',
            'submit_idea',
            'register_workshops',

            // Team membership
            'join_team',
            'contribute_idea',
            'view_team_data',

            // General
            'view_public_content',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        $rolePermissions = [
            UserType::ADMIN->value => $permissions,

            UserType::HACKATHON_ADMIN->value => [
                'manage_hackathon',
                'manage_tracks',
                'manage_teams',
                'manage_workshops',
                'manage_news',
                'view_hackathon_reports',
            ],

            UserType::TRACK_SUPERVISOR->value => [
                'evaluate_ideas',
                'manage_track',
                'communicate_teams',
                'view_track_reports',
                'register_workshops',
                'view_public_content',
            ],

            UserType::TEAM_LEADER->value => [
                'create_team',
                'manage_team',
                'submit_idea',
                'register_workshops',
                'view_public_content',
            ],

            UserType::TEAM_MEMBER->value => [
                'join_team',
                'contribute_idea',
                'view_team_data',
                'register_workshops',
                'view_public_content',
            ],

            UserType::VISITOR->value => [
                'register_workshops',
                'view_public_content',
            ],
        ];

        foreach ($rolePermissions as $roleName => $rolePerms) {
            $role = Role::create(['name' => $roleName]);
            $role->givePermissionTo($rolePerms);
        }

        $this->command->info('Roles and permissions created successfully!');
    }
}
