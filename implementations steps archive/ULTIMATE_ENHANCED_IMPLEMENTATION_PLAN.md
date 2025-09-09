# 🏗️ **ULTIMATE ENHANCED IMPLEMENTATION PLAN - CLEAN ARCHITECTURE**
**Complete Hackathon Management System with Proper Layered Architecture**
**Version:** 4.0 - Following Controller → Service → Repository → Model Pattern
**Date:** January 2025

## 🎯 **ARCHITECTURE PRINCIPLES**

### **Layered Architecture Pattern:**
```
┌─────────────────┐
│   Controllers   │ → Handle HTTP requests, validation, responses
├─────────────────┤
│    Services     │ → Business logic, orchestration, transactions
├─────────────────┤
│  Repositories   │ → Data access layer, complex queries
├─────────────────┤
│     Models      │ → Eloquent models, relationships, attributes
└─────────────────┘
```

### **Key Rules:**
1. **Controllers** ONLY talk to Services (never directly to Models/Repositories)
2. **Services** ONLY talk to Repositories (never directly to Models)
3. **Repositories** ONLY talk to Models
4. **Models** handle database relationships and attributes

---

## 📁 **COMPLETE PROJECT STRUCTURE**

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Base/                    # Abstract base controllers
│   │   │   ├── BaseController.php
│   │   │   ├── BaseCrudController.php
│   │   │   └── BaseAuthController.php
│   │   ├── SystemAdmin/
│   │   │   ├── DashboardController.php
│   │   │   ├── EditionController.php
│   │   │   ├── UserController.php
│   │   │   ├── TeamController.php
│   │   │   ├── IdeaController.php
│   │   │   ├── WorkshopController.php
│   │   │   ├── NewsController.php
│   │   │   ├── ReportController.php
│   │   │   └── SettingsController.php
│   │   ├── HackathonAdmin/
│   │   │   ├── DashboardController.php
│   │   │   ├── TeamController.php
│   │   │   ├── IdeaController.php
│   │   │   ├── WorkshopController.php
│   │   │   └── NewsController.php
│   │   ├── TrackSupervisor/
│   │   │   ├── DashboardController.php
│   │   │   ├── IdeaController.php
│   │   │   └── WorkshopController.php
│   │   ├── TeamLeader/
│   │   │   ├── DashboardController.php
│   │   │   ├── TeamController.php
│   │   │   └── IdeaController.php
│   │   └── TeamMember/
│   │       ├── DashboardController.php
│   │       ├── TeamController.php
│   │       └── WorkshopController.php
│   │
│   ├── Requests/                    # Form validation
│   │   ├── Team/
│   │   │   ├── CreateTeamRequest.php
│   │   │   ├── UpdateTeamRequest.php
│   │   │   ├── ApproveTeamRequest.php
│   │   │   └── InviteMemberRequest.php
│   │   ├── Idea/
│   │   │   ├── SubmitIdeaRequest.php
│   │   │   ├── UpdateIdeaRequest.php
│   │   │   └── ReviewIdeaRequest.php
│   │   └── Workshop/
│   │       ├── CreateWorkshopRequest.php
│   │       ├── UpdateWorkshopRequest.php
│   │       └── RegisterWorkshopRequest.php
│   │
│   └── Middleware/
│       ├── RoleMiddleware.php
│       └── TeamOwnershipMiddleware.php
│
├── Services/                        # Business Logic Layer
│   ├── Contracts/                  # Service interfaces
│   │   ├── TeamServiceInterface.php
│   │   ├── IdeaServiceInterface.php
│   │   ├── WorkshopServiceInterface.php
│   │   ├── UserServiceInterface.php
│   │   └── NotificationServiceInterface.php
│   ├── Team/
│   │   ├── TeamService.php
│   │   ├── TeamApprovalService.php
│   │   ├── TeamMemberService.php
│   │   └── TeamStatisticsService.php
│   ├── Idea/
│   │   ├── IdeaService.php
│   │   ├── IdeaReviewService.php
│   │   ├── IdeaFileService.php
│   │   └── IdeaScoringService.php
│   ├── Workshop/
│   │   ├── WorkshopService.php
│   │   ├── WorkshopRegistrationService.php
│   │   ├── WorkshopAttendanceService.php
│   │   └── WorkshopQRService.php
│   ├── User/
│   │   ├── UserService.php
│   │   ├── UserRoleService.php
│   │   └── UserAuthenticationService.php
│   └── Shared/
│       ├── NotificationService.php
│       ├── EmailService.php
│       ├── SMSService.php
│       └── TwitterService.php
│
├── Repositories/                    # Data Access Layer
│   ├── Contracts/                  # Repository interfaces
│   │   ├── TeamRepositoryInterface.php
│   │   ├── IdeaRepositoryInterface.php
│   │   ├── WorkshopRepositoryInterface.php
│   │   └── UserRepositoryInterface.php
│   ├── Eloquent/                   # Eloquent implementations
│   │   ├── TeamRepository.php
│   │   ├── IdeaRepository.php
│   │   ├── WorkshopRepository.php
│   │   ├── UserRepository.php
│   │   ├── HackathonRepository.php
│   │   └── TrackRepository.php
│   └── Criteria/                   # Query criteria
│       ├── TeamCriteria.php
│       ├── IdeaCriteria.php
│       └── WorkshopCriteria.php
│
├── Models/                         # Eloquent Models
│   ├── User.php
│   ├── Team.php
│   ├── TeamMember.php
│   ├── Idea.php
│   ├── IdeaFile.php
│   ├── IdeaReview.php
│   ├── Workshop.php
│   ├── WorkshopRegistration.php
│   ├── WorkshopAttendance.php
│   ├── Hackathon.php
│   ├── HackathonEdition.php
│   ├── Track.php
│   ├── News.php
│   ├── Speaker.php
│   └── Organization.php
│
└── Providers/
    ├── RepositoryServiceProvider.php  # Bind interfaces
    └── ServiceServiceProvider.php     # Service bindings
