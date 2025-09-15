# 🔥 COMPLETE CONTROLLER & ROUTES IMPLEMENTATION CODE
## Ready-to-Copy Code for All Remaining Features

---

## 📂 PART 1: ROUTES IMPLEMENTATION

### Update `routes/web.php` with Complete Role-Based Routes:

```php
<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
// ... existing imports ...

// Team Leader Controllers
use App\Http\Controllers\TeamLeader\TeamController as TeamLeaderTeamController;
use App\Http\Controllers\TeamLeader\IdeaController as TeamLeaderIdeaController;
use App\Http\Controllers\TeamLeader\WorkshopController as TeamLeaderWorkshopController;
use App\Http\Controllers\TeamLeader\ProfileController as TeamLeaderProfileController;
use App\Http\Controllers\TeamLeader\DashboardController as TeamLeaderDashboardController;

// Team Member Controllers
use App\Http\Controllers\TeamMember\DashboardController as TeamMemberDashboardController;
use App\Http\Controllers\TeamMember\TeamController as TeamMemberTeamController;
use App\Http\Controllers\TeamMember\IdeaController as TeamMemberIdeaController;
use App\Http\Controllers\TeamMember\WorkshopController as TeamMemberWorkshopController;

// Track Supervisor Controllers
use App\Http\Controllers\TrackSupervisor1\DashboardController as TrackSupervisorDashboardController;
use App\Http\Controllers\TrackSupervisor1\IdeaReviewController;
use App\Http\Controllers\TrackSupervisor1\TeamController as TrackSupervisorTeamController;
use App\Http\Controllers\TrackSupervisor1\TrackSettingsController;

// Workshop Supervisor Controllers
use App\Http\Controllers\WorkshopSupervisor\DashboardController as WorkshopSupervisorDashboardController;
use App\Http\Controllers\WorkshopSupervisor\WorkshopController as WorkshopSupervisorWorkshopController;
use App\Http\Controllers\WorkshopSupervisor\CheckInController;
use App\Http\Controllers\WorkshopSupervisor\AttendanceController;

// Hackathon Admin Controllers
use App\Http\Controllers\HackathonAdmin1\DashboardController as HackathonAdminDashboardController;
use App\Http\Controllers\HackathonAdmin1\TeamManagementController;
use App\Http\Controllers\HackathonAdmin1\IdeaManagementController;
use App\Http\Controllers\HackathonAdmin1\TrackController;
use App\Http\Controllers\HackathonAdmin1\WorkshopManagementController;
use App\Http\Controllers\HackathonAdmin1\NewsController;

// System Admin Controllers
use App\Http\Controllers\SystemAdmin\DashboardController as SystemAdminDashboardController;
use App\Http\Controllers\SystemAdmin\EditionController;
use App\Http\Controllers\SystemAdmin\GlobalUserController;
use App\Http\Controllers\SystemAdmin\SystemSettingsController;

// Public/Visitor Controllers
use App\Http\Controllers\Public\WorkshopRegistrationController;
use App\Http\Controllers\Public\PublicWorkshopController;

// ... existing routes ...

Route::middleware(['web', 'auth', 'auth.session'])->group(function () {
    
    // ===========================================
    // TEAM LEADER ROUTES
    // ===========================================
    Route::middleware(['role:team_leader'])->prefix('team-leader')->name('team-leader.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [TeamLeaderDashboardController::class, 'index'])->name('dashboard');
        
        // Team Management
        Route::prefix('team')->name('team.')->group(function () {
            Route::get('/create', [TeamLeaderTeamController::class, 'create'])->name('create');
            Route::post('/store', [TeamLeaderTeamController::class, 'store'])->name('store');
            Route::get('/show', [TeamLeaderTeamController::class, 'show'])->name('show');
            Route::put('/update', [TeamLeaderTeamController::class, 'update'])->name('update');
            
            // Member Management
            Route::post('/invite-member', [TeamLeaderTeamController::class, 'inviteMember'])->name('invite-member');
            Route::delete('/remove-member/{member}', [TeamLeaderTeamController::class, 'removeMember'])->name('remove-member');
            Route::post('/approve-member/{member}', [TeamLeaderTeamController::class, 'approveMember'])->name('approve-member');
        });
        
        // Idea Management
        Route::prefix('idea')->name('idea.')->group(function () {
            Route::get('/create', [TeamLeaderIdeaController::class, 'create'])->name('create');
            Route::post('/store', [TeamLeaderIdeaController::class, 'store'])->name('store');
            Route::get('/show', [TeamLeaderIdeaController::class, 'show'])->name('show');
            Route::get('/edit', [TeamLeaderIdeaController::class, 'edit'])->name('edit');
            Route::put('/update', [TeamLeaderIdeaController::class, 'update'])->name('update');
            Route::post('/submit', [TeamLeaderIdeaController::class, 'submit'])->name('submit');
            Route::post('/upload-attachment', [TeamLeaderIdeaController::class, 'uploadAttachment'])->name('upload-attachment');
            Route::delete('/remove-attachment/{attachment}', [TeamLeaderIdeaController::class, 'removeAttachment'])->name('remove-attachment');
        });
        
        // Tracks
        Route::get('/tracks', [TeamLeaderTeamController::class, 'tracks'])->name('tracks.index');
        Route::post('/tracks/select/{track}', [TeamLeaderTeamController::class, 'selectTrack'])->name('tracks.select');
        
        // Workshops
        Route::prefix('workshops')->name('workshops.')->group(function () {
            Route::get('/', [TeamLeaderWorkshopController::class, 'index'])->name('index');
            Route::post('/register/{workshop}', [TeamLeaderWorkshopController::class, 'register'])->name('register');
            Route::delete('/cancel/{registration}', [TeamLeaderWorkshopController::class, 'cancel'])->name('cancel');
            Route::get('/my-workshops', [TeamLeaderWorkshopController::class, 'myWorkshops'])->name('my');
        });
        
        // Profile
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/edit', [TeamLeaderProfileController::class, 'edit'])->name('edit');
            Route::put('/update', [TeamLeaderProfileController::class, 'update'])->name('update');
            Route::put('/update-password', [TeamLeaderProfileController::class, 'updatePassword'])->name('update-password');
        });
    });
    
    // ===========================================
    // TEAM MEMBER ROUTES
    // ===========================================
    Route::middleware(['role:team_member'])->prefix('team-member')->name('team-member.')->group(function () {
        Route::get('/dashboard', [TeamMemberDashboardController::class, 'index'])->name('dashboard');
        
        // Team
        Route::prefix('team')->name('team.')->group(function () {
            Route::get('/join', [TeamMemberTeamController::class, 'joinForm'])->name('join');
            Route::post('/request-join/{team}', [TeamMemberTeamController::class, 'requestJoin'])->name('request-join');
            Route::post('/accept-invitation/{invitation}', [TeamMemberTeamController::class, 'acceptInvitation'])->name('accept-invitation');
            Route::get('/show', [TeamMemberTeamController::class, 'show'])->name('show');
        });
        
        // Idea (View Only with Limited Edit)
        Route::prefix('idea')->name('idea.')->group(function () {
            Route::get('/show', [TeamMemberIdeaController::class, 'show'])->name('show');
            Route::post('/contribute', [TeamMemberIdeaController::class, 'contribute'])->name('contribute');
        });
        
        // Workshops
        Route::prefix('workshops')->name('workshops.')->group(function () {
            Route::get('/', [TeamMemberWorkshopController::class, 'index'])->name('index');
            Route::post('/register/{workshop}', [TeamMemberWorkshopController::class, 'register'])->name('register');
            Route::get('/my-workshops', [TeamMemberWorkshopController::class, 'myWorkshops'])->name('my');
        });
        
        // Profile
        Route::get('/profile/edit', [TeamMemberProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile/update', [TeamMemberProfileController::class, 'update'])->name('profile.update');
    });
    
    // ===========================================
    // TRACK SUPERVISOR ROUTES
    // ===========================================
    Route::middleware(['role:track_supervisor'])->prefix('track-supervisor')->name('track-supervisor.')->group(function () {
        Route::get('/dashboard', [TrackSupervisorDashboardController::class, 'index'])->name('dashboard');
        
        // Ideas Review
        Route::prefix('ideas')->name('ideas.')->group(function () {
            Route::get('/', [IdeaReviewController::class, 'index'])->name('index');
            Route::get('/{idea}/review', [IdeaReviewController::class, 'review'])->name('review');
            Route::post('/{idea}/approve', [IdeaReviewController::class, 'approve'])->name('approve');
            Route::post('/{idea}/reject', [IdeaReviewController::class, 'reject'])->name('reject');
            Route::post('/{idea}/request-changes', [IdeaReviewController::class, 'requestChanges'])->name('request-changes');
            Route::post('/{idea}/add-comment', [IdeaReviewController::class, 'addComment'])->name('add-comment');
            Route::post('/{idea}/score', [IdeaReviewController::class, 'score'])->name('score');
        });
        
        // Teams in Track
        Route::get('/teams', [TrackSupervisorTeamController::class, 'index'])->name('teams.index');
        Route::get('/teams/{team}', [TrackSupervisorTeamController::class, 'show'])->name('teams.show');
        
        // Track Settings
        Route::get('/track/edit', [TrackSettingsController::class, 'edit'])->name('track.edit');
        Route::put('/track/update', [TrackSettingsController::class, 'update'])->name('track.update');
        
        // Statistics
        Route::get('/stats', [TrackSupervisorDashboardController::class, 'statistics'])->name('stats.index');
        
        // Workshops (view only)
        Route::get('/workshops', [TrackSupervisorDashboardController::class, 'workshops'])->name('workshops.index');
    });
    
    // ===========================================
    // WORKSHOP SUPERVISOR ROUTES
    // ===========================================
    Route::middleware(['role:workshop_supervisor'])->prefix('workshop-supervisor')->name('workshop-supervisor.')->group(function () {
        Route::get('/dashboard', [WorkshopSupervisorDashboardController::class, 'index'])->name('dashboard');
        
        // Workshops Management
        Route::prefix('workshops')->name('workshops.')->group(function () {
            Route::get('/', [WorkshopSupervisorWorkshopController::class, 'index'])->name('index');
            Route::get('/my-workshops', [WorkshopSupervisorWorkshopController::class, 'myWorkshops'])->name('my');
        });
        
        // Check-In System
        Route::prefix('checkins')->name('checkins.')->group(function () {
            Route::get('/', [CheckInController::class, 'index'])->name('index');
            Route::get('/workshop/{workshop}', [CheckInController::class, 'workshop'])->name('workshop');
            Route::post('/scan', [CheckInController::class, 'scan'])->name('scan');
            Route::post('/manual', [CheckInController::class, 'manual'])->name('manual');
            Route::get('/camera', [CheckInController::class, 'camera'])->name('camera');
        });
        
        // Attendance Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance');
            Route::get('/attendance/{workshop}', [AttendanceController::class, 'workshop'])->name('attendance.workshop');
            Route::get('/attendance/export/{workshop}', [AttendanceController::class, 'export'])->name('attendance.export');
        });
    });
    
    // ===========================================
    // HACKATHON ADMIN ROUTES
    // ===========================================
    Route::middleware(['role:hackathon_admin'])->prefix('hackathon-admin')->name('hackathon-admin.')->group(function () {
        Route::get('/dashboard', [HackathonAdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/edition/show', [HackathonAdminDashboardController::class, 'showEdition'])->name('edition.show');
        
        // Teams Management
        Route::prefix('teams')->name('teams.')->group(function () {
            Route::get('/', [TeamManagementController::class, 'index'])->name('index');
            Route::post('/bulk-approve', [TeamManagementController::class, 'bulkApprove'])->name('bulk-approve');
            Route::post('/bulk-reject', [TeamManagementController::class, 'bulkReject'])->name('bulk-reject');
            Route::put('/{team}/reassign-track', [TeamManagementController::class, 'reassignTrack'])->name('reassign-track');
        });
        
        // Ideas Management
        Route::prefix('ideas')->name('ideas.')->group(function () {
            Route::get('/', [IdeaManagementController::class, 'index'])->name('index');
            Route::post('/{idea}/override-decision', [IdeaManagementController::class, 'overrideDecision'])->name('override');
            Route::get('/export', [IdeaManagementController::class, 'export'])->name('export');
        });
        
        // Tracks Management
        Route::resource('tracks', TrackController::class);
        Route::post('/tracks/{track}/assign-supervisor', [TrackController::class, 'assignSupervisor'])->name('tracks.assign-supervisor');
        
        // Workshops Management
        Route::resource('workshops', WorkshopManagementController::class);
        Route::post('/workshops/{workshop}/assign-supervisor', [WorkshopManagementController::class, 'assignSupervisor'])->name('workshops.assign-supervisor');
        
        // News Management
        Route::resource('news', NewsController::class);
        Route::post('/news/{news}/publish', [NewsController::class, 'publish'])->name('news.publish');
        Route::post('/news/{news}/tweet', [NewsController::class, 'tweet'])->name('news.tweet');
        
        // Reports
        Route::get('/reports', [HackathonAdminDashboardController::class, 'reports'])->name('reports.index');
        Route::get('/export', [HackathonAdminDashboardController::class, 'exportData'])->name('export.index');
    });
    
    // ===========================================
    // SYSTEM ADMIN ROUTES (extends existing admin routes)
    // ===========================================
    Route::middleware(['role:system_admin'])->prefix('system-admin')->name('system-admin.')->group(function () {
        Route::get('/dashboard', [SystemAdminDashboardController::class, 'index'])->name('dashboard');
        
        // Hackathon Editions
        Route::resource('editions', EditionController::class);
        Route::post('/editions/{edition}/activate', [EditionController::class, 'activate'])->name('editions.activate');
        Route::post('/editions/{edition}/archive', [EditionController::class, 'archive'])->name('editions.archive');
        Route::post('/editions/{edition}/clone', [EditionController::class, 'clone'])->name('editions.clone');
        
        // Global User Management (extends existing AdminUserController)
        Route::get('/users', [GlobalUserController::class, 'index'])->name('users.index');
        Route::post('/users/bulk-assign-role', [GlobalUserController::class, 'bulkAssignRole'])->name('users.bulk-assign-role');
        
        // System-wide Teams & Ideas
        Route::get('/teams', [SystemAdminDashboardController::class, 'allTeams'])->name('teams.index');
        Route::get('/ideas', [SystemAdminDashboardController::class, 'allIdeas'])->name('ideas.index');
        Route::get('/workshops', [SystemAdminDashboardController::class, 'allWorkshops'])->name('workshops.index');
        
        // System Settings
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/smtp', [SystemSettingsController::class, 'smtp'])->name('smtp');
            Route::put('/smtp', [SystemSettingsController::class, 'updateSmtp'])->name('smtp.update');
            Route::get('/twitter', [SystemSettingsController::class, 'twitter'])->name('twitter');
            Route::put('/twitter', [SystemSettingsController::class, 'updateTwitter'])->name('twitter.update');
            Route::get('/branding', [SystemSettingsController::class, 'branding'])->name('branding');
            Route::put('/branding', [SystemSettingsController::class, 'updateBranding'])->name('branding.update');
        });
        
        // System Health (reuse existing)
        Route::get('/health', [AdminHealthStatusController::class, 'index'])->name('health.index');
        
        // Reports & Analytics
        Route::get('/reports', [SystemAdminDashboardController::class, 'reports'])->name('reports.index');
        
        // News Management (system-wide)
        Route::get('/news', [SystemAdminDashboardController::class, 'allNews'])->name('news.index');
    });
    
    // ===========================================
    // VISITOR/PUBLIC ROUTES
    // ===========================================
    Route::prefix('visitor')->name('visitor.')->group(function () {
        Route::get('/dashboard', function () {
            return Inertia::render('Visitor/Dashboard/Index');
        })->name('dashboard');
        
        Route::prefix('workshops')->name('workshops.')->group(function () {
            Route::get('/', [PublicWorkshopController::class, 'index'])->name('index');
            Route::get('/{workshop}', [PublicWorkshopController::class, 'show'])->name('show');
            Route::post('/{workshop}/register', [WorkshopRegistrationController::class, 'register'])
                ->name('register')
                ->middleware('throttle:5,1'); // Rate limit registrations
            Route::get('/my-workshops', [WorkshopRegistrationController::class, 'myWorkshops'])
                ->name('my')
                ->middleware('auth');
            Route::get('/qr/{registration}', [WorkshopRegistrationController::class, 'showQR'])
                ->name('qr')
                ->middleware('auth');
        });
        
        // Profile
        Route::middleware('auth')->group(function () {
            Route::get('/profile/edit', function () {
                return Inertia::render('Visitor/Profile/Edit');
            })->name('profile.edit');
        });
    });
});

// Public pages (no auth required)
Route::get('/workshops', [PublicWorkshopController::class, 'index'])->name('public.workshops');
Route::get('/about', [PageController::class, 'about'])->name('public.about');
Route::get('/tracks', [PageController::class, 'tracks'])->name('public.tracks');
Route::get('/prizes', [PageController::class, 'prizes'])->name('public.prizes');
Route::get('/news', [PageController::class, 'news'])->name('public.news');
```

