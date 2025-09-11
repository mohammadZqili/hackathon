#!/bin/bash

echo "Creating Role-Based Pages Implementation..."

# Create Controllers for each role
echo "Creating Team Lead Controllers..."
php artisan make:controller TeamLead/DashboardController
php artisan make:controller TeamLead/TeamController
php artisan make:controller TeamLead/IdeaController
php artisan make:controller TeamLead/TrackController
php artisan make:controller TeamLead/WorkshopController
php artisan make:controller TeamLead/ProfileController

echo "Creating Team Member Controllers..."
php artisan make:controller TeamMember/DashboardController
php artisan make:controller TeamMember/TeamController
php artisan make:controller TeamMember/IdeaController
php artisan make:controller TeamMember/TrackController
php artisan make:controller TeamMember/WorkshopController
php artisan make:controller TeamMember/ProfileController

echo "Creating Visitor Controllers..."
php artisan make:controller Visitor/WorkshopController
php artisan make:controller Visitor/ProfileController

# Create Services (outside role namespaces)
echo "Creating Services..."
cat > app/Services/DashboardService.php << 'EOF'
<?php

namespace App\Services;

use App\Repositories\TeamRepository;
use App\Repositories\IdeaRepository;
use App\Repositories\WorkshopRepository;
use App\Repositories\TrackRepository;
use Illuminate\Support\Facades\Auth;

class DashboardService extends BaseService
{
    protected $teamRepo;
    protected $ideaRepo;
    protected $workshopRepo;
    protected $trackRepo;

    public function __construct(
        TeamRepository $teamRepo,
        IdeaRepository $ideaRepo,
        WorkshopRepository $workshopRepo,
        TrackRepository $trackRepo
    ) {
        $this->teamRepo = $teamRepo;
        $this->ideaRepo = $ideaRepo;
        $this->workshopRepo = $workshopRepo;
        $this->trackRepo = $trackRepo;
    }

    public function getTeamLeadDashboard($userId)
    {
        $team = $this->teamRepo->findByLeaderId($userId);
        $idea = $team ? $this->ideaRepo->findByTeamId($team->id) : null;
        $workshops = $this->workshopRepo->getUpcoming();
        $tracks = $this->trackRepo->getActive();

        return [
            'team' => $team,
            'idea' => $idea,
            'workshops' => $workshops,
            'tracks' => $tracks,
            'stats' => [
                'team_members' => $team ? $team->members()->count() : 0,
                'idea_status' => $idea ? $idea->status : 'pending',
                'workshops_registered' => $this->workshopRepo->countUserWorkshops($userId),
                'track' => $team && $team->track ? $team->track->name : null
            ]
        ];
    }

    public function getTeamMemberDashboard($userId)
    {
        $member = $this->teamRepo->findMemberTeam($userId);
        $team = $member ? $member->team : null;
        $idea = $team ? $this->ideaRepo->findByTeamId($team->id) : null;
        $workshops = $this->workshopRepo->getUpcoming();

        return [
            'team' => $team,
            'idea' => $idea,
            'workshops' => $workshops,
            'stats' => [
                'team_name' => $team ? $team->name : null,
                'idea_status' => $idea ? $idea->status : 'pending',
                'workshops_registered' => $this->workshopRepo->countUserWorkshops($userId),
                'role' => $member ? $member->role : null
            ]
        ];
    }

    public function getVisitorDashboard($userId)
    {
        $workshops = $this->workshopRepo->getUpcoming();
        $myWorkshops = $this->workshopRepo->getUserWorkshops($userId);

        return [
            'workshops' => $workshops,
            'myWorkshops' => $myWorkshops,
            'stats' => [
                'total_workshops' => $workshops->count(),
                'registered_workshops' => $myWorkshops->count()
            ]
        ];
    }
}
EOF

cat > app/Services/IdeaService.php << 'EOF'
<?php

namespace App\Services;

use App\Repositories\IdeaRepository;
use App\Repositories\TeamRepository;
use Illuminate\Support\Facades\DB;

class IdeaService extends BaseService
{
    protected $ideaRepo;
    protected $teamRepo;

    public function __construct(IdeaRepository $ideaRepo, TeamRepository $teamRepo)
    {
        $this->ideaRepo = $ideaRepo;
        $this->teamRepo = $teamRepo;
    }

    public function getTeamIdea($userId)
    {
        $team = $this->teamRepo->findByLeaderId($userId);
        if (!$team) {
            $member = $this->teamRepo->findMemberTeam($userId);
            $team = $member ? $member->team : null;
        }
        
        return $team ? $this->ideaRepo->findByTeamId($team->id) : null;
    }

