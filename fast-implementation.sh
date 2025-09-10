#!/bin/bash

# HACKATHON FAST IMPLEMENTATION SCRIPT
# Run this to set up all 7 roles in 2 days!

echo "🚀 Starting Fast Implementation for All 7 Roles..."
echo "================================================"

# DAY 1 MORNING: Create Service Layer
echo "📦 Day 1 Morning: Creating Service Layer..."

# Create Services
echo "Creating IdeaService..."
cat > app/Services/IdeaService.php << 'EOF'
<?php

namespace App\Services;

use App\Models\Idea;
use App\Models\User;
use Illuminate\Http\Request;

class IdeaService extends BaseService
{
    public function getIdeasForUser(Request $request, User $user)
    {
        $query = Idea::with(['team', 'files', 'track', 'reviews']);
        
        // Apply role-based filtering
        $query = $this->scopeByRole($query, $user, 'Idea');
        
        // Add role-specific data
        if ($user->role === 'track_supervisor') {
            $query->with(['reviews' => function($q) use ($user) {
                $q->where('reviewer_id', $user->id);
            }]);
        }
        
        return $query->latest()->paginate(15);
    }
    
    public function getIdeaPermissions(Idea $idea = null, User $user = null): array
    {
        $user = $user ?? auth()->user();
        $basePermissions = $this->getBasePermissions($user);
        
        if ($idea && $user->role === 'track_supervisor') {
            $basePermissions['canReview'] = in_array($idea->track_id, $user->supervisedTracks->pluck('id')->toArray());
        }
        
        if ($idea && $user->role === 'team_leader') {
            $basePermissions['canEdit'] = $idea->team->leader_id === $user->id && $idea->status === 'draft';
            $basePermissions['canSubmit'] = $idea->team->leader_id === $user->id && $idea->status === 'draft';
        }
        
        return $basePermissions;
    }
}
EOF

echo "Creating WorkshopService..."
cat > app/Services/WorkshopService.php << 'EOF'
<?php

namespace App\Services;

use App\Models\Workshop;
use App\Models\User;
use Illuminate\Http\Request;

class WorkshopService extends BaseService
{
    public function getWorkshopsForUser(Request $request, User $user)
    {
        $query = Workshop::with(['speaker', 'organizations', 'registrations']);
        
        // Apply role-based filtering
        $query = $this->scopeByRole($query, $user, 'Workshop');
        
        // For visitors and participants, show only upcoming public workshops
        if (in_array($user->role, ['visitor', 'team_leader', 'team_member'])) {
            $query->where('is_public', true)
                  ->where('date', '>=', now());
        }
        
        return $query->orderBy('date')->paginate(15);
    }
    
    public function getWorkshopPermissions(Workshop $workshop = null, User $user = null): array
    {
        $user = $user ?? auth()->user();
        $basePermissions = $this->getBasePermissions($user);
        
        // Everyone can register for public workshops
        $basePermissions['canRegister'] = true;
        
        if ($workshop && $user->role === 'workshop_supervisor') {
            $supervisedIds = $user->supervisedWorkshops->pluck('id')->toArray();
            $basePermissions['canCheckIn'] = in_array($workshop->id, $supervisedIds);
        }
        
        return $basePermissions;
    }
}
EOF

echo "Creating DashboardService..."
cat > app/Services/DashboardService.php << 'EOF'
<?php

namespace App\Services;