---

## 📂 PART 2: TEAM LEADER CONTROLLERS

### Create `app/Http/Controllers/TeamLeader/DashboardController.php`:

```php
<?php

namespace App\Http\Controllers\TeamLeader;

use App\Http\Controllers\Controller;
use App\Models\Hackathon;
use App\Models\Team;
use App\Models\WorkshopRegistration;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $currentHackathon = Hackathon::current()->first();
        $team = $user->getCurrentTeam();
        
        // If no current hackathon, show closed message
        if (!$currentHackathon) {
            return Inertia::render('TeamLeader/Dashboard/Closed');
        }
        
        // If no team yet, redirect to create team
        if (!$team && $currentHackathon->isRegistrationOpen()) {
            return redirect()->route('team-leader.team.create');
        }
        
        // Gather dashboard statistics
        $stats = [
            'team' => [
                'exists' => (bool) $team,
                'name' => $team?->name,
                'members_count' => $team ? $team->members()->count() + 1 : 0,
                'pending_invites' => $team ? $team->invitations()->where('status', 'pending')->count() : 0,
            ],
            'idea' => [
                'exists' => (bool) $team?->idea,
                'status' => $team?->idea?->status ?? 'not_submitted',
                'track' => $team?->track?->name,
                'last_feedback' => $team?->idea?->reviews()->latest()->first()?->created_at,
            ],
            'deadlines' => [
                'registration_ends' => $currentHackathon->registration_end_date->diffForHumans(),
                'idea_submission_ends' => $currentHackathon->idea_submission_end_date->diffForHumans(),
                'days_remaining' => $currentHackathon->idea_submission_end_date->diffInDays(now()),
            ],
            'workshops' => [
                'registered' => $user->workshopRegistrations()->count(),
                'attended' => $user->workshopRegistrations()->where('attended', true)->count(),
                'upcoming' => $user->workshopRegistrations()
                    ->whereHas('workshop', function ($q) {
                        $q->where('date_time', '>', now());
                    })->count(),
            ]
        ];
        
        // Get recent activities
        $activities = collect();
        
        if ($team) {
            // Add team activities
            $activities = $activities->merge(
                $team->activities()
                    ->with('causer')
                    ->latest()
                    ->take(10)
                    ->get()
                    ->map(function ($activity) {
                        return [
                            'id' => $activity->id,
                            'type' => $activity->description,
                            'message' => $this->formatActivityMessage($activity),
                            'time' => $activity->created_at->diffForHumans(),
                            'icon' => $this->getActivityIcon($activity->description),
                        ];
                    })
            );
        }
        
        // Get notifications
        $notifications = $user->notifications()
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'data' => $notification->data,
                    'read' => (bool) $notification->read_at,
                    'time' => $notification->created_at->diffForHumans(),
                ];
            });
        
        return Inertia::render('TeamLeader/Dashboard/Index', [
            'hackathon' => $currentHackathon->only([
                'id', 'name', 'year', 'theme', 
                'registration_start_date', 'registration_end_date',
                'idea_submission_start_date', 'idea_submission_end_date'
            ]),
            'statistics' => $stats,
            'activities' => $activities,
            'notifications' => $notifications,
            'team' => $team ? $team->load(['members', 'idea', 'track']) : null,
        ]);
    }
    
    private function formatActivityMessage($activity)
    {
        $causer = $activity->causer?->name ?? 'System';
        
        switch ($activity->description) {
            case 'team_created':
                return "{$causer} created the team";
            case 'member_joined':
                return "{$causer} joined the team";
            case 'member_removed':
                return "{$causer} was removed from the team";
            case 'idea_submitted':
                return "{$causer} submitted the idea";
            case 'idea_updated':
                return "{$causer} updated the idea";
            case 'idea_reviewed':
                return "Your idea was reviewed by supervisor";
            default:
                return $activity->description;
        }
    }
    
    private function getActivityIcon($description)
    {
        $icons = [
            'team_created' => 'UserGroupIcon',
            'member_joined' => 'UserPlusIcon',
            'member_removed' => 'UserMinusIcon',
            'idea_submitted' => 'LightBulbIcon',
            'idea_updated' => 'PencilIcon',
            'idea_reviewed' => 'CheckCircleIcon',
        ];
        
        return $icons[$description] ?? 'InformationCircleIcon';
    }
}
```

