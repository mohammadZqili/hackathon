#!/bin/bash

# FINAL CORRECTED FAST IMPLEMENTATION SCRIPT
# Fixes: Uses 'user_type' instead of 'role'

echo "🚀 Starting CORRECTED Fast Implementation..."
echo "==========================================="
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

# First, create a migration to add missing fields
echo -e "${GREEN}Creating migration for missing user fields...${NC}"

cat > database/migrations/$(date +%Y_%m_%d_%H%M%S)_add_missing_hackathon_fields.php << 'EOF'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add missing fields only if they don't exist
            if (!Schema::hasColumn('users', 'team_id')) {
                $table->foreignId('team_id')->nullable()->after('user_type')
                    ->constrained('teams')->nullOnDelete();
            }
            
            if (!Schema::hasColumn('users', 'edition_id')) {
                $table->foreignId('edition_id')->nullable()->after('team_id')
                    ->constrained('editions')->nullOnDelete();
            }
            
            if (!Schema::hasColumn('users', 'hackathon_edition_id')) {
                $table->foreignId('hackathon_edition_id')->nullable()->after('edition_id')
                    ->constrained('editions')->nullOnDelete();
            }
            
            // Add workshop_supervisor to user_type enum if needed
            \DB::statement("ALTER TABLE users MODIFY COLUMN user_type ENUM(
                'system_admin', 
                'hackathon_admin', 
                'track_supervisor',
                'workshop_supervisor',
                'team_leader', 
                'team_member', 
                'visitor'
            ) DEFAULT 'visitor'");
        });
        
        // Create supervisor relations tables if they don't exist
        if (!Schema::hasTable('track_supervisors')) {
            Schema::create('track_supervisors', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('track_id')->constrained()->cascadeOnDelete();
                $table->timestamps();
                
                $table->unique(['user_id', 'track_id']);
            });
        }
        
        if (!Schema::hasTable('workshop_supervisors')) {
            Schema::create('workshop_supervisors', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('workshop_id')->constrained()->cascadeOnDelete();
                $table->timestamps();
                
                $table->unique(['user_id', 'workshop_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('workshop_supervisors');
        Schema::dropIfExists('track_supervisors');
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
            $table->dropForeign(['edition_id']);
            $table->dropForeign(['hackathon_edition_id']);
            
            $table->dropColumn(['team_id', 'edition_id', 'hackathon_edition_id']);
        });
    }
};
EOF

# Update BaseService to use user_type instead of role
echo -e "${GREEN}Updating BaseService to use user_type...${NC}"

