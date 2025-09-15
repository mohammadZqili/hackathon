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
        $this->command->info('🌱 Starting database seeding...');
        
        $this->call([
            // ========================================
            // CORE SYSTEM SEEDERS (Must run first)
            // ========================================
            RoleSeeder::class,                 // Basic user role
            SettingSeeder::class,               // System settings
            PermissionRoleSeeder::class,        // Basic permissions
            HackathonRoleSeeder::class,         // Hackathon-specific roles and permissions
            
            // ========================================
            // USER AND AUTHENTICATION
            // ========================================
            UserSeeder::class,                  // Creates all test users with proper roles
            
            // ========================================
            // HACKATHON CORE DATA
            // ========================================
            HackathonEditionSeeder::class,      // Creates hackathon editions
            HackathonSeeder::class,              // Creates hackathons
            
            // ========================================
            // ORGANIZATION AND SPEAKERS
            // ========================================
            OrganizationSeeder::class,          // Creates organizations for workshops/sponsors
            SpeakerSeeder::class,                // Creates speakers for workshops
            
            // ========================================
            // TRACKS AND SUPERVISORS
            // ========================================
            TrackSeeder::class,                 // Creates hackathon tracks
            TrackSupervisorSeeder::class,       // Assigns supervisors to tracks
            
            // ========================================
            // TEAMS AND COLLABORATION
            // ========================================
            TeamSeeder::class,                  // Creates teams with different statuses
            TeamMemberSeeder::class,             // Adds members to teams
            // TeamInvitationSeeder::class,     // Skipped - requires Jetstream
            
            // ========================================
            // IDEAS AND FILES
            // ========================================
            IdeaSeeder::class,                  // Creates ideas for teams
            IdeaFileSeeder::class,               // Adds file attachments to ideas
            IdeaAuditLogSeeder::class,           // Creates audit logs for ideas
            
            // ========================================
            // CONTENT AND ACTIVITIES
            // ========================================
            NewsSeeder::class,                  // Creates news/announcements
            WorkshopSeeder::class,               // Creates workshops
            
            // ========================================
            // NOTIFICATIONS
            // ========================================
            NotificationSeeder::class,           // Creates sample notifications for users
            
            // ========================================
            // ANALYTICS AND METRICS
            // ========================================
            FinancialMetricsSeeder::class,      // Sample financial/analytics data
            
            // ========================================
            // COMPREHENSIVE TEST DATA
            // ========================================
            DashboardTestDataSeeder::class,      // Additional comprehensive test data
        ]);
        
        $this->command->info('✅ Database seeding completed successfully!');
        $this->command->newLine();
        $this->command->info('📋 Available Test Accounts:');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('🔐 All passwords: password');
        $this->command->newLine();
        
        $this->command->info('👑 System Admin:');
        $this->command->info('   Email: superadmin@hackathon.com');
        $this->command->info('   Access: Full system access');
        $this->command->newLine();
        
        $this->command->info('🎯 Hackathon Admin:');
        $this->command->info('   Email: admin@hackathon.com');
        $this->command->info('   Access: Manage hackathons, teams, ideas');
        $this->command->newLine();
        
        $this->command->info('👨‍🏫 Track Supervisors:');
        $this->command->info('   Email: sarah.johnson@hackathon.com');
        $this->command->info('   Email: michael.chen@hackathon.com');
        $this->command->info('   Access: Manage assigned tracks, evaluate ideas');
        $this->command->newLine();
        
        $this->command->info('👥 Team Leaders:');
        $this->command->info('   Email: alice@team.com');
        $this->command->info('   Email: bob@team.com');
        $this->command->info('   Access: Manage teams, submit ideas');
        $this->command->newLine();
        
        $this->command->info('🧑‍💻 Team Members:');
        $this->command->info('   Email: member1@team.com to member10@team.com');
        $this->command->info('   Access: Contribute to team ideas');
        $this->command->newLine();
        
        $this->command->info('👤 Visitors:');
        $this->command->info('   Email: user1@example.com to user5@example.com');
        $this->command->info('   Access: View public content, register for workshops');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    }
}