### Create `app/Http/Controllers/TeamLeader/TeamController.php`:

```php
<?php

namespace App\Http\Controllers\TeamLeader;

use App\Http\Controllers\Controller;
use App\Http\Requests\TeamLeader\CreateTeamRequest;
use App\Http\Requests\TeamLeader\InviteMemberRequest;
use App\Models\Team;
use App\Models\User;
use App\Models\TeamMember;
use App\Models\TeamInvitation;
use App\Models\Hackathon;
use App\Models\Track;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class TeamController extends Controller
{
    protected $notificationService;
    
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
    
    public function create()
    {
        $user = auth()->user();
        $currentHackathon = Hackathon::current()->first();
        
        // Check if user already has a team
        if ($user->getCurrentTeam()) {
            return redirect()->route('team-leader.team.show')
                ->with('warning', 'You already have a team for this hackathon.');
        }
        
        // Check if registration is open
        if (!$currentHackathon || !$currentHackathon->isRegistrationOpen()) {
            return redirect()->route('team-leader.dashboard')
                ->with('error', 'Team registration is currently closed.');
        }
        
        return Inertia::render('TeamLeader/Team/Create', [
            'hackathon' => $currentHackathon,
            'maxMembers' => config('hackathon.max_team_members', 5),
        ]);
    }
    
    public function store(CreateTeamRequest $request)
    {
        $user = auth()->user();
        $currentHackathon = Hackathon::current()->first();
        
        // Double-check no existing team
        if ($user->getCurrentTeam()) {
            return back()->with('error', 'You already have a team.');
        }
        
        DB::beginTransaction();
        try {
            // Create the team
            $team = Team::create([
                'name' => $request->name,
                'description' => $request->description,
                'hackathon_id' => $currentHackathon->id,
                'leader_id' => $user->id,
                'status' => 'draft',
            ]);
            
            // Log activity
            activity()
                ->performedOn($team)
                ->causedBy($user)
                ->withProperties(['team_name' => $team->name])
                ->log('team_created');
            
            // Send invitations if emails provided
            if ($request->filled('member_emails')) {
                foreach ($request->member_emails as $email) {
                    $this->sendInvitation($team, $email);
                }
            }
            
            DB::commit();
            
            return redirect()->route('team-leader.team.show')
                ->with('success', 'Team created successfully!');
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to create team. Please try again.');
        }
    }
    
    public function show()
    {
        $user = auth()->user();
        $team = $user->getCurrentTeam();
        
        if (!$team) {
            return redirect()->route('team-leader.team.create')
                ->with('info', 'Please create a team first.');
        }
        
        // Load team with relationships
        $team->load([
            'members.user',
            'invitations',
            'idea',
            'track',
            'hackathon'
        ]);
        
        // Get pending join requests
        $pendingRequests = TeamMember::where('team_id', $team->id)
            ->where('join_status', 'pending')
            ->with('user')
            ->get();
        
        return Inertia::render('TeamLeader/Team/Show', [
            'team' => $team,
            'members' => $team->members->map(function ($member) {
                return [
                    'id' => $member->id,
                    'user' => $member->user->only(['id', 'name', 'email', 'profile_photo_url']),
                    'joined_at' => $member->joined_at,
                    'status' => $member->join_status,
                ];
            }),
            'invitations' => $team->invitations->map(function ($invitation) {
                return [
                    'id' => $invitation->id,
                    'email' => $invitation->email,
                    'status' => $invitation->status,
                    'sent_at' => $invitation->created_at,
                    'expires_at' => $invitation->expires_at,
                ];
            }),
            'pendingRequests' => $pendingRequests->map(function ($request) {
                return [
                    'id' => $request->id,
                    'user' => $request->user->only(['id', 'name', 'email']),
                    'requested_at' => $request->created_at,
                ];
            }),
            'maxMembers' => config('hackathon.max_team_members', 5),
            'currentMembersCount' => $team->members->where('join_status', 'approved')->count() + 1, // +1 for leader
        ]);
    }
    
    public function update(Request $request)
    {
        $team = auth()->user()->getCurrentTeam();
        
        if (!$team) {
            return back()->with('error', 'No team found.');
        }
        
        // Only allow updates if team is in draft or needs_edit status
        if (!in_array($team->status, ['draft', 'needs_edit'])) {
            return back()->with('error', 'Cannot update team in current status.');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:teams,name,' . $team->id,
            'description' => 'required|string|min:10|max:500',
        ]);
        
        $team->update($validated);
        
        activity()
            ->performedOn($team)
            ->causedBy(auth()->user())
            ->log('team_updated');
        
        return back()->with('success', 'Team updated successfully.');
    }
    
    public function inviteMember(InviteMemberRequest $request)
    {
        $team = auth()->user()->getCurrentTeam();
        
        if (!$team) {
            return back()->with('error', 'No team found.');
        }
        
        // Check team capacity
        $currentMembers = $team->members->where('join_status', 'approved')->count() + 1;
        $maxMembers = config('hackathon.max_team_members', 5);
        
        if ($currentMembers >= $maxMembers) {
            return back()->with('error', 'Team has reached maximum capacity.');
        }
        
        // Check if already invited or member
        $email = $request->email;
        
        // Check if user exists
        $invitedUser = User::where('email', $email)->first();
        
        if ($invitedUser) {
            // Check if already in team
            if ($team->hasMember($invitedUser)) {
                return back()->with('error', 'User is already a team member.');
            }
            
            // Check if user is in another team
            if ($invitedUser->getCurrentTeam()) {
                return back()->with('error', 'User is already in another team.');
            }
        }
        
        // Check for existing invitation
        $existingInvite = TeamInvitation::where('team_id', $team->id)
            ->where('email', $email)
            ->where('status', 'pending')
            ->first();
            
        if ($existingInvite) {
            return back()->with('error', 'Invitation already sent to this email.');
        }
        
        // Send invitation
        $this->sendInvitation($team, $email, $request->message);
        
        return back()->with('success', 'Invitation sent successfully.');
    }
    
    public function removeMember($memberId)
    {
        $team = auth()->user()->getCurrentTeam();
        
        if (!$team) {
            return back()->with('error', 'No team found.');
        }
        
        $member = TeamMember::where('team_id', $team->id)
            ->where('id', $memberId)
            ->first();
            
        if (!$member) {
            return back()->with('error', 'Member not found.');
        }
        
        // Cannot remove if idea is already submitted
        if ($team->idea && $team->idea->status !== 'draft') {
            return back()->with('error', 'Cannot remove members after idea submission.');
        }
        
        $memberName = $member->user->name;
        $member->delete();
        
        activity()
            ->performedOn($team)
            ->causedBy(auth()->user())
            ->withProperties(['removed_member' => $memberName])
            ->log('member_removed');
        
        // Notify the removed member
        $this->notificationService->notify($member->user, 'team.removed', [
            'team_name' => $team->name,
        ]);
        
        return back()->with('success', 'Member removed from team.');
    }
    
    public function approveMember($memberId)
    {
        $team = auth()->user()->getCurrentTeam();
        
        if (!$team) {
            return back()->with('error', 'No team found.');
        }
        
        $member = TeamMember::where('team_id', $team->id)
            ->where('id', $memberId)
            ->where('join_status', 'pending')
            ->first();
            
        if (!$member) {
            return back()->with('error', 'Pending member not found.');
        }
        
        // Check team capacity
        $currentMembers = $team->members->where('join_status', 'approved')->count() + 1;
        $maxMembers = config('hackathon.max_team_members', 5);
        
        if ($currentMembers >= $maxMembers) {
            return back()->with('error', 'Team has reached maximum capacity.');
        }
        
        $member->update([
            'join_status' => 'approved',
            'joined_at' => now(),
        ]);
        
        activity()
            ->performedOn($team)
            ->causedBy(auth()->user())
            ->withProperties(['approved_member' => $member->user->name])
            ->log('member_approved');
        
        // Notify the approved member
        $this->notificationService->notify($member->user, 'team.approved', [
            'team_name' => $team->name,
        ]);
        
        return back()->with('success', 'Member approved successfully.');
    }
    
    public function tracks()
    {
        $currentHackathon = Hackathon::current()->first();
        $team = auth()->user()->getCurrentTeam();
        
        $tracks = Track::where('hackathon_id', $currentHackathon->id)
            ->withCount('teams')
            ->with('supervisor:id,name')
            ->get();
        
        return Inertia::render('TeamLeader/Tracks/Index', [
            'tracks' => $tracks,
            'currentTrack' => $team?->track,
            'canSelectTrack' => $team && !$team->idea, // Can only select before submitting idea
        ]);
    }
    
    public function selectTrack($trackId)
    {
        $team = auth()->user()->getCurrentTeam();
        
        if (!$team) {
            return back()->with('error', 'No team found.');
        }
        
        if ($team->idea) {
            return back()->with('error', 'Cannot change track after submitting idea.');
        }
        
        $track = Track::findOrFail($trackId);
        
        // Check if track belongs to current hackathon
        if ($track->hackathon_id !== $team->hackathon_id) {
            return back()->with('error', 'Invalid track selection.');
        }
        
        $team->update(['track_id' => $track->id]);
        
        activity()
            ->performedOn($team)
            ->causedBy(auth()->user())
            ->withProperties(['track_name' => $track->name])
            ->log('track_selected');
        
        return back()->with('success', 'Track selected successfully.');
    }
    
    private function sendInvitation(Team $team, string $email, string $message = null)
    {
        $invitation = TeamInvitation::create([
            'team_id' => $team->id,
            'email' => $email,
            'token' => \Str::random(32),
            'message' => $message,
            'expires_at' => now()->addDays(7),
            'status' => 'pending',
        ]);
        
        // Send invitation email
        $this->notificationService->sendEmail($email, 'team.invitation', [
            'team_name' => $team->name,
            'leader_name' => $team->leader->name,
            'message' => $message,
            'accept_url' => route('team-member.team.accept-invitation', $invitation->token),
            'expires_at' => $invitation->expires_at,
        ]);
        
        return $invitation;
    }
}
```