cat > app/Services/BaseService.php << 'EOF'
<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class BaseService
{
    /**
     * Apply role-based filtering to any query
     * Note: Using user_type field, not role
     */
    protected function scopeByRole(Builder $query, User $user, string $model = ''): Builder
    {
        return match($user->user_type) {  // Changed from $user->role
            'system_admin' => $query,
            'hackathon_admin' => $this->scopeForHackathonAdmin($query, $user, $model),
            'track_supervisor' => $this->scopeForTrackSupervisor($query, $user, $model),
            'workshop_supervisor' => $this->scopeForWorkshopSupervisor($query, $user, $model),
            'team_leader', 'team_member' => $this->scopeForTeamMember($query, $user, $model),
            'visitor' => $query->whereRaw('1 = 0'),
            default => $query->whereRaw('1 = 0')
        };
    }
    
    private function scopeForHackathonAdmin($query, $user, $model)
    {
        $editionId = $user->edition_id ?? $user->hackathon_edition_id;
        
        return match($model) {
            'Team' => $query->where('edition_id', $editionId),
            'Idea' => $query->whereHas('team', fn($q) => $q->where('edition_id', $editionId)),
            'Workshop' => $query->where('edition_id', $editionId),
            'Track' => $query->where('edition_id', $editionId),
            'User' => $query->whereHas('teams', fn($q) => $q->where('edition_id', $editionId)),
            'News' => $query->where('edition_id', $editionId),
            default => $query
        };
    }
    
    private function scopeForTrackSupervisor($query, $user, $model)
    {
        // Get supervised track IDs from relation
        $trackIds = \DB::table('track_supervisors')
            ->where('user_id', $user->id)
            ->pluck('track_id')
            ->toArray();
        
        if (empty($trackIds)) {
            return $query->whereRaw('1 = 0');
        }
        
        return match($model) {
            'Team' => $query->whereIn('track_id', $trackIds),
            'Idea' => $query->whereIn('track_id', $trackIds),
            'Track' => $query->whereIn('id', $trackIds),
            default => $query->whereRaw('1 = 0')
        };
    }
    
    private function scopeForWorkshopSupervisor($query, $user, $model)
    {
        // Get supervised workshop IDs from relation
        $workshopIds = \DB::table('workshop_supervisors')
            ->where('user_id', $user->id)
            ->pluck('workshop_id')
            ->toArray();
        
        if (empty($workshopIds)) {
            return $query->whereRaw('1 = 0');
        }
        
        return match($model) {
            'Workshop' => $query->whereIn('id', $workshopIds),
            'WorkshopRegistration' => $query->whereIn('workshop_id', $workshopIds),
            'WorkshopAttendance' => $query->whereIn('workshop_id', $workshopIds),
            default => $query->whereRaw('1 = 0')
        };
    }
    
    private function scopeForTeamMember($query, $user, $model)
    {
        $teamId = $user->team_id;
        
        if (!$teamId) {
            return $query->whereRaw('1 = 0');
        }
        
        return match($model) {
            'Team' => $query->where('id', $teamId),
            'Idea' => $query->where('team_id', $teamId),
            'TeamMember' => $query->where('team_id', $teamId),
            default => $query->whereRaw('1 = 0')
        };
    }
    
    protected function getBasePermissions(User $user): array
    {
        return match($user->user_type) {  // Changed from $user->role
            'system_admin' => [
                'canCreate' => true,
                'canEdit' => true,
                'canDelete' => true,
                'canExport' => true,
                'canViewAll' => true,
                'canManageUsers' => true,
                'canManageSettings' => true
            ],
            'hackathon_admin' => [
                'canCreate' => true,
                'canEdit' => true,
                'canDelete' => false,
                'canExport' => true,
                'canViewAll' => true,
                'canManageUsers' => false,
                'canManageSettings' => false
            ],
            'track_supervisor' => [
                'canCreate' => false,
                'canEdit' => false,
                'canDelete' => false,
                'canExport' => true,
                'canViewAll' => true,
                'canReview' => true,
                'canScore' => true
            ],
            'workshop_supervisor' => [
                'canCreate' => false,
                'canEdit' => false,
                'canDelete' => false,
                'canExport' => true,
                'canCheckIn' => true,
                'canViewAttendance' => true
            ],
            'team_leader' => [
                'canCreate' => true,
                'canEdit' => true,
                'canDelete' => false,
                'canExport' => false,
                'canInviteMembers' => true,
                'canSubmitIdea' => true
            ],
            'team_member' => [
                'canCreate' => false,
                'canEdit' => false,
                'canDelete' => false,
                'canExport' => false,
                'canViewTeam' => true,
                'canLeaveTeam' => true
            ],
            'visitor' => [
                'canRegisterWorkshop' => true,
                'canViewPublic' => true
            ],
            default => []
        };
    }
}
EOF

# Update User model to add necessary relationships
echo -e "${GREEN}Adding relationships to User model...${NC}"

cat >> app/Models/User.php << 'EOF'

    // =====================================================
    // Hackathon Role Relationships
    // =====================================================
    
    /**
     * Get supervised tracks for track supervisors
     */
    public function supervisedTracks()
    {
        return $this->belongsToMany(Track::class, 'track_supervisors', 'user_id', 'track_id')
                    ->withTimestamps();
    }
    
    /**
     * Get supervised workshops for workshop supervisors
     */
    public function supervisedWorkshops()
    {
        return $this->belongsToMany(Workshop::class, 'workshop_supervisors', 'user_id', 'workshop_id')
                    ->withTimestamps();
    }
    
    /**
     * Get the user's role (alias for user_type)
     */
    public function getRoleAttribute()
    {
        return $this->user_type;
    }
    
    /**
     * Set the user's role (alias for user_type)
     */
    public function setRoleAttribute($value)
    {
        $this->user_type = $value;
    }
EOF

# Create the updated seeder
echo -e "${GREEN}Creating comprehensive test seeder...${NC}"