    public function submitIdea($userId, array $data)
    {
        return DB::transaction(function () use ($userId, $data) {
            $team = $this->teamRepo->findByLeaderId($userId);
            if (!$team) {
                throw new \Exception('Only team leaders can submit ideas');
            }

            $data['team_id'] = $team->id;
            $data['submitted_by'] = $userId;
            $data['status'] = 'submitted';
            
            return $this->ideaRepo->create($data);
        });
    }

    public function updateIdea($ideaId, $userId, array $data)
    {
        return DB::transaction(function () use ($ideaId, $userId, $data) {
            $idea = $this->ideaRepo->find($ideaId);
            $team = $this->teamRepo->findByLeaderId($userId);
            
            if (!$team || $idea->team_id !== $team->id) {
                throw new \Exception('Unauthorized to update this idea');
            }

            return $this->ideaRepo->update($ideaId, $data);
        });
    }

    public function addComment($ideaId, $userId, $comment)
    {
        $idea = $this->ideaRepo->find($ideaId);
        
        // Check if user is part of the team
        $team = $this->teamRepo->findByLeaderId($userId);
        if (!$team) {
            $member = $this->teamRepo->findMemberTeam($userId);
            $team = $member ? $member->team : null;
        }
        
        if (!$team || $idea->team_id !== $team->id) {
            throw new \Exception('Unauthorized to comment on this idea');
        }

        return $this->ideaRepo->addComment($ideaId, [
            'user_id' => $userId,
            'comment' => $comment,
            'created_at' => now()
        ]);
    }
}
EOF

cat > app/Services/WorkshopService.php << 'EOF'
<?php

namespace App\Services;

use App\Repositories\WorkshopRepository;
use Illuminate\Support\Facades\DB;

class WorkshopService extends BaseService
{
    protected $workshopRepo;

    public function __construct(WorkshopRepository $workshopRepo)
    {
        $this->workshopRepo = $workshopRepo;
    }

    public function getAllWorkshops()
    {
        return $this->workshopRepo->getAll();
    }

    public function getUpcomingWorkshops()
    {
        return $this->workshopRepo->getUpcoming();
    }

    public function getUserWorkshops($userId)
    {
        return $this->workshopRepo->getUserWorkshops($userId);
    }

    public function registerForWorkshop($userId, $workshopId)
    {
        return DB::transaction(function () use ($userId, $workshopId) {
            // Check if already registered
            if ($this->workshopRepo->isUserRegistered($userId, $workshopId)) {
                throw new \Exception('Already registered for this workshop');
            }

            // Check capacity
            $workshop = $this->workshopRepo->find($workshopId);
            if ($workshop->current_participants >= $workshop->max_participants) {
                throw new \Exception('Workshop is full');
            }

            return $this->workshopRepo->registerUser($userId, $workshopId);
        });
    }

    public function unregisterFromWorkshop($userId, $workshopId)
    {
        return DB::transaction(function () use ($userId, $workshopId) {
            if (!$this->workshopRepo->isUserRegistered($userId, $workshopId)) {
                throw new \Exception('Not registered for this workshop');
            }

            return $this->workshopRepo->unregisterUser($userId, $workshopId);
        });
    }
}
EOF

cat > app/Services/TrackService.php << 'EOF'
<?php

namespace App\Services;

use App\Repositories\TrackRepository;

class TrackService extends BaseService
{
    protected $trackRepo;

    public function __construct(TrackRepository $trackRepo)
    {
        $this->trackRepo = $trackRepo;
    }

    public function getAllTracks()
    {
        return $this->trackRepo->getAll();
    }

    public function getActiveTracksWithTeams()
    {
        return $this->trackRepo->getActiveWithTeams();
    }

    public function getTrackDetails($trackId)
    {
        return $this->trackRepo->findWithDetails($trackId);
    }

    public function getTrackSupervisors($trackId)
    {
        return $this->trackRepo->getSupervisors($trackId);
    }
}
EOF

# Create Repositories
echo "Creating Repositories..."
cat > app/Repositories/TeamRepository.php << 'EOF'
<?php

namespace App\Repositories;

use App\Models\Team;
use App\Models\TeamMember;

class TeamRepository
{
    public function findByLeaderId($userId)
    {
        return Team::where('leader_id', $userId)->with(['members', 'track'])->first();
    }

    public function findMemberTeam($userId)
    {
        return TeamMember::where('user_id', $userId)->with('team.members')->first();
    }

    public function create(array $data)
    {
        return Team::create($data);
    }

    public function update($id, array $data)
    {
        $team = Team::findOrFail($id);
        $team->update($data);
        return $team;
    }

