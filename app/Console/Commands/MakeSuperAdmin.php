<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Enums\UserType;

class MakeSuperAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:make-super-admin {email : The email of the user to make super admin}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Grant super admin privileges to a user (ALL permissions)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("User with email {$email} not found!");
            return 1;
        }
        
        $this->info("Current user: {$user->name} ({$user->email})");
        $this->info("Current roles: " . $user->getRoleNames()->implode(', '));
        
        if ($this->confirm("Are you sure you want to grant SUPER ADMIN privileges (ALL permissions) to this user?")) {
            // Remove all existing roles
            $user->syncRoles([]);
            
            // Assign super admin role
            $user->assignRole(UserType::ADMIN->value);
            
            // Update user type
            $user->update(['user_type' => UserType::ADMIN->value]);
            
            $this->info("✅ SUCCESS!");
            $this->info("{$user->name} is now a SUPER ADMIN with ALL permissions!");
            $this->line("");
            $this->info("This user now has the following capabilities:");
            $this->line("• Full system control");
            $this->line("• Can manage all users, roles, and permissions");
            $this->line("• Can manage all hackathons and their data");
            $this->line("• Can access all areas of the system");
            $this->line("• Can export and import all data");
            $this->line("• Can view audit logs and security settings");
            $this->line("• Can impersonate other users");
            $this->line("• Bypasses all permission checks");
            
            return 0;
        }
        
        $this->info("Operation cancelled.");
        return 0;
    }
}