```

---

## 🔧 **IMPLEMENTATION EXAMPLES**

### **1. CONTROLLER LAYER**

```php
// app/Http/Controllers/SystemAdmin/TeamController.php
<?php

namespace App\Http\Controllers\SystemAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Team\CreateTeamRequest;
use App\Http\Requests\Team\UpdateTeamRequest;
use App\Http\Requests\Team\ApproveTeamRequest;
use App\Services\Team\TeamService;
use App\Services\Team\TeamApprovalService;
use App\Services\Team\TeamStatisticsService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TeamController extends Controller
{
    public function __construct(
        private TeamService $teamService,
        private TeamApprovalService $approvalService,
        private TeamStatisticsService $statisticsService
    ) {}

    /**
     * Display listing of teams
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status', 'hackathon_id', 'track_id']);
        $perPage = $request->get('per_page', 20);
        
        $teams = $this->teamService->getAllTeams($filters, $perPage);
        $statistics = $this->statisticsService->getTeamStatistics();
        
        return Inertia::render('SystemAdmin/Teams/Index', [
            'teams' => $teams,
            'statistics' => $statistics,
            'filters' => $filters,
            'can' => [
                'create' => true,
                'edit' => true,
                'delete' => true,
                'approve' => true,
                'export' => true,
            ]
        ]);
    }

    /**
     * Show team details
     */
    public function show(int $teamId)
    {
        $team = $this->teamService->getTeamById($teamId);
        $members = $this->teamService->getTeamMembers($teamId);
        $activities = $this->teamService->getTeamActivities($teamId);
        
        return Inertia::render('SystemAdmin/Teams/Show', [
            'team' => $team,
            'members' => $members,
            'activities' => $activities,
        ]);
    }

    /**
     * Store new team
     */
    public function store(CreateTeamRequest $request)
    {
        $team = $this->teamService->createTeam(
            $request->validated(),
            auth()->id()
        );
        
        return redirect()
            ->route('system-admin.teams.show', $team['id'])
            ->with('success', 'Team created successfully');
    }

    /**
     * Update team
     */
    public function update(UpdateTeamRequest $request, int $teamId)
    {
        $this->teamService->updateTeam($teamId, $request->validated());
        
        return redirect()
            ->route('system-admin.teams.show', $teamId)
            ->with('success', 'Team updated successfully');
    }

    /**
     * Approve team
     */
    public function approve(ApproveTeamRequest $request, int $teamId)
    {
        $this->approvalService->approveTeam(
            $teamId,
            auth()->id(),
            $request->get('notes')
        );
        
        return redirect()
            ->route('system-admin.teams.index')
            ->with('success', 'Team approved successfully');
    }

    /**
     * Bulk approve teams
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'team_ids' => 'required|array',
            'team_ids.*' => 'integer|exists:teams,id'
        ]);
        
        $result = $this->approvalService->bulkApproveTeams(
            $request->team_ids,
            auth()->id()
        );
        
        return response()->json([
            'message' => "Successfully approved {$result['approved']} teams",
            'approved' => $result['approved'],
            'failed' => $result['failed']
        ]);
    }

    /**
     * Delete team
     */
    public function destroy(int $teamId)
    {
        $this->teamService->deleteTeam($teamId);
        
        return redirect()
            ->route('system-admin.teams.index')
            ->with('success', 'Team deleted successfully');
    }
}
```

### **2. SERVICE LAYER**

```php
// app/Services/Team/TeamService.php
<?php