---

## 📂 PART 3: REQUEST VALIDATION CLASSES

### Create `app/Http/Requests/TeamLeader/CreateTeamRequest.php`:

```php
<?php

namespace App\Http\Requests\TeamLeader;

use Illuminate\Foundation\Http\FormRequest;

class CreateTeamRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->hasRole('team_leader');
    }

    public function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:50',
                'unique:teams,name',
                'regex:/^[a-zA-Z0-9\s\-\_]+$/' // Only alphanumeric, spaces, hyphens, underscores
            ],
            'description' => [
                'required',
                'string',
                'min:10',
                'max:500'
            ],
            'member_emails' => [
                'nullable',
                'array',
                'max:4' // Max 4 initial invites (+ leader = 5)
            ],
            'member_emails.*' => [
                'email',
                'distinct' // No duplicate emails
            ]
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Team name is required.',
            'name.unique' => 'This team name is already taken.',
            'name.regex' => 'Team name can only contain letters, numbers, spaces, hyphens, and underscores.',
            'description.required' => 'Team description is required.',
            'description.min' => 'Team description must be at least 10 characters.',
            'member_emails.*.email' => 'Please provide valid email addresses.',
            'member_emails.*.distinct' => 'Duplicate email addresses are not allowed.',
        ];
    }
}
```