    public function addMember($teamId, array $memberData)
    {
        return TeamMember::create([
            'team_id' => $teamId,
            'user_id' => $memberData['user_id'],
            'role' => $memberData['role'] ?? 'member',
            'joined_at' => now()
        ]);
    }

    public function removeMember($teamId, $userId)
    {
        return TeamMember::where('team_id', $teamId)
            ->where('user_id', $userId)
            ->delete();
    }
}
EOF

cat > app/Repositories/IdeaRepository.php << 'EOF'
<?php

namespace App\Repositories;

use App\Models\Idea;
use App\Models\IdeaComment;

class IdeaRepository
{
    public function findByTeamId($teamId)
    {
        return Idea::where('team_id', $teamId)
            ->with(['comments.user', 'attachments'])
            ->first();
    }

    public function find($id)
    {
        return Idea::findOrFail($id);
    }

    public function create(array $data)
    {
        return Idea::create($data);
    }

    public function update($id, array $data)
    {
        $idea = Idea::findOrFail($id);
        $idea->update($data);
        return $idea;
    }

    public function addComment($ideaId, array $commentData)
    {
        return IdeaComment::create([
            'idea_id' => $ideaId,
            'user_id' => $commentData['user_id'],
            'comment' => $commentData['comment'],
            'created_at' => $commentData['created_at']
        ]);
    }

    public function getComments($ideaId)
    {
        return IdeaComment::where('idea_id', $ideaId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
EOF

cat > app/Repositories/WorkshopRepository.php << 'EOF'
<?php

namespace App\Repositories;

use App\Models\Workshop;
use App\Models\WorkshopRegistration;
use Illuminate\Support\Facades\DB;

class WorkshopRepository
{
    public function getAll()
    {
        return Workshop::with(['supervisor', 'registrations'])->get();
    }

    public function getUpcoming()
    {
        return Workshop::where('date', '>=', now())
            ->orderBy('date', 'asc')
            ->with('supervisor')
            ->get();
    }

    public function find($id)
    {
        return Workshop::findOrFail($id);
    }

    public function getUserWorkshops($userId)
    {
        return Workshop::whereHas('registrations', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->with('supervisor')->get();
    }

    public function isUserRegistered($userId, $workshopId)
    {
        return WorkshopRegistration::where('user_id', $userId)
            ->where('workshop_id', $workshopId)
            ->exists();
    }

    public function registerUser($userId, $workshopId)
    {
        DB::transaction(function () use ($userId, $workshopId) {
            WorkshopRegistration::create([
                'user_id' => $userId,
                'workshop_id' => $workshopId,
                'registered_at' => now()
            ]);

            Workshop::where('id', $workshopId)
                ->increment('current_participants');
        });

        return true;
    }

    public function unregisterUser($userId, $workshopId)
    {
        DB::transaction(function () use ($userId, $workshopId) {
            WorkshopRegistration::where('user_id', $userId)
                ->where('workshop_id', $workshopId)
                ->delete();

            Workshop::where('id', $workshopId)
                ->decrement('current_participants');
        });

        return true;
    }

    public function countUserWorkshops($userId)
    {
        return WorkshopRegistration::where('user_id', $userId)->count();
    }
}
EOF

cat > app/Repositories/TrackRepository.php << 'EOF'
<?php

namespace App\Repositories;

use App\Models\Track;

class TrackRepository
{
    public function getAll()
    {
        return Track::with(['teams', 'supervisors'])->get();
    }

    public function getActive()
    {
        return Track::where('is_active', true)
            ->with(['teams', 'supervisors'])
            ->get();
    }

    public function getActiveWithTeams()
    {
        return Track::where('is_active', true)
            ->withCount('teams')
            ->with('supervisors')
            ->get();
    }

    public function find($id)
    {
        return Track::findOrFail($id);
    }

    public function findWithDetails($id)
    {
        return Track::with(['teams.members', 'supervisors', 'ideas'])
            ->findOrFail($id);
    }

    public function getSupervisors($trackId)
    {
        $track = Track::findOrFail($trackId);
        return $track->supervisors;
    }
}
EOF

# Create middleware for role checking
echo "Creating Role Middleware..."
cat > app/Http/Middleware/CheckTeamLeadRole.php << 'EOF'
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckTeamLeadRole
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->hasRole('team_lead')) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
EOF

cat > app/Http/Middleware/CheckTeamMemberRole.php << 'EOF'
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckTeamMemberRole
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->hasRole('team_member')) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
EOF

cat > app/Http/Middleware/CheckVisitorRole.php << 'EOF'
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckVisitorRole
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->hasRole('visitor')) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
EOF

# Create route files
echo "Creating route files..."
cat > routes/team-lead.php << 'EOF'
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeamLead\DashboardController;
use App\Http\Controllers\TeamLead\TeamController;
use App\Http\Controllers\TeamLead\IdeaController;
use App\Http\Controllers\TeamLead\TrackController;
use App\Http\Controllers\TeamLead\WorkshopController;
use App\Http\Controllers\TeamLead\ProfileController;

Route::middleware(['auth', 'verified', 'team.lead'])->prefix('team-lead')->name('team-lead.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Team Management
    Route::prefix('team')->name('team.')->group(function () {
        Route::get('/', [TeamController::class, 'index'])->name('index');
        Route::get('/create', [TeamController::class, 'create'])->name('create');
        Route::post('/', [TeamController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [TeamController::class, 'edit'])->name('edit');
        Route::put('/{id}', [TeamController::class, 'update'])->name('update');
        Route::post('/add-member', [TeamController::class, 'addMember'])->name('add-member');
        Route::delete('/remove-member/{id}', [TeamController::class, 'removeMember'])->name('remove-member');
    });
    
    // Idea Management
    Route::prefix('idea')->name('idea.')->group(function () {
        Route::get('/', [IdeaController::class, 'index'])->name('index');
        Route::get('/submit', [IdeaController::class, 'create'])->name('create');
        Route::post('/', [IdeaController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [IdeaController::class, 'edit'])->name('edit');
        Route::put('/{id}', [IdeaController::class, 'update'])->name('update');
        Route::post('/{id}/comment', [IdeaController::class, 'addComment'])->name('comment');
    });
    
    // Tracks
    Route::prefix('tracks')->name('tracks.')->group(function () {
        Route::get('/', [TrackController::class, 'index'])->name('index');
        Route::get('/{id}', [TrackController::class, 'show'])->name('show');
    });
    
    // Workshops
    Route::prefix('workshops')->name('workshops.')->group(function () {
        Route::get('/', [WorkshopController::class, 'index'])->name('index');
        Route::post('/{id}/register', [WorkshopController::class, 'register'])->name('register');
        Route::delete('/{id}/unregister', [WorkshopController::class, 'unregister'])->name('unregister');
    });
    
    // Profile
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
    });
});
EOF