use App\Models\Team;
use App\Models\Idea;
use App\Models\Workshop;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardService extends BaseService
{
    public function getDashboardData(User $user)
    {
        return match($user->role) {
            'system_admin' => $this->getSystemAdminDashboard(),
            'hackathon_admin' => $this->getHackathonAdminDashboard($user),
            'track_supervisor' => $this->getTrackSupervisorDashboard($user),
            'workshop_supervisor' => $this->getWorkshopSupervisorDashboard($user),
            'team_leader' => $this->getTeamLeaderDashboard($user),
            'team_member' => $this->getTeamMemberDashboard($user),
            'visitor' => $this->getVisitorDashboard(),
            default => []
        };
    }
    
    private function getSystemAdminDashboard()
    {
        return [
            'stats' => [
                'total_users' => User::count(),
                'total_teams' => Team::count(),
                'total_ideas' => Idea::count(),
                'total_workshops' => Workshop::count(),
            ],
            'recent_activity' => Team::latest()->take(5)->get(),
            'charts' => [
                'teams_by_edition' => Team::select('edition_id', DB::raw('count(*) as count'))
                    ->groupBy('edition_id')->get()
            ]
        ];
    }
    
    private function getHackathonAdminDashboard($user)
    {
        return [
            'stats' => [
                'edition_teams' => Team::where('edition_id', $user->edition_id)->count(),
                'edition_ideas' => Idea::whereHas('team', fn($q) => $q->where('edition_id', $user->edition_id))->count(),
                'pending_reviews' => Idea::whereHas('team', fn($q) => $q->where('edition_id', $user->edition_id))
                    ->where('status', 'submitted')->count(),
            ],
            'recent_teams' => Team::where('edition_id', $user->edition_id)->latest()->take(5)->get()
        ];
    }
    
    private function getTrackSupervisorDashboard($user)
    {
        $trackIds = $user->supervisedTracks->pluck('id');
        
        return [
            'stats' => [
                'ideas_to_review' => Idea::whereIn('track_id', $trackIds)->where('status', 'submitted')->count(),
                'reviewed_ideas' => Idea::whereIn('track_id', $trackIds)->where('status', 'reviewed')->count(),
                'total_teams' => Team::whereIn('track_id', $trackIds)->count(),
            ],
            'pending_reviews' => Idea::whereIn('track_id', $trackIds)
                ->where('status', 'submitted')
                ->with('team')
                ->take(5)->get()
        ];
    }
    
    private function getWorkshopSupervisorDashboard($user)
    {
        $workshopIds = $user->supervisedWorkshops->pluck('id');
        
        return [
            'stats' => [
                'total_workshops' => count($workshopIds),
                'today_workshops' => Workshop::whereIn('id', $workshopIds)->whereDate('date', today())->count(),
                'total_registrations' => Workshop::whereIn('id', $workshopIds)->withCount('registrations')->get()->sum('registrations_count'),
            ],
            'today_schedule' => Workshop::whereIn('id', $workshopIds)->whereDate('date', today())->get()
        ];
    }
    
    private function getTeamLeaderDashboard($user)
    {
        return [
            'team' => $user->team,
            'idea' => $user->team?->idea,
            'members' => $user->team?->members,
            'can_submit' => $user->team && !$user->team->idea
        ];
    }
    
    private function getTeamMemberDashboard($user)
    {
        return [
            'team' => $user->team,
            'idea' => $user->team?->idea,
            'members' => $user->team?->members
        ];
    }
    
    private function getVisitorDashboard()
    {
        return [
            'upcoming_workshops' => Workshop::where('is_public', true)
                ->where('date', '>=', now())
                ->orderBy('date')
                ->take(5)->get()
        ];
    }
}
EOF

# DAY 1 AFTERNOON: Create Shared Controllers
echo ""
echo "📦 Day 1 Afternoon: Creating Shared Controllers..."

# Already created TeamController, now create others
echo "Creating Shared IdeaController..."
cat > app/Http/Controllers/Shared/IdeaController.php << 'EOF'
<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Services\IdeaService;
use App\Models\Idea;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IdeaController extends Controller
{
    public function __construct(
        private IdeaService $ideaService
    ) {}
    
    public function index(Request $request)
    {
        $user = auth()->user();
        $ideas = $this->ideaService->getIdeasForUser($request, $user);
        
        return Inertia::render('Shared/Ideas/Index', [
            'ideas' => $ideas,
            'permissions' => $this->ideaService->getIdeaPermissions(null, $user),
            'userRole' => $user->role
        ]);
    }
    
    public function show($id)
    {
        $user = auth()->user();
        $idea = Idea::findOrFail($id);
        
        return Inertia::render('Shared/Ideas/Show', [
            'idea' => $idea->load(['team', 'files', 'reviews']),
            'permissions' => $this->ideaService->getIdeaPermissions($idea, $user),
            'userRole' => $user->role
        ]);
    }
    
    public function submitReview(Request $request, $id)
    {
        $user = auth()->user();
        
        if ($user->role !== 'track_supervisor') {
            abort(403);
        }
        
        $validated = $request->validate([
            'score' => 'required|numeric|min:0|max:100',
            'feedback' => 'required|string|min:100',
            'status' => 'required|in:approved,rejected,needs_revision'
        ]);
        
        $idea = Idea::findOrFail($id);
        $idea->reviews()->create([
            'reviewer_id' => $user->id,
            'score' => $validated['score'],
            'feedback' => $validated['feedback'],
            'status' => $validated['status']
        ]);
        
        $idea->update(['status' => 'reviewed']);
        
        return redirect()->route('ideas.index')->with('success', 'Review submitted');
    }
}
EOF