### Create `app/Http/Requests/TeamLeader/SubmitIdeaRequest.php`:

```php
<?php

namespace App\Http\Requests\TeamLeader;

use Illuminate\Foundation\Http\FormRequest;

class SubmitIdeaRequest extends FormRequest
{
    public function authorize()
    {
        $user = $this->user();
        $team = $user->getCurrentTeam();
        
        return $user->hasRole('team_leader') && 
               $team && 
               $user->id === $team->leader_id;
    }

    public function rules()
    {
        return [
            'title' => [
                'required',
                'string',
                'min:10',
                'max:100'
            ],
            'track_id' => [
                'required',
                'exists:tracks,id'
            ],
            'brief_description' => [
                'required',
                'string',
                'min:100',
                'max:500'
            ],
            'problem_statement' => [
                'required',
                'string',
                'min:200',
                'max:2000'
            ],
            'proposed_solution' => [
                'required',
                'string',
                'min:200',
                'max:3000'
            ],
            'target_audience' => [
                'required',
                'string',
                'min:50',
                'max:500'
            ],
            'expected_impact' => [
                'required',
                'string',
                'min:100',
                'max:1000'
            ],
            'technology_stack' => [
                'nullable',
                'string',
                'max:500'
            ],
            'implementation_timeline' => [
                'nullable',
                'string',
                'max:1000'
            ],
            'required_resources' => [
                'nullable',
                'string',
                'max:1000'
            ],
            'attachments' => [
                'nullable',
                'array',
                'max:8'
            ],
            'attachments.*' => [
                'file',
                'mimes:pdf,ppt,pptx,doc,docx',
                'max:15360' // 15MB in KB
            ]
        ];
    }

    public function messages()
    {
        return [
            'title.min' => 'Idea title must be at least 10 characters.',
            'brief_description.min' => 'Brief description must be at least 100 characters.',
            'problem_statement.min' => 'Problem statement must be at least 200 characters.',
            'proposed_solution.min' => 'Proposed solution must be at least 200 characters.',
            'attachments.*.mimes' => 'Only PDF, PowerPoint, and Word documents are allowed.',
            'attachments.*.max' => 'Each file must not exceed 15MB.',
        ];
    }
}
```