cat > database/seeders/CompleteFastImplementationSeeder.php << 'EOF'
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Edition;
use App\Models\Team;
use App\Models\Track;
use App\Models\Workshop;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class CompleteFastImplementationSeeder extends Seeder
{
    public function run()
    {
        echo "========================================\n";
        echo "Creating test data for all 7 roles...\n";
        echo "========================================\n\n";
        
        // Create test edition
        $edition = Edition::firstOrCreate(
            ['name' => '2024 Hackathon'],
            [
                'year' => 2024,
                'is_current' => true,
                'start_date' => now()->addDays(30),
                'end_date' => now()->addDays(33),
                'registration_start' => now(),
                'registration_end' => now()->addDays(20)
            ]
        );
        echo "✓ Edition created: {$edition->name}\n";
        
        // Create test tracks
        $track1 = Track::firstOrCreate(
            ['name' => 'Environment Track'],
            [
                'edition_id' => $edition->id,
                'description' => 'Environmental solutions'
            ]
        );
        
        $track2 = Track::firstOrCreate(
            ['name' => 'Technology Track'],
            [
                'edition_id' => $edition->id,
                'description' => 'Tech innovations'
            ]
        );
        echo "✓ Tracks created\n";
        
        // Create test workshops
        $workshop1 = Workshop::firstOrCreate(
            ['title' => 'Introduction to AI'],
            [
                'description' => 'Learn AI basics',
                'date' => now()->addDays(5),
                'start_time' => '10:00',
                'end_time' => '12:00',
                'location' => 'Room A101',
                'max_capacity' => 50,
                'is_public' => true,
                'edition_id' => $edition->id
            ]
        );
        
        $workshop2 = Workshop::firstOrCreate(
            ['title' => 'Web Development Workshop'],
            [
                'description' => 'Modern web development',
                'date' => now()->addDays(7),
                'start_time' => '14:00',
                'end_time' => '16:00',
                'location' => 'Room B202',
                'max_capacity' => 30,
                'is_public' => true,
                'edition_id' => $edition->id
            ]
        );
        echo "✓ Workshops created\n\n";
        
        // Create users for each role
        echo "Creating test users:\n";
        echo "-------------------\n";
        
        // System Admin
        $systemAdmin = User::updateOrCreate(
            ['email' => 'system@test.com'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('password'),
                'user_type' => 'system_admin',
                'email_verified_at' => now()
            ]
        );
        echo "✓ System Admin: system@test.com\n";
        
        // Hackathon Admin
        $hackathonAdmin = User::updateOrCreate(
            ['email' => 'hackathon@test.com'],
            [
                'name' => 'Hackathon Admin',
                'password' => Hash::make('password'),
                'user_type' => 'hackathon_admin',
                'edition_id' => $edition->id,
                'hackathon_edition_id' => $edition->id,
                'email_verified_at' => now()
            ]
        );
        echo "✓ Hackathon Admin: hackathon@test.com\n";
        
        // Track Supervisor
        $trackSupervisor = User::updateOrCreate(
            ['email' => 'track@test.com'],
            [
                'name' => 'Track Supervisor',
                'password' => Hash::make('password'),
                'user_type' => 'track_supervisor',
                'email_verified_at' => now()
            ]
        );
        
        // Assign tracks to supervisor
        DB::table('track_supervisors')->updateOrInsert(
            ['user_id' => $trackSupervisor->id, 'track_id' => $track1->id],
            ['created_at' => now(), 'updated_at' => now()]
        );
        DB::table('track_supervisors')->updateOrInsert(
            ['user_id' => $trackSupervisor->id, 'track_id' => $track2->id],
            ['created_at' => now(), 'updated_at' => now()]
        );
        echo "✓ Track Supervisor: track@test.com (supervises 2 tracks)\n";
        
        // Workshop Supervisor
        $workshopSupervisor = User::updateOrCreate(
            ['email' => 'workshop@test.com'],
            [
                'name' => 'Workshop Supervisor',
                'password' => Hash::make('password'),
                'user_type' => 'workshop_supervisor',
                'email_verified_at' => now()
            ]
        );
        
        // Assign workshops to supervisor
        DB::table('workshop_supervisors')->updateOrInsert(
            ['user_id' => $workshopSupervisor->id, 'workshop_id' => $workshop1->id],
            ['created_at' => now(), 'updated_at' => now()]
        );
        DB::table('workshop_supervisors')->updateOrInsert(
            ['user_id' => $workshopSupervisor->id, 'workshop_id' => $workshop2->id],
            ['created_at' => now(), 'updated_at' => now()]
        );
        echo "✓ Workshop Supervisor: workshop@test.com (supervises 2 workshops)\n";
        
        // Team Leader
        $teamLeader = User::updateOrCreate(
            ['email' => 'leader@test.com'],
            [
                'name' => 'Team Leader',
                'password' => Hash::make('password'),
                'user_type' => 'team_leader',
                'email_verified_at' => now()
            ]
        );
        echo "✓ Team Leader: leader@test.com\n";
        
        // Team Member
        $teamMember = User::updateOrCreate(
            ['email' => 'member@test.com'],
            [
                'name' => 'Team Member',
                'password' => Hash::make('password'),
                'user_type' => 'team_member',
                'email_verified_at' => now()
            ]
        );
        echo "✓ Team Member: member@test.com\n";
        
        // Visitor
        $visitor = User::updateOrCreate(
            ['email' => 'visitor@test.com'],
            [
                'name' => 'Visitor',
                'password' => Hash::make('password'),
                'user_type' => 'visitor',
                'email_verified_at' => now()
            ]
        );
        echo "✓ Visitor: visitor@test.com\n\n";
        
        // Create a test team
        $team = Team::firstOrCreate(
            ['name' => 'Test Team Alpha'],
            [
                'edition_id' => $edition->id,
                'track_id' => $track1->id,
                'leader_id' => $teamLeader->id,
                'description' => 'Test team for demo purposes',
                'max_members' => 5,
                'status' => 'active'
            ]
        );
        
        // Update team_id for users
        $teamLeader->update(['team_id' => $team->id]);
        $teamMember->update(['team_id' => $team->id]);
        
        // Add members to team if relation exists
        if (Schema::hasTable('team_members')) {
            DB::table('team_members')->updateOrInsert(
                ['team_id' => $team->id, 'user_id' => $teamLeader->id],
                ['role' => 'leader', 'joined_at' => now()]
            );
            DB::table('team_members')->updateOrInsert(
                ['team_id' => $team->id, 'user_id' => $teamMember->id],
                ['role' => 'member', 'joined_at' => now()]
            );
        }
        
        echo "✓ Team created: {$team->name}\n";
        echo "  - Leader: {$teamLeader->name}\n";
        echo "  - Member: {$teamMember->name}\n";
        
        echo "\n========================================\n";
        echo "✅ ALL TEST DATA CREATED SUCCESSFULLY!\n";
        echo "========================================\n\n";
        echo "Test Credentials (all passwords: 'password'):\n";
        echo "---------------------------------------------\n";
        echo "System Admin:        system@test.com\n";
        echo "Hackathon Admin:     hackathon@test.com\n";
        echo "Track Supervisor:    track@test.com\n";
        echo "Workshop Supervisor: workshop@test.com\n";
        echo "Team Leader:         leader@test.com\n";
        echo "Team Member:         member@test.com\n";
        echo "Visitor:             visitor@test.com\n";
        echo "========================================\n";
    }
}
EOF