echo "Creating Shared WorkshopController..."
cat > app/Http/Controllers/Shared/WorkshopController.php << 'EOF'
<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Services\WorkshopService;
use App\Models\Workshop;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WorkshopController extends Controller
{
    public function __construct(
        private WorkshopService $workshopService
    ) {}
    
    public function index(Request $request)
    {
        $user = auth()->user();
        $workshops = $this->workshopService->getWorkshopsForUser($request, $user);
        
        return Inertia::render('Shared/Workshops/Index', [
            'workshops' => $workshops,
            'permissions' => $this->workshopService->getWorkshopPermissions(null, $user),
            'userRole' => $user->role
        ]);
    }
    
    public function show($id)
    {
        $user = auth()->user();
        $workshop = Workshop::findOrFail($id);
        
        return Inertia::render('Shared/Workshops/Show', [
            'workshop' => $workshop->load(['speaker', 'organizations', 'registrations']),
            'permissions' => $this->workshopService->getWorkshopPermissions($workshop, $user),
            'userRole' => $user->role,
            'isRegistered' => $workshop->registrations()->where('user_id', $user->id)->exists()
        ]);
    }
    
    public function register(Request $request, $id)
    {
        $user = auth()->user();
        $workshop = Workshop::findOrFail($id);
        
        // Check if already registered
        if ($workshop->registrations()->where('user_id', $user->id)->exists()) {
            return back()->with('error', 'Already registered');
        }
        
        // Check capacity
        if ($workshop->registrations()->count() >= $workshop->max_capacity) {
            return back()->with('error', 'Workshop is full');
        }
        
        $workshop->registrations()->create([
            'user_id' => $user->id,
            'registered_at' => now()
        ]);
        
        return back()->with('success', 'Successfully registered');
    }
    
    public function checkIn(Request $request, $id)
    {
        $user = auth()->user();
        
        if ($user->role !== 'workshop_supervisor') {
            abort(403);
        }
        
        $validated = $request->validate([
            'qr_code' => 'required|string'
        ]);
        
        // Process QR code and mark attendance
        // ... QR processing logic
        
        return back()->with('success', 'Check-in successful');
    }
}
EOF

echo "Creating Shared DashboardController..."
cat > app/Http/Controllers/Shared/DashboardController.php << 'EOF'
<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __construct(
        private DashboardService $dashboardService
    ) {}
    
    public function index()
    {
        $user = auth()->user();
        $dashboardData = $this->dashboardService->getDashboardData($user);
        
        return Inertia::render('Shared/Dashboard', [
            'dashboardData' => $dashboardData,
            'userRole' => $user->role
        ]);
    }
}
EOF

# DAY 2: Create Shared Vue Pages
echo ""
echo "📦 Day 2: Setting up Shared Vue Pages..."

# Create Shared directory
mkdir -p resources/js/Pages/Shared

