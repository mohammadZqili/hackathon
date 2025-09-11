<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Team;
use App\Models\Idea;
use App\Models\User;

class FixDashboardDataIntegrity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hackathon:fix-dashboard-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix data integrity issues causing dashboard null reference errors';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Starting dashboard data integrity fix...');

        // Step 1: Find and fix teams without valid leaders
        $this->info('📋 Checking teams without valid leaders...');
        
        $teamsWithoutLeaders = Team::whereNull('leader_id')
            ->orWhereNotIn('leader_id', User::pluck('id'))
            ->count();

        if ($teamsWithoutLeaders > 0) {
            $this->warn("Found {$teamsWithoutLeaders} teams without valid leaders");
            
            // Try to assign leaders from team_members table
            $fixed = DB::update('
                UPDATE teams 
                SET leader_id = (
                    SELECT tm.user_id 
                    FROM team_members tm 
                    WHERE tm.team_id = teams.id 
                    AND tm.role = "leader" 
                    AND tm.status = "accepted"
                    LIMIT 1
                ) 
                WHERE leader_id IS NULL 
                OR leader_id NOT IN (SELECT id FROM users)
            ');

            $this->info("✅ Fixed {$fixed} teams by assigning leaders from team_members");

            // Delete teams that still can't be fixed
            $deleted = Team::whereNull('leader_id')
                ->orWhereNotIn('leader_id', User::pluck('id'))
                ->delete();

            if ($deleted > 0) {
                $this->warn("🗑️ Deleted {$deleted} orphaned teams without valid leaders");
            }
        } else {
            $this->info('✅ All teams have valid leaders');
        }

        // Step 2: Find and fix ideas without valid teams
        $this->info('💡 Checking ideas without valid teams...');
        
        $ideasWithoutTeams = Idea::whereNull('team_id')
            ->orWhereNotIn('team_id', Team::pluck('id'))
            ->count();

        if ($ideasWithoutTeams > 0) {
            $this->warn("Found {$ideasWithoutTeams} ideas without valid teams");
            
            $deleted = Idea::whereNull('team_id')
                ->orWhereNotIn('team_id', Team::pluck('id'))
                ->delete();

            $this->warn("🗑️ Deleted {$deleted} orphaned ideas");
        } else {
            $this->info('✅ All ideas have valid teams');
        }

        // Step 3: Check current edition
        $this->info('🏆 Checking current hackathon edition...');
        
        $currentEdition = DB::table('hackathon_editions')
            ->where('is_current', true)
            ->first();

        if (!$currentEdition) {
            $this->warn('⚠️ No current hackathon edition found');
            
            // Try to set the latest edition as current
            $latestEdition = DB::table('hackathon_editions')
                ->orderBy('created_at', 'desc')
                ->first();

            if ($latestEdition) {
                DB::table('hackathon_editions')
                    ->where('id', $latestEdition->id)
                    ->update(['is_current' => true]);
                
                $this->info("✅ Set latest edition '{$latestEdition->name}' as current");
            } else {
                $this->warn('⚠️ No hackathon editions found. You may need to create one.');
            }
        } else {
            $this->info("✅ Current edition: {$currentEdition->name}");
        }

        // Step 4: Create default data if needed
        $this->info('📊 Checking for minimum required data...');
        
        $userCount = User::count();
        $teamCount = Team::count();
        
        if ($userCount === 0) {
            $this->warn('⚠️ No users found. The dashboard may show empty statistics.');
        } else {
            $this->info("✅ Found {$userCount} users");
        }

        if ($teamCount === 0) {
            $this->warn('⚠️ No teams found. The dashboard may show empty statistics.');
        } else {
            $this->info("✅ Found {$teamCount} teams");
        }

        // Step 5: Test dashboard query
        $this->info('🧪 Testing dashboard queries...');
        
        try {
            $recentTeams = Team::with('leader')
                ->whereNotNull('leader_id')
                ->whereHas('leader')
                ->latest()
                ->take(5)
                ->get();

            $recentIdeas = Idea::with(['team', 'team.leader'])
                ->whereHas('team')
                ->latest()
                ->take(5)
                ->get();

            $this->info("✅ Dashboard queries working - {$recentTeams->count()} recent teams, {$recentIdeas->count()} recent ideas");

        } catch (\Exception $e) {
            $this->error("❌ Dashboard queries still failing: " . $e->getMessage());
            return 1;
        }

        $this->info('');
        $this->info('🎉 Dashboard data integrity fix completed successfully!');
        $this->info('💡 You can now access: http://localhost:8000/hackathon-admin/dashboard');
        
        return 0;
    }
}