cat > routes/team-member.php << 'EOF'
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeamMember\DashboardController;
use App\Http\Controllers\TeamMember\TeamController;
use App\Http\Controllers\TeamMember\IdeaController;
use App\Http\Controllers\TeamMember\TrackController;
use App\Http\Controllers\TeamMember\WorkshopController;
use App\Http\Controllers\TeamMember\ProfileController;

Route::middleware(['auth', 'verified', 'team.member'])->prefix('team-member')->name('team-member.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Team View
    Route::prefix('team')->name('team.')->group(function () {
        Route::get('/', [TeamController::class, 'index'])->name('index');
    });
    
    // Idea View & Comments
    Route::prefix('idea')->name('idea.')->group(function () {
        Route::get('/', [IdeaController::class, 'index'])->name('index');
        Route::post('/{id}/comment', [IdeaController::class, 'addComment'])->name('comment');
    });
    
    // Tracks
    Route::prefix('tracks')->name('tracks.')->group(function () {
        Route::get('/', [TrackController::class, 'index'])->name('index');
        Route::get('/{id}', [TrackController::class, 'show'])->name('show');
    });
    
    // Workshops
    Route::prefix('workshops')->name('workshops.')->group(function () {
        Route::get('/', [WorkshopController::class, 'index'])->name('index');
        Route::post('/{id}/register', [WorkshopController::class, 'register'])->name('register');
        Route::delete('/{id}/unregister', [WorkshopController::class, 'unregister'])->name('unregister');
    });
    
    // Profile
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
    });
});
EOF

cat > routes/visitor.php << 'EOF'
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Visitor\WorkshopController;
use App\Http\Controllers\Visitor\ProfileController;

Route::middleware(['auth', 'verified', 'visitor'])->prefix('visitor')->name('visitor.')->group(function () {
    // All Workshops
    Route::prefix('workshops')->name('workshops.')->group(function () {
        Route::get('/', [WorkshopController::class, 'index'])->name('index');
        Route::get('/my-workshops', [WorkshopController::class, 'myWorkshops'])->name('my');
        Route::post('/{id}/register', [WorkshopController::class, 'register'])->name('register');
        Route::delete('/{id}/unregister', [WorkshopController::class, 'unregister'])->name('unregister');
    });
    
    // Profile
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
    });
});
EOF

echo "Script created successfully!"
echo "Now run: chmod +x implement-roles-pages.sh && ./implement-roles-pages.sh"