namespace App\Services\Team;

use App\Repositories\Contracts\TeamRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\Shared\NotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TeamService
{
    public function __construct(
        private TeamRepositoryInterface $teamRepository,
        private UserRepositoryInterface $userRepository,
        private NotificationService $notificationService
    ) {}

    /**
     * Get all teams with filters
     */
    public function getAllTeams(array $filters = [], int $perPage = 20): array
    {
        try {
            $teams = $this->teamRepository->getAllWithFilters($filters, $perPage);
            
            return [
                'data' => $this->formatTeamsData($teams['data']),
                'pagination' => $teams['pagination']
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get teams', [
                'filters' => $filters,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get team by ID
     */
    public function getTeamById(int $teamId): array
    {
        $team = $this->teamRepository->findById($teamId);
        
        if (!$team) {
            throw new \Exception("Team not found with ID: {$teamId}");
        }
        
        return $this->formatTeamData($team);
    }

    /**
     * Create new team
     */
    public function createTeam(array $data, int $leaderId): array
    {
        DB::beginTransaction();
        
        try {
            // Get leader information
            $leader = $this->userRepository->findById($leaderId);
            
            if (!$leader) {
                throw new \Exception("Leader not found");
            }
            
            // Check if leader already has a team
            if ($this->teamRepository->userHasTeam($leaderId)) {
                throw new \Exception("User already leads a team");
            }
            
            // Create team
            $teamData = array_merge($data, [
                'leader_id' => $leaderId,
                'join_code' => $this->generateUniqueJoinCode(),
                'status' => 'pending',
                'max_members' => $data['max_members'] ?? 5
            ]);
            
            $team = $this->teamRepository->create($teamData);
            
            // Add leader as first member
            $this->teamRepository->addMember($team['id'], $leaderId, 'accepted');
            
            // Send invitations if provided
            if (!empty($data['member_emails'])) {
                foreach ($data['member_emails'] as $email) {
                    $this->inviteMember($team['id'], $email);
                }
            }
            
            // Send notification to leader
            $this->notificationService->notifyTeamCreated($leader, $team);
            
            DB::commit();
            
            return $this->formatTeamData($team);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create team', [
                'data' => $data,
                'leader_id' => $leaderId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Update team
     */
    public function updateTeam(int $teamId, array $data): array
    {
        DB::beginTransaction();
        
        try {
            $team = $this->teamRepository->findById($teamId);
            
            if (!$team) {
                throw new \Exception("Team not found");
            }
            
            $updatedTeam = $this->teamRepository->update($teamId, $data);
            
            // Log activity
            $this->teamRepository->logActivity($teamId, 'team_updated', $data);
            
            DB::commit();
            
            return $this->formatTeamData($updatedTeam);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update team', [
                'team_id' => $teamId,
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Delete team
     */
    public function deleteTeam(int $teamId): bool
    {
        DB::beginTransaction();
        
        try {
            $team = $this->teamRepository->findById($teamId);
            
            if (!$team) {
                throw new \Exception("Team not found");
            }
            
            // Notify members before deletion
            $members = $this->teamRepository->getMembers($teamId);
            foreach ($members as $member) {
                $this->notificationService->notifyTeamDeleted($member, $team);
            }
            
            // Delete team (cascade will handle related records)
            $result = $this->teamRepository->delete($teamId);
            
            DB::commit();
            
            return $result;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete team', [
                'team_id' => $teamId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get team members
     */
    public function getTeamMembers(int $teamId): array
    {
        $members = $this->teamRepository->getMembers($teamId);
        
        return array_map(function($member) {
            return [
                'id' => $member['id'],
                'name' => $member['name'],
                'email' => $member['email'],
                'status' => $member['status'],
                'joined_at' => $member['joined_at'],
                'role' => $member['role']
            ];
        }, $members);
    }

    /**
     * Get team activities
     */
    public function getTeamActivities(int $teamId, int $limit = 20): array
    {
        return $this->teamRepository->getActivities($teamId, $limit);
    }

    /**
     * Invite member to team
     */
    private function inviteMember(int $teamId, string $email): void
    {
        $user = $this->userRepository->findByEmail($email);
        
        if (!$user) {
            // Send external invitation
            $this->notificationService->sendExternalTeamInvitation($email, $teamId);
            return;
        }
        
        // Check if user already in a team
        if ($this->teamRepository->userHasTeam($user['id'])) {
            Log::warning('User already in a team', [
                'user_id' => $user['id'],
                'team_id' => $teamId
            ]);
            return;
        }
        
        // Add as pending member
        $this->teamRepository->addMember($teamId, $user['id'], 'invited');
        
        // Send notification
        $this->notificationService->notifyTeamInvitation($user, $teamId);
    }

    /**
     * Generate unique join code
     */
    private function generateUniqueJoinCode(): string
    {
        do {
            $code = strtoupper(substr(md5(uniqid()), 0, 8));
        } while ($this->teamRepository->joinCodeExists($code));
        
        return $code;
    }

    /**
     * Format team data for response
     */
    private function formatTeamData(array $team): array
    {
        return [
            'id' => $team['id'],
            'name' => $team['name'],
            'description' => $team['description'],
            'join_code' => $team['join_code'],
            'status' => $team['status'],
            'max_members' => $team['max_members'],
            'members_count' => $team['members_count'] ?? 0,
            'leader' => $team['leader'] ?? null,
            'track' => $team['track'] ?? null,
            'hackathon' => $team['hackathon'] ?? null,
            'created_at' => $team['created_at'],
            'updated_at' => $team['updated_at']
        ];
    }

    /**
     * Format multiple teams data
     */
    private function formatTeamsData(array $teams): array
    {
        return array_map(fn($team) => $this->formatTeamData($team), $teams);
    }
}
```

### **3. REPOSITORY LAYER**

```php
// app/Repositories/Contracts/TeamRepositoryInterface.php
<?php

namespace App\Repositories\Contracts;

interface TeamRepositoryInterface
{
    public function getAllWithFilters(array $filters, int $perPage): array;
    public function findById(int $id): ?array;
    public function findByJoinCode(string $code): ?array;
    public function create(array $data): array;
    public function update(int $id, array $data): array;
    public function delete(int $id): bool;
    public function userHasTeam(int $userId): bool;
    public function joinCodeExists(string $code): bool;
    public function addMember(int $teamId, int $userId, string $status): bool;
    public function removeMember(int $teamId, int $userId): bool;
    public function getMembers(int $teamId): array;
    public function getActivities(int $teamId, int $limit): array;
    public function logActivity(int $teamId, string $action, array $data): void;
}
```

```php
// app/Repositories/Eloquent/TeamRepository.php
<?php

namespace App\Repositories\Eloquent;

use App\Models\Team;
use App\Models\TeamMember;
use App\Models\AuditLog;
use App\Repositories\Contracts\TeamRepositoryInterface;
use Illuminate\Support\Facades\DB;

class TeamRepository implements TeamRepositoryInterface
{
    protected Team $model;
    
    public function __construct(Team $model)
    {
        $this->model = $model;
    }

    /**
     * Get all teams with filters
     */
    public function getAllWithFilters(array $filters, int $perPage): array
    {
        $query = $this->model->query()
            ->with(['leader', 'track', 'hackathon'])
            ->withCount('members');
        
        // Apply filters
        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                  ->orWhereHas('leader', function($q) use ($filters) {
                      $q->where('name', 'like', "%{$filters['search']}%");
                  });
            });
        }
        
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        if (!empty($filters['hackathon_id'])) {
            $query->where('hackathon_id', $filters['hackathon_id']);
        }
        
        if (!empty($filters['track_id'])) {
            $query->where('track_id', $filters['track_id']);
        }
        
        // Order and paginate
        $paginated = $query->orderBy('created_at', 'desc')->paginate($perPage);
        
        return [
            'data' => $paginated->items(),
            'pagination' => [
                'total' => $paginated->total(),
                'per_page' => $paginated->perPage(),
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'from' => $paginated->firstItem(),
                'to' => $paginated->lastItem()
            ]
        ];
    }

    /**
     * Find team by ID
     */
    public function findById(int $id): ?array
    {
        $team = $this->model
            ->with(['leader', 'members.user', 'track', 'hackathon', 'ideas'])
            ->find($id);
        
        return $team ? $team->toArray() : null;
    }

    /**
     * Find team by join code
     */
    public function findByJoinCode(string $code): ?array
    {
        $team = $this->model
            ->where('join_code', $code)
            ->with(['leader', 'track', 'hackathon'])
            ->first();
        
        return $team ? $team->toArray() : null;
    }

    /**
     * Create new team
     */
    public function create(array $data): array
    {
        $team = $this->model->create($data);
        $team->load(['leader', 'track', 'hackathon']);
        
        return $team->toArray();
    }

    /**
     * Update team
     */
    public function update(int $id, array $data): array
    {
        $team = $this->model->findOrFail($id);
        $team->update($data);
        $team->load(['leader', 'track', 'hackathon']);
        
        return $team->toArray();
    }

    /**
     * Delete team
     */
    public function delete(int $id): bool
    {
        return $this->model->findOrFail($id)->delete();
    }

    /**
     * Check if user has team
     */
    public function userHasTeam(int $userId): bool
    {
        return $this->model
            ->where('leader_id', $userId)
            ->orWhereHas('members', function($q) use ($userId) {
                $q->where('user_id', $userId)
                  ->where('status', 'accepted');
            })
            ->exists();
    }

    /**
     * Check if join code exists
     */
    public function joinCodeExists(string $code): bool
    {
        return $this->model->where('join_code', $code)->exists();
    }

    /**
     * Add member to team
     */
    public function addMember(int $teamId, int $userId, string $status): bool
    {
        return TeamMember::create([
            'team_id' => $teamId,
            'user_id' => $userId,
            'status' => $status,
            'joined_at' => $status === 'accepted' ? now() : null
        ]) !== null;
    }

    /**
     * Remove member from team
     */
    public function removeMember(int $teamId, int $userId): bool
    {
        return TeamMember::where('team_id', $teamId)
            ->where('user_id', $userId)
            ->delete() > 0;
    }

    /**
     * Get team members
     */
    public function getMembers(int $teamId): array
    {
        $members = TeamMember::with('user')
            ->where('team_id', $teamId)
            ->get();
        
        return $members->map(function($member) {
            return [
                'id' => $member->user->id,
                'name' => $member->user->name,
                'email' => $member->user->email,
                'status' => $member->status,
                'joined_at' => $member->joined_at,
                'role' => $member->user->primary_role
            ];
        })->toArray();
    }

    /**
     * Get team activities
     */
    public function getActivities(int $teamId, int $limit): array
    {
        return AuditLog::where('model_type', Team::class)
            ->where('model_id', $teamId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Log team activity
     */
    public function logActivity(int $teamId, string $action, array $data): void
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'model_type' => Team::class,
            'model_id' => $teamId,
            'action' => $action,
            'new_values' => $data,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }
}
```

### **4. SERVICE PROVIDER BINDINGS**

```php
// app/Providers/RepositoryServiceProvider.php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register repository bindings
     */
    public function register(): void
    {
        // Team repositories
        $this->app->bind(
            \App\Repositories\Contracts\TeamRepositoryInterface::class,
            \App\Repositories\Eloquent\TeamRepository::class
        );
        
        // User repositories
        $this->app->bind(
            \App\Repositories\Contracts\UserRepositoryInterface::class,
            \App\Repositories\Eloquent\UserRepository::class
        );
        
        // Idea repositories
        $this->app->bind(
            \App\Repositories\Contracts\IdeaRepositoryInterface::class,
            \App\Repositories\Eloquent\IdeaRepository::class
        );
        
        // Workshop repositories
        $this->app->bind(
            \App\Repositories\Contracts\WorkshopRepositoryInterface::class,
            \App\Repositories\Eloquent\WorkshopRepository::class
        );
        
        // Hackathon repositories
        $this->app->bind(
            \App\Repositories\Contracts\HackathonRepositoryInterface::class,
            \App\Repositories\Eloquent\HackathonRepository::class
        );
        
        // Track repositories
        $this->app->bind(
            \App\Repositories\Contracts\TrackRepositoryInterface::class,
            \App\Repositories\Eloquent\TrackRepository::class
        );
    }
}
```

---

## 🎨 **FRONTEND ARCHITECTURE**

### **Shared Vue Components Structure**

```
resources/js/
├── Components/
│   ├── Base/                       # Abstract base components
│   │   ├── BaseTable.vue
│   │   ├── BaseForm.vue
│   │   ├── BaseModal.vue
│   │   └── BasePage.vue
│   ├── Shared/                     # Shared across all roles
│   │   ├── RoleBasedTable.vue
│   │   ├── RoleBasedForm.vue
│   │   ├── StatusBadge.vue
│   │   ├── ActionButtons.vue
│   │   └── ConfirmDialog.vue
│   ├── Team/                       # Team-specific components
│   │   ├── TeamTable.vue
│   │   ├── TeamForm.vue
│   │   ├── TeamCard.vue
│   │   ├── MembersList.vue
│   │   └── InviteMemberModal.vue
│   ├── Idea/                       # Idea-specific components
│   │   ├── IdeaTable.vue
│   │   ├── IdeaForm.vue
│   │   ├── IdeaReviewForm.vue
│   │   ├── IdeaFileUploader.vue
│   │   └── IdeaScoreCard.vue
│   └── Workshop/                   # Workshop-specific components
│       ├── WorkshopTable.vue
│       ├── WorkshopForm.vue
│       ├── WorkshopCard.vue
│       ├── RegistrationModal.vue
│       └── AttendanceScanner.vue
│
├── Composables/                    # Reusable Vue logic
│   ├── useTeams.js
│   ├── useIdeas.js
│   ├── useWorkshops.js
│   ├── usePermissions.js
│   ├── useNotifications.js
│   └── useFilters.js
│
├── Stores/                         # Pinia stores
│   ├── auth.js
│   ├── teams.js
│   ├── ideas.js
│   ├── workshops.js
│   └── notifications.js
│
└── Pages/
    ├── SystemAdmin/
    │   └── Teams/
    │       ├── Index.vue           # Uses RoleBasedTable
    │       ├── Show.vue            # Uses TeamCard, MembersList
    │       ├── Create.vue          # Uses TeamForm
    │       └── Edit.vue            # Uses TeamForm
    ├── HackathonAdmin/
    │   └── Teams/
    │       └── Index.vue           # Uses same RoleBasedTable with different permissions
    ├── TeamLeader/
    │   └── Team/
    │       └── Show.vue            # Uses TeamCard, MembersList with limited actions
    └── TeamMember/
        └── Team/
            └── Show.vue            # Uses TeamCard (read-only)
```

### **Shared Component Example - RoleBasedTable**

```vue
<!-- resources/js/Components/Shared/RoleBasedTable.vue -->
<template>
  <div class="bg-white rounded-lg shadow">
    <!-- Header with role-based actions -->
    <div class="px-6 py-4 border-b border-gray-200">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="text-lg font-medium text-gray-900">{{ title }}</h3>
          <p v-if="subtitle" class="mt-1 text-sm text-gray-600">{{ subtitle }}</p>
        </div>
        
        <div class="flex space-x-2">
          <!-- Create button - only for authorized roles -->
          <Link 
            v-if="can.create" 
            :href="createRoute"
            class="btn-primary"
          >
            <PlusIcon class="w-4 h-4 mr-2" />
            {{ createLabel }}
          </Link>
          
          <!-- Bulk actions - only when items selected -->
          <div v-if="hasSelection" class="flex space-x-2">
            <button 
              v-if="can.bulkApprove" 
              @click="handleBulkApprove"
              class="btn-success"
            >
              Approve ({{ selectedItems.length }})
            </button>
            
            <button 
              v-if="can.bulkDelete" 
              @click="handleBulkDelete"
              class="btn-danger"
            >
              Delete ({{ selectedItems.length }})
            </button>
            
            <button 
              v-if="can.export" 
              @click="handleExport"
              class="btn-secondary"
            >
              Export
            </button>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Filters -->
    <div v-if="showFilters" class="px-6 py-4 border-b border-gray-200 bg-gray-50">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <FormInput
          v-model="filters.search"
          placeholder="Search..."
          @input="debouncedSearch"
        />
        
        <FormSelect
          v-if="filterOptions.status"
          v-model="filters.status"
          :options="filterOptions.status"
          placeholder="All Status"
          @change="applyFilters"
        />
        
        <FormSelect
          v-if="filterOptions.track && showTrackFilter"
          v-model="filters.track_id"
          :options="filterOptions.track"
          placeholder="All Tracks"
          @change="applyFilters"
        />
        
        <button
          @click="resetFilters"
          class="btn-secondary"
        >
          Reset Filters
        </button>
      </div>
    </div>
    
    <!-- Table -->
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <!-- Selection checkbox -->
            <th v-if="enableSelection" class="px-6 py-3 text-left">
              <input
                type="checkbox"
                v-model="selectAll"
                @change="toggleSelectAll"
                class="rounded border-gray-300"
              />
            </th>
            
            <!-- Dynamic columns -->
            <th
              v-for="column in visibleColumns"
              :key="column.key"
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
            >
              {{ column.label }}
            </th>
            
            <!-- Actions column -->
            <th v-if="hasActions" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
              Actions
            </th>
          </tr>
        </thead>
        
        <tbody class="bg-white divide-y divide-gray-200">
          <tr
            v-for="item in items"
            :key="item.id"
            :class="{ 'bg-gray-50': isSelected(item.id) }"
          >
            <!-- Selection checkbox -->
            <td v-if="enableSelection" class="px-6 py-4">
              <input
                type="checkbox"
                :value="item.id"
                v-model="selectedItems"
                class="rounded border-gray-300"
              />
            </td>
            
            <!-- Dynamic columns -->
            <td
              v-for="column in visibleColumns"
              :key="column.key"
              class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
            >
              <component
                v-if="column.component"
                :is="column.component"
                :value="getNestedValue(item, column.key)"
                :item="item"
              />
              <span v-else>
                {{ getNestedValue(item, column.key) }}
              </span>
            </td>
            
            <!-- Actions -->
            <td v-if="hasActions" class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <ActionButtons
                :item="item"
                :actions="getAvailableActions(item)"
                @action="handleAction"
              />
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    
    <!-- Pagination -->
    <div v-if="pagination" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
      <Pagination
        :links="pagination.links"
        :from="pagination.from"
        :to="pagination.to"
        :total="pagination.total"
      />
    </div>
    
    <!-- Empty state -->
    <div v-if="!items.length" class="text-center py-12">
      <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
      </svg>
      <h3 class="mt-2 text-sm font-medium text-gray-900">No {{ resourceName }} found</h3>
      <p class="mt-1 text-sm text-gray-500">Get started by creating a new {{ resourceNameSingular }}.</p>
      <div class="mt-6">
        <Link
          v-if="can.create"
          :href="createRoute"
          class="btn-primary"
        >
          <PlusIcon class="w-4 h-4 mr-2" />
          New {{ resourceNameSingular }}
        </Link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { debounce } from 'lodash'
import { PlusIcon } from '@heroicons/vue/24/outline'
import Pagination from '@/Components/Pagination.vue'
import FormInput from '@/Components/FormInput.vue'
import FormSelect from '@/Components/FormSelect.vue'
import ActionButtons from '@/Components/Shared/ActionButtons.vue'
import { usePermissions } from '@/Composables/usePermissions'

const props = defineProps({
  title: String,
  subtitle: String,
  resourceName: {
    type: String,
    default: 'items'
  },
  resourceNameSingular: {
    type: String,
    default: 'item'
  },
  items: {
    type: Array,
    default: () => []
  },
  columns: {
    type: Array,
    required: true
  },
  pagination: Object,
  can: {
    type: Object,
    default: () => ({})
  },
  createRoute: String,
  createLabel: {
    type: String,
    default: 'Create New'
  },
  enableSelection: {
    type: Boolean,
    default: false
  },
  showFilters: {
    type: Boolean,
    default: true
  },
  filterOptions: {
    type: Object,
    default: () => ({})
  },
  userRole: String
})

const { hasPermission } = usePermissions()

// Selection state
const selectedItems = ref([])
const selectAll = ref(false)

// Filter state
const filters = ref({
  search: '',
  status: '',
  track_id: ''
})

// Computed
const hasSelection = computed(() => selectedItems.value.length > 0)
const hasActions = computed(() => {
  return props.can.edit || props.can.delete || props.can.approve
})

const visibleColumns = computed(() => {
  // Filter columns based on user role
  return props.columns.filter(col => {
    if (!col.roles) return true
    return col.roles.includes(props.userRole)
  })
})

const showTrackFilter = computed(() => {
  // Only show track filter for certain roles
  return ['system_admin', 'hackathon_admin'].includes(props.userRole)
})

// Methods
const getNestedValue = (obj, path) => {
  return path.split('.').reduce((curr, prop) => curr?.[prop], obj)
}

const isSelected = (id) => {
  return selectedItems.value.includes(id)
}

const toggleSelectAll = () => {
  if (selectAll.value) {
    selectedItems.value = props.items.map(item => item.id)
  } else {
    selectedItems.value = []
  }
}

const getAvailableActions = (item) => {
  const actions = []
  
  if (props.can.view) {
    actions.push({
      label: 'View',
      icon: 'EyeIcon',
      route: route(`${props.userRole}.${props.resourceName}.show`, item.id)
    })
  }
  
  if (props.can.edit) {
    actions.push({
      label: 'Edit',
      icon: 'PencilIcon',
      route: route(`${props.userRole}.${props.resourceName}.edit`, item.id)
    })
  }
  
  if (props.can.approve && item.status === 'pending') {
    actions.push({
      label: 'Approve',
      icon: 'CheckIcon',
      action: () => handleApprove(item)
    })
  }
  
  if (props.can.delete) {
    actions.push({
      label: 'Delete',
      icon: 'TrashIcon',
      action: () => handleDelete(item),
      class: 'text-red-600 hover:text-red-900'
    })
  }
  
  return actions
}

const handleAction = ({ action, item }) => {
  if (typeof action === 'function') {
    action(item)
  }
}

const handleApprove = (item) => {
  router.post(route(`${props.userRole}.${props.resourceName}.approve`, item.id))
}

const handleDelete = (item) => {
  if (confirm(`Are you sure you want to delete this ${props.resourceNameSingular}?`)) {
    router.delete(route(`${props.userRole}.${props.resourceName}.destroy`, item.id))
  }
}

const handleBulkApprove = () => {
  router.post(route(`${props.userRole}.${props.resourceName}.bulk-approve`), {
    ids: selectedItems.value
  })
}

const handleBulkDelete = () => {
  if (confirm(`Are you sure you want to delete ${selectedItems.value.length} ${props.resourceName}?`)) {
    router.delete(route(`${props.userRole}.${props.resourceName}.bulk-destroy`), {
      data: { ids: selectedItems.value }
    })
  }
}

const handleExport = () => {
  window.location.href = route(`${props.userRole}.${props.resourceName}.export`, {
    ids: selectedItems.value,
    ...filters.value
  })
}

const applyFilters = () => {
  router.get(route(route().current()), filters.value, {
    preserveState: true,
    preserveScroll: true
  })
}

const debouncedSearch = debounce(() => {
  applyFilters()
}, 300)

const resetFilters = () => {
  filters.value = {
    search: '',
    status: '',
    track_id: ''
  }
  applyFilters()
}

// Watch for items change to reset selection
watch(() => props.items, () => {
  selectedItems.value = []
  selectAll.value = false
})
</script>
```

---

## 📋 **COMPLETE IMPLEMENTATION CHECKLIST**

### **Phase 1: Architecture Setup (Week 1)**
- [ ] Create base controller classes
- [ ] Create service interfaces and implementations
- [ ] Create repository interfaces and implementations
- [ ] Set up service providers for dependency injection
- [ ] Create request validation classes
- [ ] Set up middleware for role-based access

### **Phase 2: Core Services (Week 2)**
- [ ] Implement TeamService with all methods
- [ ] Implement IdeaService with review logic
- [ ] Implement WorkshopService with registration
- [ ] Implement UserService with role management
- [ ] Implement NotificationService
- [ ] Add transaction handling to all services

### **Phase 3: Repository Layer (Week 3)**
- [ ] Create TeamRepository with complex queries
- [ ] Create IdeaRepository with filtering
- [ ] Create WorkshopRepository with attendance
- [ ] Create UserRepository with role queries
- [ ] Add caching layer to repositories
- [ ] Implement query criteria classes

### **Phase 4: Controllers (Week 4)**
- [ ] Implement SystemAdmin controllers
- [ ] Implement HackathonAdmin controllers
- [ ] Implement TrackSupervisor controllers
- [ ] Implement TeamLeader controllers
- [ ] Implement TeamMember controllers
- [ ] Add proper error handling to all controllers

### **Phase 5: Frontend Components (Week 5)**
- [ ] Create shared base components
- [ ] Create role-based table component
- [ ] Create role-based form components
- [ ] Create reusable modal components
- [ ] Implement Vue composables
- [ ] Set up Pinia stores

### **Phase 6: Testing & Documentation (Week 6)**
- [ ] Write unit tests for services
- [ ] Write integration tests for repositories
- [ ] Write feature tests for controllers
- [ ] Create API documentation
- [ ] Write user documentation
- [ ] Performance optimization

---

## 🎯 **KEY BENEFITS OF THIS ARCHITECTURE**

1. **Clean Separation of Concerns**
   - Controllers handle HTTP only
   - Services contain business logic
   - Repositories handle data access
   - Models handle relationships

2. **Maximum Code Reusability**
   - Base classes for common functionality
   - Shared services across roles
   - Reusable Vue components
   - Single source of truth for business logic

3. **Easy Testing**
   - Mock repositories in service tests
   - Mock services in controller tests
   - No direct database access in controllers

4. **Scalability**
   - Easy to add new roles
   - Easy to modify business logic
   - Clear dependency injection
   - Cacheable at repository level

5. **Maintainability**
   - Clear code organization
   - Single responsibility principle
   - Easy to debug and trace issues
   - Consistent patterns throughout

This architecture ensures that your hackathon management system is built with professional standards, making it easy to maintain, test, and scale as needed.