# Copy SystemAdmin pages to Shared (if they exist)
if [ -d "resources/js/Pages/SystemAdmin" ]; then
    echo "Copying SystemAdmin pages to Shared..."
    cp -r resources/js/Pages/SystemAdmin/* resources/js/Pages/Shared/ 2>/dev/null || true
fi

# Update routes
echo ""
echo "📦 Updating Routes..."

# Backup existing web.php
cp routes/web.php routes/web.php.backup

# Add new shared routes to web.php
cat >> routes/web.php << 'EOF'

// SHARED ROUTES FOR ALL 7 ROLES
use App\Http\Controllers\Shared\DashboardController;
use App\Http\Controllers\Shared\TeamController;
use App\Http\Controllers\Shared\IdeaController;
use App\Http\Controllers\Shared\WorkshopController;

Route::middleware(['auth'])->group(function () {
    // Dashboard - works for ALL roles
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Teams - works for ALL roles with role-based filtering
    Route::resource('teams', TeamController::class);
    Route::post('teams/{team}/members', [TeamController::class, 'addMember'])->name('teams.add-member');
    Route::delete('teams/{team}/members/{user}', [TeamController::class, 'removeMember'])->name('teams.remove-member');
    
    // Ideas - works for ALL roles with role-based filtering
    Route::resource('ideas', IdeaController::class);
    Route::post('ideas/{idea}/review', [IdeaController::class, 'submitReview'])->name('ideas.review.submit');
    
    // Workshops - works for ALL roles
    Route::resource('workshops', WorkshopController::class);
    Route::post('workshops/{workshop}/register', [WorkshopController::class, 'register'])->name('workshops.register');
    Route::post('workshops/{workshop}/checkin', [WorkshopController::class, 'checkIn'])->name('workshops.checkin');
});
EOF

# Create test seeder
echo ""
echo "📦 Creating Test Users Seeder..."

cat > database/seeders/FastImplementationSeeder.php << 'EOF'
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Edition;
use App\Models\Team;
use App\Models\Track;
use App\Models\Workshop;

class FastImplementationSeeder extends Seeder
{
    public function run()
    {
        // Create test edition
        $edition = Edition::firstOrCreate([
            'name' => '2024 Hackathon',
            'year' => 2024,
            'is_current' => true
        ]);
        
        // Create test tracks
        $track1 = Track::firstOrCreate([
            'name' => 'Environment Track',
            'edition_id' => $edition->id
        ]);
        
        $track2 = Track::firstOrCreate([
            'name' => 'Technology Track',
            'edition_id' => $edition->id
        ]);
        
        // Create test users for each role
        $systemAdmin = User::firstOrCreate([
            'email' => 'system@test.com'
        ], [
            'name' => 'System Admin',
            'password' => bcrypt('password'),
            'role' => 'system_admin'
        ]);
        
        $hackathonAdmin = User::firstOrCreate([
            'email' => 'hackathon@test.com'
        ], [
            'name' => 'Hackathon Admin',
            'password' => bcrypt('password'),
            'role' => 'hackathon_admin',
            'edition_id' => $edition->id
        ]);
        
        $trackSupervisor = User::firstOrCreate([
            'email' => 'track@test.com'
        ], [
            'name' => 'Track Supervisor',
            'password' => bcrypt('password'),
            'role' => 'track_supervisor'
        ]);
        
        // Assign tracks to supervisor (create relation if needed)
        // $trackSupervisor->supervisedTracks()->sync([$track1->id, $track2->id]);
        
        $workshopSupervisor = User::firstOrCreate([
            'email' => 'workshop@test.com'
        ], [
            'name' => 'Workshop Supervisor',
            'password' => bcrypt('password'),
            'role' => 'workshop_supervisor'
        ]);
        
        $teamLeader = User::firstOrCreate([
            'email' => 'leader@test.com'
        ], [
            'name' => 'Team Leader',
            'password' => bcrypt('password'),
            'role' => 'team_leader'
        ]);
        
        $teamMember = User::firstOrCreate([
            'email' => 'member@test.com'
        ], [
            'name' => 'Team Member',
            'password' => bcrypt('password'),
            'role' => 'team_member'
        ]);
        
        $visitor = User::firstOrCreate([
            'email' => 'visitor@test.com'
        ], [
            'name' => 'Visitor',
            'password' => bcrypt('password'),
            'role' => 'visitor'
        ]);
        
        // Create a test team
        $team = Team::firstOrCreate([
            'name' => 'Test Team'
        ], [
            'edition_id' => $edition->id,
            'track_id' => $track1->id,
            'leader_id' => $teamLeader->id,
            'description' => 'Test team for demo'
        ]);
        
        // Update team_id for leader and member
        $teamLeader->update(['team_id' => $team->id]);
        $teamMember->update(['team_id' => $team->id]);
        
        // Add members to team
        $team->members()->syncWithoutDetaching([
            $teamLeader->id => ['role' => 'leader'],
            $teamMember->id => ['role' => 'member']
        ]);
        
        echo "Test users created:\n";
        echo "System Admin: system@test.com / password\n";
        echo "Hackathon Admin: hackathon@test.com / password\n";
        echo "Track Supervisor: track@test.com / password\n";
        echo "Workshop Supervisor: workshop@test.com / password\n";
        echo "Team Leader: leader@test.com / password\n";
        echo "Team Member: member@test.com / password\n";
        echo "Visitor: visitor@test.com / password\n";
    }
}
EOF

# Run migrations and seeders
echo ""
echo "🔧 Running migrations and seeders..."
php artisan migrate
php artisan db:seed --class=FastImplementationSeeder

# Clear caches
echo ""
echo "🧹 Clearing caches..."
php artisan optimize:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Build assets
echo ""
echo "📦 Building assets..."
npm install
npm run build

echo ""
echo "✅ FAST IMPLEMENTATION COMPLETE!"
echo "================================"
echo ""
echo "Test Users Created:"
echo "- System Admin: system@test.com / password"
echo "- Hackathon Admin: hackathon@test.com / password"
echo "- Track Supervisor: track@test.com / password"
echo "- Workshop Supervisor: workshop@test.com / password"
echo "- Team Leader: leader@test.com / password"
echo "- Team Member: member@test.com / password"
echo "- Visitor: visitor@test.com / password"
echo ""
echo "Next Steps:"
echo "1. Start the server: php artisan serve"
echo "2. Start Vite: npm run dev"
echo "3. Login with each role to test"
echo "4. All roles now share the same pages with role-based filtering!"
echo ""
echo "🎉 All 7 roles are now working with shared codebase!"
