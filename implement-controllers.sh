#!/bin/bash

echo "Implementing Controllers for all roles..."

# Team Lead Dashboard Controller
cat > app/Http/Controllers/TeamLead/DashboardController.php << 'EOF'
<?php

namespace App\Http\Controllers\TeamLead;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index()
    {
        $data = $this->dashboardService->getTeamLeadDashboard(auth()->id());
        
        return Inertia::render('TeamLead/Dashboard', $data);
    }
}
EOF

# Team Lead Team Controller
cat > app/Http/Controllers/TeamLead/TeamController.php << 'EOF'
<?php

namespace App\Http\Controllers\TeamLead;

use App\Http\Controllers\Controller;
use App\Services\TeamService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TeamController extends Controller
{
    protected $teamService;

    public function __construct(TeamService $teamService)
    {
        $this->teamService = $teamService;
    }

    public function index()
    {
        $team = $this->teamService->getTeamByLeader(auth()->id());
        
        return Inertia::render('TeamLead/Team/Index', [
            'team' => $team,
            'canCreateTeam' => !$team
        ]);
    }

    public function create()
    {
        if ($this->teamService->getTeamByLeader(auth()->id())) {
            return redirect()->route('team-lead.team.index')
                ->with('error', 'You already have a team');
        }

        $tracks = $this->teamService->getAvailableTracks();
        
        return Inertia::render('TeamLead/Team/Create', [
            'tracks' => $tracks
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:teams',
            'track_id' => 'required|exists:tracks,id',
            'description' => 'nullable|string'
        ]);

        $team = $this->teamService->createTeam(auth()->id(), $validated);

        return redirect()->route('team-lead.team.index')
            ->with('success', 'Team created successfully');
    }

    public function addMember(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
            'role' => 'nullable|string|in:developer,designer,marketer,other'
        ]);

        try {
            $member = $this->teamService->addTeamMember(
                auth()->id(),
                $validated['email'],
                $validated['role'] ?? 'member'
            );

            return back()->with('success', 'Team member added successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function removeMember($memberId)
    {
        try {
            $this->teamService->removeTeamMember(auth()->id(), $memberId);
            return back()->with('success', 'Team member removed successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
EOF

# Team Lead Idea Controller
cat > app/Http/Controllers/TeamLead/IdeaController.php << 'EOF'
<?php

namespace App\Http\Controllers\TeamLead;

use App\Http\Controllers\Controller;
use App\Services\IdeaService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IdeaController extends Controller
{
    protected $ideaService;

    public function __construct(IdeaService $ideaService)
    {
        $this->ideaService = $ideaService;
    }

    public function index()
    {
        $idea = $this->ideaService->getTeamIdea(auth()->id());
        
        return Inertia::render('TeamLead/Idea/Index', [
            'idea' => $idea,
            'canSubmit' => !$idea
        ]);
    }

    public function create()
    {
        $idea = $this->ideaService->getTeamIdea(auth()->id());
        if ($idea) {
            return redirect()->route('team-lead.idea.index');
        }

        return Inertia::render('TeamLead/Idea/Submit');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'problem_statement' => 'required|string',
            'solution' => 'required|string',
            'target_audience' => 'required|string',
            'unique_value' => 'required|string',
            'technical_feasibility' => 'required|string',
            'business_model' => 'nullable|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240'
        ]);

        try {
            $idea = $this->ideaService->submitIdea(auth()->id(), $validated);
            
            return redirect()->route('team-lead.idea.index')
                ->with('success', 'Idea submitted successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $idea = $this->ideaService->getTeamIdea(auth()->id());
        
        if (!$idea || $idea->id != $id) {
            return redirect()->route('team-lead.idea.index');
        }

        return Inertia::render('TeamLead/Idea/Edit', [
            'idea' => $idea
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'problem_statement' => 'required|string',
            'solution' => 'required|string',
            'target_audience' => 'required|string',
            'unique_value' => 'required|string',
            'technical_feasibility' => 'required|string',
            'business_model' => 'nullable|string'
        ]);

        try {
            $idea = $this->ideaService->updateIdea($id, auth()->id(), $validated);
            
            return redirect()->route('team-lead.idea.index')
                ->with('success', 'Idea updated successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function addComment(Request $request, $id)
    {
        $validated = $request->validate([
            'comment' => 'required|string'
        ]);

        try {
            $this->ideaService->addComment($id, auth()->id(), $validated['comment']);
            return back()->with('success', 'Comment added successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
EOF

# Team Lead Track Controller
cat > app/Http/Controllers/TeamLead/TrackController.php << 'EOF'
<?php

namespace App\Http\Controllers\TeamLead;

use App\Http\Controllers\Controller;
use App\Services\TrackService;
use Inertia\Inertia;

class TrackController extends Controller
{
    protected $trackService;

    public function __construct(TrackService $trackService)
    {
        $this->trackService = $trackService;
    }

    public function index()
    {
        $tracks = $this->trackService->getActiveTracksWithTeams();
        
        return Inertia::render('TeamLead/Tracks/Index', [
            'tracks' => $tracks
        ]);
    }

    public function show($id)
    {
        $track = $this->trackService->getTrackDetails($id);
        
        return Inertia::render('TeamLead/Tracks/Show', [
            'track' => $track
        ]);
    }
}
EOF

# Team Lead Workshop Controller
cat > app/Http/Controllers/TeamLead/WorkshopController.php << 'EOF'
<?php

namespace App\Http\Controllers\TeamLead;

use App\Http\Controllers\Controller;
use App\Services\WorkshopService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WorkshopController extends Controller
{
    protected $workshopService;

    public function __construct(WorkshopService $workshopService)
    {
        $this->workshopService = $workshopService;
    }

    public function index()
    {
        $workshops = $this->workshopService->getUpcomingWorkshops();
        $myWorkshops = $this->workshopService->getUserWorkshops(auth()->id());
        
        return Inertia::render('TeamLead/Workshops/Index', [
            'workshops' => $workshops,
            'myWorkshops' => $myWorkshops
        ]);
    }

    public function register($id)
    {
        try {
            $this->workshopService->registerForWorkshop(auth()->id(), $id);
            return back()->with('success', 'Successfully registered for workshop');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function unregister($id)
    {
        try {
            $this->workshopService->unregisterFromWorkshop(auth()->id(), $id);
            return back()->with('success', 'Successfully unregistered from workshop');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
EOF

# Team Lead Profile Controller
cat > app/Http/Controllers/TeamLead/ProfileController.php << 'EOF'
<?php

namespace App\Http\Controllers\TeamLead;

use App\Http\Controllers\Controller;
use App\Services\ProfileService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProfileController extends Controller
{
    protected $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    public function index()
    {
        $profile = $this->profileService->getUserProfile(auth()->id());
        
        return Inertia::render('Shared/Profile/Index', [
            'profile' => $profile,
            'role' => 'team-lead'
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:500',
            'skills' => 'nullable|array',
            'avatar' => 'nullable|image|max:2048'
        ]);

        $profile = $this->profileService->updateProfile(auth()->id(), $validated);

        return back()->with('success', 'Profile updated successfully');
    }
}
EOF

# Team Member Dashboard Controller
cat > app/Http/Controllers/TeamMember/DashboardController.php << 'EOF'
<?php

namespace App\Http\Controllers\TeamMember;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Inertia\Inertia;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index()
    {
        $data = $this->dashboardService->getTeamMemberDashboard(auth()->id());
        
        return Inertia::render('TeamMember/Dashboard', $data);
    }
}
EOF

# Team Member Team Controller
cat > app/Http/Controllers/TeamMember/TeamController.php << 'EOF'
<?php

namespace App\Http\Controllers\TeamMember;

use App\Http\Controllers\Controller;
use App\Services\TeamService;
use Inertia\Inertia;

class TeamController extends Controller
{
    protected $teamService;

    public function __construct(TeamService $teamService)
    {
        $this->teamService = $teamService;
    }

    public function index()
    {
        $team = $this->teamService->getMemberTeam(auth()->id());
        
        return Inertia::render('TeamMember/Team/Index', [
            'team' => $team
        ]);
    }
}
EOF

# Team Member Idea Controller
cat > app/Http/Controllers/TeamMember/IdeaController.php << 'EOF'
<?php

namespace App\Http\Controllers\TeamMember;

use App\Http\Controllers\Controller;
use App\Services\IdeaService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IdeaController extends Controller
{
    protected $ideaService;

    public function __construct(IdeaService $ideaService)
    {
        $this->ideaService = $ideaService;
    }

    public function index()
    {
        $idea = $this->ideaService->getTeamIdea(auth()->id());
        
        return Inertia::render('TeamMember/Idea/Index', [
            'idea' => $idea
        ]);
    }

    public function addComment(Request $request, $id)
    {
        $validated = $request->validate([
            'comment' => 'required|string'
        ]);

        try {
            $this->ideaService->addComment($id, auth()->id(), $validated['comment']);
            return back()->with('success', 'Comment added successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
EOF

# Team Member Track Controller
cat > app/Http/Controllers/TeamMember/TrackController.php << 'EOF'
<?php

namespace App\Http\Controllers\TeamMember;

use App\Http\Controllers\Controller;
use App\Services\TrackService;
use Inertia\Inertia;

class TrackController extends Controller
{
    protected $trackService;

    public function __construct(TrackService $trackService)
    {
        $this->trackService = $trackService;
    }

    public function index()
    {
        $tracks = $this->trackService->getActiveTracksWithTeams();
        
        return Inertia::render('TeamMember/Tracks/Index', [
            'tracks' => $tracks
        ]);
    }

    public function show($id)
    {
        $track = $this->trackService->getTrackDetails($id);
        
        return Inertia::render('TeamMember/Tracks/Show', [
            'track' => $track
        ]);
    }
}
EOF

# Team Member Workshop Controller
cat > app/Http/Controllers/TeamMember/WorkshopController.php << 'EOF'
<?php

namespace App\Http\Controllers\TeamMember;

use App\Http\Controllers\Controller;
use App\Services\WorkshopService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WorkshopController extends Controller
{
    protected $workshopService;

    public function __construct(WorkshopService $workshopService)
    {
        $this->workshopService = $workshopService;
    }

    public function index()
    {
        $workshops = $this->workshopService->getUpcomingWorkshops();
        $myWorkshops = $this->workshopService->getUserWorkshops(auth()->id());
        
        return Inertia::render('TeamMember/Workshops/Index', [
            'workshops' => $workshops,
            'myWorkshops' => $myWorkshops
        ]);
    }

    public function register($id)
    {
        try {
            $this->workshopService->registerForWorkshop(auth()->id(), $id);
            return back()->with('success', 'Successfully registered for workshop');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function unregister($id)
    {
        try {
            $this->workshopService->unregisterFromWorkshop(auth()->id(), $id);
            return back()->with('success', 'Successfully unregistered from workshop');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
EOF

# Team Member Profile Controller
cat > app/Http/Controllers/TeamMember/ProfileController.php << 'EOF'
<?php

namespace App\Http\Controllers\TeamMember;

use App\Http\Controllers\Controller;
use App\Services\ProfileService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProfileController extends Controller
{
    protected $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    public function index()
    {
        $profile = $this->profileService->getUserProfile(auth()->id());
        
        return Inertia::render('Shared/Profile/Index', [
            'profile' => $profile,
            'role' => 'team-member'
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:500',
            'skills' => 'nullable|array',
            'avatar' => 'nullable|image|max:2048'
        ]);

        $profile = $this->profileService->updateProfile(auth()->id(), $validated);

        return back()->with('success', 'Profile updated successfully');
    }
}
EOF

# Visitor Workshop Controller
cat > app/Http/Controllers/Visitor/WorkshopController.php << 'EOF'
<?php

namespace App\Http\Controllers\Visitor;

use App\Http\Controllers\Controller;
use App\Services\WorkshopService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WorkshopController extends Controller
{
    protected $workshopService;

    public function __construct(WorkshopService $workshopService)
    {
        $this->workshopService = $workshopService;
    }

    public function index()
    {
        $workshops = $this->workshopService->getAllWorkshops();
        
        return Inertia::render('Visitor/Workshops/All', [
            'workshops' => $workshops
        ]);
    }

    public function myWorkshops()
    {
        $workshops = $this->workshopService->getUserWorkshops(auth()->id());
        
        return Inertia::render('Visitor/Workshops/My', [
            'workshops' => $workshops
        ]);
    }

    public function register($id)
    {
        try {
            $this->workshopService->registerForWorkshop(auth()->id(), $id);
            return back()->with('success', 'Successfully registered for workshop');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function unregister($id)
    {
        try {
            $this->workshopService->unregisterFromWorkshop(auth()->id(), $id);
            return back()->with('success', 'Successfully unregistered from workshop');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
EOF

# Visitor Profile Controller
cat > app/Http/Controllers/Visitor/ProfileController.php << 'EOF'
<?php

namespace App\Http\Controllers\Visitor;

use App\Http\Controllers\Controller;
use App\Services\ProfileService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProfileController extends Controller
{
    protected $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    public function index()
    {
        $profile = $this->profileService->getUserProfile(auth()->id());
        
        return Inertia::render('Shared/Profile/Index', [
            'profile' => $profile,
            'role' => 'visitor'
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|max:2048'
        ]);

        $profile = $this->profileService->updateProfile(auth()->id(), $validated);

        return back()->with('success', 'Profile updated successfully');
    }
}
EOF

# Create ProfileService
cat > app/Services/ProfileService.php << 'EOF'
<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Storage;

class ProfileService extends BaseService
{
    public function getUserProfile($userId)
    {
        return User::with(['roles', 'teams'])->findOrFail($userId);
    }

    public function updateProfile($userId, array $data)
    {
        $user = User::findOrFail($userId);

        if (isset($data['avatar'])) {
            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::delete($user->avatar);
            }
            
            // Store new avatar
            $data['avatar'] = $data['avatar']->store('avatars', 'public');
        }

        $user->update($data);

        return $user;
    }
}
EOF

echo "Controllers implementation completed!"