---

## 📂 PART 4: SERVICES LAYER

### Create `app/Services/NotificationService.php`:

```php
<?php

namespace App\Services;

use App\Models\User;
use App\Mail\TeamInvitation;
use App\Mail\IdeaStatusChanged;
use App\Mail\WorkshopRegistration;
use App\Notifications\InAppNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send email notification
     */
    public function sendEmail($to, $template, $data = [])
    {
        try {
            switch ($template) {
                case 'team.invitation':
                    Mail::to($to)->send(new TeamInvitation($data));
                    break;
                    
                case 'idea.status_changed':
                    Mail::to($to)->send(new IdeaStatusChanged($data));
                    break;
                    
                case 'workshop.registration':
                    Mail::to($to)->send(new WorkshopRegistration($data));
                    break;
                    
                default:
                    Log::warning("Unknown email template: {$template}");
                    return false;
            }
            
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send email: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Send in-app notification
     */
    public function notify(User $user, $type, $data = [])
    {
        $user->notify(new InAppNotification($type, $data));
    }
    
    /**
     * Send SMS notification (if configured)
     */
    public function sendSMS($phone, $message)
    {
        // Implement SMS gateway integration
        // Example: Twilio, Unifonic, etc.
        Log::info("SMS would be sent to {$phone}: {$message}");
    }
    
    /**
     * Send notification to Twitter
     */
    public function tweet($message, $mediaPath = null)
    {
        // Implement Twitter API integration
        Log::info("Tweet would be posted: {$message}");
    }
}
```