# Run migrations
echo ""
echo -e "${GREEN}Running migrations...${NC}"
php artisan migrate --force

# Run the comprehensive seeder
echo ""
echo -e "${GREEN}Seeding test data...${NC}"
php artisan db:seed --class=CompleteFastImplementationSeeder --force

# Clear all caches
echo ""
echo -e "${GREEN}Clearing all caches...${NC}"
php artisan optimize:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Final summary
echo ""
echo "============================================"
echo -e "${GREEN}✅ IMPLEMENTATION COMPLETE!${NC}"
echo "============================================"
echo ""
echo "📊 What was done:"
echo "-----------------"
echo "✅ Added missing database fields (team_id, edition_id)"
echo "✅ Created supervisor relation tables"
echo "✅ Updated BaseService to use 'user_type' field"
echo "✅ Added User model relationships"
echo "✅ Created 7 test users with proper roles"
echo "✅ Set up test data (edition, tracks, workshops, team)"
echo ""
echo "🧪 Test the system:"
echo "-------------------"
echo "1. Start server: php artisan serve"
echo "2. Visit: http://localhost:8000/login"
echo "3. Login with test credentials (password: 'password')"
echo ""
echo "📝 Routes to test:"
echo "------------------"
echo "- /dashboard        → Role-specific dashboard"
echo "- /teams           → Teams (filtered by role)"
echo "- /ideas           → Ideas (filtered by role)"
echo "- /workshops       → Workshops (filtered by role)"
echo ""
echo "💡 Important Notes:"
echo "-------------------"
echo "- User role field is 'user_type' not 'role'"
echo "- All services use user_type for filtering"
echo "- Track/Workshop supervisors have relation tables"
echo "- Team members have team_id field"
echo ""
echo "🎉 System is ready for testing!"
echo ""
