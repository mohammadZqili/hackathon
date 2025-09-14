<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class FixSystemAdminRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create system_admin role if it doesn't exist
        $systemAdminRole = Role::firstOrCreate(
            ['name' => 'system_admin'],
            ['guard_name' => 'web']
        );

        // Create manage-hackathon-editions permission if it doesn't exist
        $manageEditionsPermission = Permission::firstOrCreate(
            ['name' => 'manage-hackathon-editions'],
            ['guard_name' => 'web']
        );

        // Assign permission to role
        if (!$systemAdminRole->hasPermissionTo($manageEditionsPermission)) {
            $systemAdminRole->givePermissionTo($manageEditionsPermission);
        }

        // Find all users with user_type = 'system_admin' or 'admin'
        $adminUsers = User::whereIn('user_type', ['system_admin', 'admin'])->get();

        foreach ($adminUsers as $user) {
            // Assign the system_admin role if they don't have it
            if (!$user->hasRole('system_admin')) {
                $user->assignRole('system_admin');
                $this->command->info("Assigned system_admin role to: {$user->email}");
            }
        }

        // Also specifically check for admin@guacpanel.com
        $mainAdmin = User::where('email', 'admin@guacpanel.com')->first();
        if ($mainAdmin && !$mainAdmin->hasRole('system_admin')) {
            $mainAdmin->assignRole('system_admin');
            $this->command->info("Assigned system_admin role to main admin: admin@guacpanel.com");
        }

        $this->command->info('System admin roles have been fixed successfully!');
    }
}