### Create `app/Services/QRCodeService.php`:

```php
<?php

namespace App\Services;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class QRCodeService
{
    /**
     * Generate QR code for workshop registration
     */
    public function generateRegistrationQR($registrationId, $token)
    {
        $data = json_encode([
            'type' => 'workshop_registration',
            'id' => $registrationId,
            'token' => $token,
            'url' => route('workshop-supervisor.checkins.scan', [
                'token' => $token
            ])
        ]);
        
        $qrCode = QrCode::format('png')
            ->size(400)
            ->margin(2)
            ->errorCorrection('H')
            ->generate($data);
        
        // Save to storage
        $filename = "qr/registrations/{$registrationId}.png";
        Storage::disk('public')->put($filename, $qrCode);
        
        return $filename;
    }
    
    /**
     * Validate QR code data
     */
    public function validateQR($data)
    {
        try {
            $decoded = json_decode($data, true);
            
            if (!isset($decoded['type']) || !isset($decoded['token'])) {
                return ['valid' => false, 'error' => 'Invalid QR format'];
            }
            
            // Additional validation based on type
            switch ($decoded['type']) {
                case 'workshop_registration':
                    return $this->validateWorkshopQR($decoded);
                default:
                    return ['valid' => false, 'error' => 'Unknown QR type'];
            }
        } catch (\Exception $e) {
            return ['valid' => false, 'error' => 'Failed to decode QR'];
        }
    }
    
    private function validateWorkshopQR($data)
    {
        // Validate against database
        $registration = \App\Models\WorkshopRegistration::where('id', $data['id'])
            ->where('qr_token', $data['token'])
            ->first();
            
        if (!$registration) {
            return ['valid' => false, 'error' => 'Registration not found'];
        }
        
        if ($registration->attended) {
            return ['valid' => false, 'error' => 'Already checked in'];
        }
        
        return [
            'valid' => true,
            'registration' => $registration
        ];
    }
}
```

---

## 📂 PART 5: VUE COMPONENTS - TEAM LEADER DASHBOARD

### Create `resources/js/Pages/TeamLeader/Dashboard/Index.vue`:

```vue
<template>
  <Default>
    <div class="p-6 space-y-6">
      <!-- Page Header -->
      <div class="flex justify-between items-center">
        <div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
            {{ $t('dashboard.title', 'Dashboard') }}
          </h1>
          <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
            {{ $t('dashboard.welcome', 'Welcome back') }}, {{ user.name }}
          </p>
        </div>
        <div class="flex space-x-3">
          <Link v-if="!statistics.team.exists" 
                :href="route('team-leader.team.create')"
                class="btn btn-primary">
            <UserGroupIcon class="w-5 h-5 mr-2" />
            {{ $t('team.create', 'Create Team') }}
          </Link>
          <Link v-else-if="!statistics.idea.exists"
                :href="route('team-leader.idea.create')"
                class="btn btn-primary">
            <LightBulbIcon class="w-5 h-5 mr-2" />
            {{ $t('idea.submit', 'Submit Idea') }}
          </Link>
        </div>
      </div>

      <!-- Deadline Alert -->
      <div v-if="statistics.deadlines.days_remaining <= 7" 
           class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
        <div class="flex">
          <ExclamationTriangleIcon class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mr-3" />
          <div>
            <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
              {{ $t('deadline.warning', 'Deadline Approaching') }}
            </h3>
            <p class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">
              {{ $t('deadline.days_left', '{days} days remaining for idea submission', { days: statistics.deadlines.days_remaining }) }}
            </p>
          </div>
        </div>
      </div>

      <!-- Statistics Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Team Status Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ $t('team.status', 'Team Status') }}
              </p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                {{ statistics.team.exists ? statistics.team.name : $t('team.none', 'No Team') }}
              </p>
              <p v-if="statistics.team.exists" class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                {{ statistics.team.members_count }} {{ $t('team.members', 'members') }}
                <span v-if="statistics.team.pending_invites > 0" class="text-yellow-600">
                  ({{ statistics.team.pending_invites }} {{ $t('team.pending', 'pending') }})
                </span>
              </p>
            </div>
            <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
              <UserGroupIcon class="w-6 h-6 text-blue-600 dark:text-blue-400" />
            </div>
          </div>
          <Link v-if="statistics.team.exists" 
                :href="route('team-leader.team.show')"
                class="mt-4 text-sm text-blue-600 dark:text-blue-400 hover:underline flex items-center">
            {{ $t('team.manage', 'Manage Team') }}
            <ArrowRightIcon class="w-4 h-4 ml-1" />
          </Link>
        </div>

        <!-- Idea Status Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ $t('idea.status', 'Idea Status') }}
              </p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                <StatusBadge :status="statistics.idea.status" />
              </p>
              <p v-if="statistics.idea.track" class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                {{ statistics.idea.track }}
              </p>
            </div>
            <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-lg">
              <LightBulbIcon class="w-6 h-6 text-green-600 dark:text-green-400" />
            </div>
          </div>
          <Link v-if="statistics.idea.exists" 
                :href="route('team-leader.idea.show')"
                class="mt-4 text-sm text-green-600 dark:text-green-400 hover:underline flex items-center">
            {{ $t('idea.view', 'View Idea') }}
            <ArrowRightIcon class="w-4 h-4 ml-1" />
          </Link>
        </div>

        <!-- Workshops Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ $t('workshops.registered', 'Workshops') }}
              </p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                {{ statistics.workshops.registered }}
              </p>
              <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                {{ statistics.workshops.upcoming }} {{ $t('workshops.upcoming', 'upcoming') }}
              </p>
            </div>
            <div class="p-3 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
              <AcademicCapIcon class="w-6 h-6 text-purple-600 dark:text-purple-400" />
            </div>
          </div>
          <Link :href="route('team-leader.workshops.index')"
                class="mt-4 text-sm text-purple-600 dark:text-purple-400 hover:underline flex items-center">
            {{ $t('workshops.browse', 'Browse Workshops') }}
            <ArrowRightIcon class="w-4 h-4 ml-1" />
          </Link>
        </div>

        <!-- Deadline Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ $t('deadline.submission', 'Submission Deadline') }}
              </p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                {{ statistics.deadlines.days_remaining }}
              </p>
              <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                {{ $t('deadline.days', 'days remaining') }}
              </p>
            </div>
            <div class="p-3 bg-orange-100 dark:bg-orange-900/30 rounded-lg">
              <ClockIcon class="w-6 h-6 text-orange-600 dark:text-orange-400" />
            </div>
          </div>
          <p class="mt-4 text-xs text-gray-500 dark:text-gray-400">
            {{ $t('deadline.ends', 'Ends') }} {{ statistics.deadlines.idea_submission_ends }}
          </p>
        </div>
      </div>

      <!-- Activity Feed & Notifications -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Activity -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-lg shadow">
          <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
              {{ $t('activity.recent', 'Recent Activity') }}
            </h2>
          </div>
          <div class="p-6">
            <div v-if="activities.length > 0" class="space-y-4">
              <div v-for="activity in activities" :key="activity.id" 
                   class="flex items-start space-x-3">
                <div class="flex-shrink-0">
                  <div class="p-2 bg-gray-100 dark:bg-gray-700 rounded-lg">
                    <component :is="activity.icon" class="w-5 h-5 text-gray-600 dark:text-gray-400" />
                  </div>
                </div>
                <div class="flex-1">
                  <p class="text-sm text-gray-900 dark:text-white">
                    {{ activity.message }}
                  </p>
                  <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    {{ activity.time }}
                  </p>
                </div>
              </div>
            </div>
            <div v-else class="text-center py-8">
              <p class="text-gray-500 dark:text-gray-400">
                {{ $t('activity.none', 'No recent activity') }}
              </p>
            </div>
          </div>
        </div>

        <!-- Notifications -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
          <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
              {{ $t('notifications.title', 'Notifications') }}
            </h2>
          </div>
          <div class="p-6">
            <div v-if="notifications.length > 0" class="space-y-3">
              <div v-for="notification in notifications" :key="notification.id"
                   :class="[
                     'p-3 rounded-lg cursor-pointer transition-colors',
                     notification.read 
                       ? 'bg-gray-50 dark:bg-gray-700/50' 
                       : 'bg-blue-50 dark:bg-blue-900/20'
                   ]">
                <p class="text-sm text-gray-900 dark:text-white">
                  {{ notification.data.message }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                  {{ notification.time }}
                </p>
              </div>
            </div>
            <div v-else class="text-center py-8">
              <BellIcon class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-3" />
              <p class="text-gray-500 dark:text-gray-400">
                {{ $t('notifications.none', 'No new notifications') }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Default>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import Default from '@/Layouts/Default.vue'
import StatusBadge from '@/Components/Shared/StatusBadge.vue'
import {
  UserGroupIcon,
  LightBulbIcon,
  AcademicCapIcon,
  ClockIcon,
  ArrowRightIcon,
  ExclamationTriangleIcon,
  BellIcon,
  UserPlusIcon,
  PencilIcon,
  CheckCircleIcon,
  InformationCircleIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  hackathon: Object,
  statistics: Object,
  activities: Array,
  notifications: Array,
  team: Object
})

const page = usePage()
const user = page.props.auth.user

// Icon mapping for activities
const activityIcons = {
  'UserGroupIcon': UserGroupIcon,
  'UserPlusIcon': UserPlusIcon,
  'LightBulbIcon': LightBulbIcon,
  'PencilIcon': PencilIcon,
  'CheckCircleIcon': CheckCircleIcon,
  'InformationCircleIcon': InformationCircleIcon
}

// Map icon names to components
props.activities?.forEach(activity => {
  activity.icon = activityIcons[activity.icon] || InformationCircleIcon
})
</script>
```

---

## 📍 CONTINUE POINT MARKER

**YOU ARE HERE:** We've completed:
1. ✅ Complete routes structure for all roles
2. ✅ Team Leader Dashboard Controller
3. ✅ Team Controller with all CRUD operations
4. ✅ Request validation classes
5. ✅ Service layer (Notification & QR Code)
6. ✅ Team Leader Dashboard Vue component

**NEXT TO BUILD:**
1. Team Leader Idea submission flow
2. Track Supervisor review interface
3. Workshop Supervisor check-in system
4. Workshop registration flow
5. Email templates
6. Arabic translations

This file gives you ready-to-copy code implementations. Continue from "Team Leader Idea submission flow" when you reach the message limit.
