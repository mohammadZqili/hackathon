# 🏗️ CLEAN ARCHITECTURE IMPLEMENTATION GUIDE
## Proper Structure: Controllers → Services → Repositories → Models

---

## 📐 ARCHITECTURE OVERVIEW

### Layer Responsibilities:
```
┌─────────────────────────────────────────────────────────┐
│                     CONTROLLERS                         │
│         (HTTP handling, validation, responses)          │
└────────────────────┬────────────────────────────────────┘
                     │
┌────────────────────▼────────────────────────────────────┐
│                     SERVICES                            │
│        (Business logic, orchestration, rules)           │
└────────────────────┬────────────────────────────────────┘
                     │
┌────────────────────▼────────────────────────────────────┐
│                   REPOSITORIES                          │
│         (Data access, queries, persistence)             │
└────────────────────┬────────────────────────────────────┘
                     │
┌────────────────────▼────────────────────────────────────┐
│                     MODELS                              │
│           (Entities, relationships, scopes)             │
└─────────────────────────────────────────────────────────┘
```

---

## 📁 DIRECTORY STRUCTURE

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── TeamLeader/
│   │   │   ├── TeamController.php       # HTTP handling only
│   │   │   └── IdeaController.php
│   │   ├── TrackSupervisor/
│   │   │   └── ReviewController.php
│   │   └── ...
│   │
│   ├── Requests/                        # Form Requests for validation
│   │   ├── CreateTeamRequest.php
│   │   ├── SubmitIdeaRequest.php
│   │   └── ReviewIdeaRequest.php
│   │
│   └── Resources/                       # API Resources
│       ├── TeamResource.php
│       ├── IdeaResource.php
│       └── UserResource.php
│
├── Services/                             # Business Logic Layer
│   ├── Team/
│   │   ├── TeamService.php
│   │   ├── TeamInvitationService.php
│   │   └── TeamMemberService.php
│   ├── Idea/
│   │   ├── IdeaService.php
│   │   ├── IdeaReviewService.php
│   │   └── IdeaFileService.php
│   ├── Workshop/
│   │   ├── WorkshopService.php
│   │   └── WorkshopRegistrationService.php
│   └── Notification/
│       └── NotificationService.php
│
├── Repositories/                         # Data Access Layer
│   ├── Contracts/                       # Repository Interfaces
│   │   ├── TeamRepositoryInterface.php
│   │   ├── IdeaRepositoryInterface.php
│   │   └── UserRepositoryInterface.php
│   │
│   ├── Eloquent/                        # Implementations
│   │   ├── TeamRepository.php
│   │   ├── IdeaRepository.php
│   │   └── UserRepository.php
│   │
│   └── RepositoryServiceProvider.php    # Bind interfaces
│
├── Models/                               # Eloquent Models
│   ├── User.php
│   ├── Team.php
│   ├── Idea.php
│   └── Workshop.php
│
├── DTOs/                                 # Data Transfer Objects
│   ├── CreateTeamDTO.php
│   ├── IdeaSubmissionDTO.php
│   └── WorkshopRegistrationDTO.php
│
├── Events/                               # Domain Events
│   ├── TeamCreated.php
│   ├── IdeaSubmitted.php
│   └── IdeaReviewed.php
│
├── Listeners/                            # Event Handlers
│   ├── SendTeamCreatedNotification.php
│   └── NotifySupervisorOfNewIdea.php
│
└── Exceptions/                           # Custom Exceptions
    ├── TeamLimitExceededException.php
    ├── IdeaAlreadySubmittedException.php
    └── RegistrationClosedException.php
```

---

## 💼 IMPLEMENTATION EXAMPLES

### 1. CONTROLLER (Thin Controller)

**File:** `app/Http/Controllers/TeamLeader/TeamController.php`

```php
<?php

namespace App\Http\Controllers\TeamLeader;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTeamRequest;
use App\Http\Requests\InviteMemberRequest;
use App\Http\Resources\TeamResource;
use App\Services\Team\TeamService;
use App\Services\Team\TeamInvitationService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TeamController extends Controller
{
    public function __construct(
        private TeamService $teamService,
        private TeamInvitationService $invitationService
    ) {}

    /**
     * Display team creation form
     */
    public function create()
    {
        // Controller only handles HTTP concerns
        $tracks = $this->teamService->getAvailableTracksForCurrentHackathon();
        
        return Inertia::render('TeamLeader/Team/Create', [
            'tracks' => $tracks,
            'canCreateTeam' => $this->teamService->canUserCreateTeam(auth()->user())
        ]);
    }

    /**
     * Store new team
     */
    public function store(CreateTeamRequest $request)
    {
        try {
            // Validation already done by FormRequest
            // Controller delegates to service
            $team = $this->teamService->createTeam(
                auth()->user(),
                $request->toDTO() // Convert to DTO
            );

            return redirect()
                ->route('team-leader.team.show')
                ->with('success', 'تم إنشاء الفريق بنجاح / Team created successfully');
                
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display team management page
     */
    public function show()
    {
        $team = $this->teamService->getTeamWithDetails(auth()->user());
        
        if (!$team) {
            return redirect()->route('team-leader.team.create');
        }

        return Inertia::render('TeamLeader/Team/Show', [
            'team' => new TeamResource($team),
            'invitations' => $team->pendingInvitations,
            'joinRequests' => $team->pendingJoinRequests
        ]);
    }

    /**
     * Invite member to team
     */
    public function inviteMember(InviteMemberRequest $request)
    {
        try {
            $invitation = $this->invitationService->inviteMember(
                auth()->user()->leadingTeam,
                $request->validated()
            );

            return response()->json([
                'message' => 'تم إرسال الدعوة / Invitation sent',
                'invitation' => $invitation
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 422);
        }
    }
}
```

---

### 2. FORM REQUEST (Validation)

**File:** `app/Http/Requests/CreateTeamRequest.php`

```php
<?php

namespace App\Http\Requests;

use App\DTOs\CreateTeamDTO;
use Illuminate\Foundation\Http\FormRequest;

class CreateTeamRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Can use policies here
        return $this->user()->role === 'team_leader';
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:100',
                'unique:teams,name',
                'regex:/^[a-zA-Z0-9\s\-\_\.]+$/u'
            ],
            'description' => 'nullable|string|max:500',
            'track_id' => [
                'required',
                'integer',
                'exists:tracks,id',
                function ($attribute, $value, $fail) {
                    // Custom validation using service
                    if (!app(\App\Services\Team\TeamService::class)->isTrackAvailable($value)) {
                        $fail('المسار غير متاح / Track not available');
                    }
                }
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم الفريق مطلوب / Team name is required',
            'name.unique' => 'اسم الفريق محجوز / Team name already taken',
            'name.regex' => 'اسم الفريق يحتوي على رموز غير مسموحة / Invalid characters in team name',
            'track_id.required' => 'يجب اختيار المسار / Track selection is required',
            'track_id.exists' => 'المسار غير موجود / Track does not exist'
        ];
    }

    /**
     * Convert validated data to DTO
     */
    public function toDTO(): CreateTeamDTO
    {
        return new CreateTeamDTO(
            name: $this->validated('name'),
            description: $this->validated('description'),
            trackId: $this->validated('track_id')
        );
    }
}
```

---

### 3. SERVICE (Business Logic)

**File:** `app/Services/Team/TeamService.php`

```php
<?php

namespace App\Services\Team;

use App\DTOs\CreateTeamDTO;
use App\Events\TeamCreated;
use App\Exceptions\TeamLimitExceededException;
use App\Exceptions\RegistrationClosedException;
use App\Models\User;
use App\Models\Team;
use App\Repositories\Contracts\TeamRepositoryInterface;
use App\Repositories\Contracts\HackathonRepositoryInterface;
use App\Services\Notification\NotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TeamService
{
    public function __construct(
        private TeamRepositoryInterface $teamRepository,
        private HackathonRepositoryInterface $hackathonRepository,
        private NotificationService $notificationService
    ) {}

    /**
     * Create a new team with all business rules
     */
    public function createTeam(User $leader, CreateTeamDTO $dto): Team
    {
        // Business Rule: Check if registration is open
        if (!$this->hackathonRepository->isRegistrationOpen()) {
            throw new RegistrationClosedException(
                'فترة التسجيل مغلقة / Registration period is closed'
            );
        }

        // Business Rule: Leader can only have one team
        if ($this->teamRepository->userHasTeam($leader)) {
            throw new TeamLimitExceededException(
                'لديك فريق بالفعل / You already have a team'
            );
        }

        // Business Rule: Check track capacity
        if (!$this->isTrackAvailable($dto->trackId)) {
            throw new \Exception('المسار ممتلئ / Track is full');
        }

        // Use transaction for data consistency
        return DB::transaction(function () use ($leader, $dto) {
            // Generate unique team code
            $teamCode = $this->generateUniqueTeamCode();
            
            // Create team through repository
            $team = $this->teamRepository->create([
                'name' => $dto->name,
                'description' => $dto->description,
                'track_id' => $dto->trackId,
                'leader_id' => $leader->id,
                'hackathon_id' => $this->hackathonRepository->getCurrentHackathonId(),
                'code' => $teamCode,
                'status' => 'active'
            ]);

            // Add leader as first member
            $this->teamRepository->addMember($team, $leader, 'leader');

            // Fire domain event
            event(new TeamCreated($team, $leader));

            // Send notifications
            $this->notificationService->notifyTeamCreated($team, $leader);

            // Log activity
            activity()
                ->performedOn($team)
                ->causedBy($leader)
                ->withProperties(['team_name' => $team->name])
                ->log('Team created');

            return $team;
        });
    }

    /**
     * Business logic to check if user can create team
     */
    public function canUserCreateTeam(User $user): bool
    {
        // Must be team leader role
        if ($user->role !== 'team_leader') {
            return false;
        }

        // Must not have existing team
        if ($this->teamRepository->userHasTeam($user)) {
            return false;
        }

        // Registration must be open
        if (!$this->hackathonRepository->isRegistrationOpen()) {
            return false;
        }

        return true;
    }

    /**
     * Get available tracks for current hackathon
     */
    public function getAvailableTracksForCurrentHackathon()
    {
        $hackathonId = $this->hackathonRepository->getCurrentHackathonId();
        return $this->teamRepository->getAvailableTracks($hackathonId);
    }

    /**
     * Check if track has capacity
     */
    public function isTrackAvailable(int $trackId): bool
    {
        $track = $this->teamRepository->getTrackById($trackId);
        
        if (!$track) {
            return false;
        }

        // Business Rule: Max 20 teams per track
        $currentTeamsCount = $this->teamRepository->getTeamsCountByTrack($trackId);
        return $currentTeamsCount < 20;
    }

    /**
     * Generate unique team code
     */
    private function generateUniqueTeamCode(): string
    {
        do {
            $code = 'TEAM-' . strtoupper(Str::random(4));
        } while ($this->teamRepository->codeExists($code));

        return $code;
    }

    /**
     * Get team with all details for display
     */
    public function getTeamWithDetails(User $user): ?Team
    {
        $team = $this->teamRepository->getUserTeam($user);
        
        if (!$team) {
            return null;
        }

        // Load relationships efficiently
        return $this->teamRepository->loadTeamRelations($team, [
            'members.user',
            'idea',
            'track',
            'pendingInvitations',
            'pendingJoinRequests.user'
        ]);
    }
}
```

---

### 4. REPOSITORY (Data Access)

**File:** `app/Repositories/Contracts/TeamRepositoryInterface.php`

```php
<?php

namespace App\Repositories\Contracts;

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Collection;

interface TeamRepositoryInterface
{
    public function create(array $data): Team;
    public function find(int $id): ?Team;
    public function userHasTeam(User $user): bool;
    public function getUserTeam(User $user): ?Team;
    public function addMember(Team $team, User $user, string $role = 'member'): void;
    public function removeMember(Team $team, User $user): void;
    public function getTeamsCountByTrack(int $trackId): int;
    public function codeExists(string $code): bool;
    public function getAvailableTracks(int $hackathonId): Collection;
    public function getTrackById(int $trackId);
    public function loadTeamRelations(Team $team, array $relations): Team;
    public function getTeamsByStatus(string $status): Collection;
    public function getTeamStatistics(int $hackathonId): array;
}
```

**File:** `app/Repositories/Eloquent/TeamRepository.php`

```php
<?php

namespace App\Repositories\Eloquent;

use App\Models\Team;
use App\Models\User;
use App\Models\Track;
use App\Models\TeamMember;
use App\Repositories\Contracts\TeamRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class TeamRepository implements TeamRepositoryInterface
{
    /**
     * Create new team
     */
    public function create(array $data): Team
    {
        return Team::create($data);
    }

    /**
     * Find team by ID
     */
    public function find(int $id): ?Team
    {
        return Cache::remember("team.{$id}", 3600, function () use ($id) {
            return Team::find($id);
        });
    }

    /**
     * Check if user has a team
     */
    public function userHasTeam(User $user): bool
    {
        return Team::where('leader_id', $user->id)
            ->orWhereHas('members', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->where('status', 'active');
            })
            ->exists();
    }

    /**
     * Get user's team
     */
    public function getUserTeam(User $user): ?Team
    {
        // Check if user is leader
        $team = Team::where('leader_id', $user->id)->first();
        
        if ($team) {
            return $team;
        }

        // Check if user is member
        return Team::whereHas('members', function ($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->where('status', 'active');
        })->first();
    }

    /**
     * Add member to team
     */
    public function addMember(Team $team, User $user, string $role = 'member'): void
    {
        TeamMember::create([
            'team_id' => $team->id,
            'user_id' => $user->id,
            'role' => $role,
            'status' => 'active',
            'joined_at' => now()
        ]);

        // Clear cache
        Cache::forget("team.{$team->id}");
    }

    /**
     * Remove member from team
     */
    public function removeMember(Team $team, User $user): void
    {
        TeamMember::where('team_id', $team->id)
            ->where('user_id', $user->id)
            ->update(['status' => 'removed', 'removed_at' => now()]);

        Cache::forget("team.{$team->id}");
    }

    /**
     * Get teams count by track
     */
    public function getTeamsCountByTrack(int $trackId): int
    {
        return Cache::remember("track.{$trackId}.teams_count", 300, function () use ($trackId) {
            return Team::where('track_id', $trackId)
                ->where('status', 'active')
                ->count();
        });
    }

    /**
     * Check if team code exists
     */
    public function codeExists(string $code): bool
    {
        return Team::where('code', $code)->exists();
    }

    /**
     * Get available tracks for hackathon
     */
    public function getAvailableTracks(int $hackathonId): Collection
    {
        return Track::where('hackathon_id', $hackathonId)
            ->where('is_active', true)
            ->withCount('teams')
            ->having('teams_count', '<', 20) // Max 20 teams per track
            ->get();
    }

    /**
     * Get track by ID
     */
    public function getTrackById(int $trackId)
    {
        return Track::find($trackId);
    }

    /**
     * Load team relations efficiently
     */
    public function loadTeamRelations(Team $team, array $relations): Team
    {
        return $team->load($relations);
    }

    /**
     * Get teams by status
     */
    public function getTeamsByStatus(string $status): Collection
    {
        return Team::where('status', $status)
            ->with(['leader', 'track', 'idea'])
            ->get();
    }

    /**
     * Get team statistics for hackathon
     */
    public function getTeamStatistics(int $hackathonId): array
    {
        return Cache::remember("hackathon.{$hackathonId}.stats", 600, function () use ($hackathonId) {
            return [
                'total_teams' => Team::where('hackathon_id', $hackathonId)->count(),
                'active_teams' => Team::where('hackathon_id', $hackathonId)
                    ->where('status', 'active')
                    ->count(),
                'teams_with_ideas' => Team::where('hackathon_id', $hackathonId)
                    ->whereHas('idea')
                    ->count(),
                'average_team_size' => TeamMember::whereHas('team', function ($q) use ($hackathonId) {
                    $q->where('hackathon_id', $hackathonId);
                })
                ->where('status', 'active')
                ->groupBy('team_id')
                ->selectRaw('AVG(COUNT(*)) as avg_size')
                ->value('avg_size') ?? 0
            ];
        });
    }
}
```

---

### 5. DATA TRANSFER OBJECT (DTO)

**File:** `app/DTOs/CreateTeamDTO.php`

```php
<?php

namespace App\DTOs;

class CreateTeamDTO
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $description,
        public readonly int $trackId
    ) {}

    /**
     * Create from array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            description: $data['description'] ?? null,
            trackId: $data['track_id']
        );
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'track_id' => $this->trackId
        ];
    }
}
```

---

### 6. MODEL (Entity)

**File:** `app/Models/Team.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'code',
        'leader_id',
        'track_id',
        'hackathon_id',
        'status'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relationships
     */
    public function leader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    public function track(): BelongsTo
    {
        return $this->belongsTo(Track::class);
    }

    public function hackathon(): BelongsTo
    {
        return $this->belongsTo(Hackathon::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(TeamMember::class);
    }

    public function activeMembers(): HasMany
    {
        return $this->members()->where('status', 'active');
    }

    public function idea(): HasOne
    {
        return $this->hasOne(Idea::class);
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(TeamInvitation::class);
    }

    public function pendingInvitations(): HasMany
    {
        return $this->invitations()->where('status', 'pending');
    }

    public function joinRequests(): HasMany
    {
        return $this->hasMany(TeamJoinRequest::class);
    }

    public function pendingJoinRequests(): HasMany
    {
        return $this->joinRequests()->where('status', 'pending');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForCurrentHackathon($query)
    {
        return $query->where('hackathon_id', currentHackathonId());
    }

    public function scopeWithoutIdea($query)
    {
        return $query->doesntHave('idea');
    }

    /**
     * Accessors & Mutators
     */
    public function getIsFull(): bool
    {
        return $this->activeMembers()->count() >= 5;
    }

    public function getMembersCountAttribute(): int
    {
        return $this->activeMembers()->count();
    }

    /**
     * Business Methods
     */
    public function canAddMember(): bool
    {
        return $this->members_count < 5;
    }

    public function hasIdea(): bool
    {
        return $this->idea()->exists();
    }

    public function isLeader(User $user): bool
    {
        return $this->leader_id === $user->id;
    }

    public function hasMember(User $user): bool
    {
        return $this->activeMembers()
            ->where('user_id', $user->id)
            ->exists();
    }
}
```

---

### 7. SERVICE PROVIDER (Dependency Injection)

**File:** `app/Providers/RepositoryServiceProvider.php`

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services
     */
    public function register(): void
    {
        // Bind repository interfaces to implementations
        $this->app->bind(
            \App\Repositories\Contracts\TeamRepositoryInterface::class,
            \App\Repositories\Eloquent\TeamRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\IdeaRepositoryInterface::class,
            \App\Repositories\Eloquent\IdeaRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\UserRepositoryInterface::class,
            \App\Repositories\Eloquent\UserRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\HackathonRepositoryInterface::class,
            \App\Repositories\Eloquent\HackathonRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\WorkshopRepositoryInterface::class,
            \App\Repositories\Eloquent\WorkshopRepository::class
        );
    }
}
```

**Register in `config/app.php`:**
```php
'providers' => [
    // ...
    App\Providers\RepositoryServiceProvider::class,
],
```

---

## 📋 MORE SERVICE EXAMPLES

### Idea Service

**File:** `app/Services/Idea/IdeaService.php`

```php
<?php

namespace App\Services\Idea;

use App\DTOs\IdeaSubmissionDTO;
use App\Events\IdeaSubmitted;
use App\Exceptions\IdeaAlreadySubmittedException;
use App\Exceptions\SubmissionClosedException;
use App\Models\Team;
use App\Models\Idea;
use App\Repositories\Contracts\IdeaRepositoryInterface;
use App\Repositories\Contracts\HackathonRepositoryInterface;
use App\Services\Notification\NotificationService;
use Illuminate\Support\Facades\DB;

class IdeaService
{
    public function __construct(
        private IdeaRepositoryInterface $ideaRepository,
        private HackathonRepositoryInterface $hackathonRepository,
        private NotificationService $notificationService,
        private IdeaFileService $fileService
    ) {}

    /**
     * Submit idea with all validations
     */
    public function submitIdea(Team $team, IdeaSubmissionDTO $dto): Idea
    {
        // Business Rule: Check submission period
        if (!$this->hackathonRepository->isIdeaSubmissionOpen()) {
            throw new SubmissionClosedException(
                'فترة تسليم الأفكار مغلقة / Idea submission closed'
            );
        }

        // Business Rule: One idea per team
        if ($this->ideaRepository->teamHasIdea($team)) {
            throw new IdeaAlreadySubmittedException(
                'الفريق لديه فكرة مسلمة / Team already submitted idea'
            );
        }

        // Business Rule: Team must have at least 2 members
        if ($team->members_count < 2) {
            throw new \Exception(
                'يجب أن يكون الفريق مكون من عضوين على الأقل / Team must have at least 2 members'
            );
        }

        return DB::transaction(function () use ($team, $dto) {
            // Create idea
            $idea = $this->ideaRepository->create([
                'team_id' => $team->id,
                'title' => $dto->title,
                'description' => $dto->description,
                'track_id' => $team->track_id,
                'status' => $dto->submitForReview ? 'pending' : 'draft',
                'submitted_at' => $dto->submitForReview ? now() : null
            ]);

            // Handle file uploads
            if (!empty($dto->files)) {
                foreach ($dto->files as $file) {
                    $this->fileService->attachFile($idea, $file);
                }
            }

            // Fire events if submitted
            if ($dto->submitForReview) {
                event(new IdeaSubmitted($idea));
                
                // Notify supervisor
                $this->notificationService->notifySupervisorOfNewIdea($idea);
            }

            // Log activity
            activity()
                ->performedOn($idea)
                ->causedBy(auth()->user())
                ->withProperties(['idea_title' => $idea->title])
                ->log($dto->submitForReview ? 'Idea submitted' : 'Idea draft saved');

            return $idea;
        });
    }

    /**
     * Update idea (only if in draft or needs_revision status)
     */
    public function updateIdea(Idea $idea, IdeaSubmissionDTO $dto): Idea
    {
        // Business Rule: Can only edit draft or needs_revision
        if (!in_array($idea->status, ['draft', 'needs_revision'])) {
            throw new \Exception(
                'لا يمكن تعديل الفكرة في هذه الحالة / Cannot edit idea in this status'
            );
        }

        return DB::transaction(function () use ($idea, $dto) {
            // Update idea
            $idea = $this->ideaRepository->update($idea, [
                'title' => $dto->title,
                'description' => $dto->description,
                'status' => $dto->submitForReview ? 'pending' : $idea->status,
                'submitted_at' => $dto->submitForReview ? now() : $idea->submitted_at
            ]);

            // Handle new files
            if (!empty($dto->files)) {
                foreach ($dto->files as $file) {
                    $this->fileService->attachFile($idea, $file);
                }
            }

            // Fire events if resubmitted
            if ($dto->submitForReview && $idea->status === 'pending') {
                event(new IdeaSubmitted($idea));
                $this->notificationService->notifySupervisorOfResubmission($idea);
            }

            return $idea;
        });
    }
}
```

---

### Review Service

**File:** `app/Services/Idea/IdeaReviewService.php`

```php
<?php

namespace App\Services\Idea;

use App\DTOs\IdeaReviewDTO;
use App\Events\IdeaReviewed;
use App\Models\Idea;
use App\Models\User;
use App\Repositories\Contracts\IdeaRepositoryInterface;
use App\Services\Notification\NotificationService;
use Illuminate\Support\Facades\DB;

class IdeaReviewService
{
    public function __construct(
        private IdeaRepositoryInterface $ideaRepository,
        private NotificationService $notificationService
    ) {}

    /**
     * Review idea by supervisor
     */
    public function reviewIdea(Idea $idea, User $supervisor, IdeaReviewDTO $dto): void
    {
        // Business Rule: Only pending or under_review ideas
        if (!in_array($idea->status, ['pending', 'under_review'])) {
            throw new \Exception('الفكرة ليست في حالة المراجعة / Idea not in review status');
        }

        // Business Rule: Supervisor must be assigned to track
        if ($supervisor->supervised_track_id !== $idea->track_id) {
            throw new \Exception('ليس لديك صلاحية مراجعة هذه الفكرة / Not authorized to review this idea');
        }

        // Business Rule: Minimum score requirements
        $totalScore = array_sum($dto->scores);
        if ($dto->decision === 'approved' && $totalScore < 60) {
            throw new \Exception('لا يمكن قبول فكرة بدرجة أقل من 60 / Cannot approve idea with score below 60');
        }

        DB::transaction(function () use ($idea, $supervisor, $dto) {
            // Create review record
            $review = $this->ideaRepository->createReview([
                'idea_id' => $idea->id,
                'supervisor_id' => $supervisor->id,
                'scores' => json_encode($dto->scores),
                'total_score' => array_sum($dto->scores),
                'feedback' => $dto->feedback,
                'private_notes' => $dto->privateNotes,
                'decision' => $dto->decision,
                'reviewed_at' => now()
            ]);

            // Update idea status
            $newStatus = match($dto->decision) {
                'approved' => 'approved',
                'rejected' => 'rejected',
                'needs_revision' => 'needs_revision',
                default => $idea->status
            };

            $this->ideaRepository->update($idea, [
                'status' => $newStatus,
                'last_reviewed_at' => now(),
                'supervisor_id' => $supervisor->id
            ]);

            // Fire domain event
            event(new IdeaReviewed($idea, $review));

            // Send notifications
            $this->notificationService->notifyTeamOfReview($idea, $review);

            // Log activity
            activity()
                ->performedOn($idea)
                ->causedBy($supervisor)
                ->withProperties([
                    'decision' => $dto->decision,
                    'score' => array_sum($dto->scores)
                ])
                ->log('Idea reviewed');
        });
    }

    /**
     * Get review statistics for supervisor
     */
    public function getSupervisorStatistics(User $supervisor): array
    {
        return $this->ideaRepository->getReviewStatistics($supervisor->id);
    }
}
```

---

## 🏭 FACTORY PATTERN FOR COMPLEX OBJECTS

**File:** `app/Factories/TeamFactory.php`

```php
<?php

namespace App\Factories;

use App\Models\Team;
use App\Models\User;
use App\Services\Team\TeamService;

class TeamFactory
{
    public function __construct(
        private TeamService $teamService
    ) {}

    /**
     * Create team with initial setup
     */
    public function createWithInitialSetup(User $leader, array $data): Team
    {
        // Create team
        $team = $this->teamService->createTeam($leader, 
            CreateTeamDTO::fromArray($data)
        );

        // Add default settings
        $this->createDefaultSettings($team);

        // Initialize team workspace
        $this->initializeWorkspace($team);

        return $team;
    }

    private function createDefaultSettings(Team $team): void
    {
        // Create default team settings
    }

    private function initializeWorkspace(Team $team): void
    {
        // Initialize team workspace, folders, etc.
    }
}
```

---

## 🎯 BEST PRACTICES IMPLEMENTED

### 1. **Single Responsibility Principle**
- Controllers: Only handle HTTP
- Services: Only business logic
- Repositories: Only data access
- Models: Only entity representation

### 2. **Dependency Injection**
- All dependencies injected via constructor
- Interfaces for repositories
- Service provider for binding

### 3. **Data Transfer Objects (DTOs)**
- Type-safe data transfer
- Validation separated from business logic
- Immutable data structures

### 4. **Transactions**
- All multi-step operations wrapped
- Rollback on failure
- Data consistency guaranteed

### 5. **Caching Strategy**
- Repository level caching
- Cache invalidation on updates
- Statistics cached for performance

### 6. **Event-Driven Architecture**
- Domain events for important actions
- Decoupled notification system
- Activity logging

### 7. **Exception Handling**
- Custom exceptions for business rules
- Meaningful error messages
- Bilingual error messages

### 8. **Testing Structure**
```
tests/
├── Unit/
│   ├── Services/
│   │   ├── TeamServiceTest.php
│   │   └── IdeaServiceTest.php
│   └── Repositories/
│       └── TeamRepositoryTest.php
├── Feature/
│   ├── TeamManagementTest.php
│   └── IdeaSubmissionTest.php
└── Integration/
    └── CompleteWorkflowTest.php
```

---

## 📝 HELPER FUNCTIONS

**File:** `app/Helpers/hackathon.php`

```php
<?php

use App\Models\Hackathon;

if (!function_exists('currentHackathonId')) {
    function currentHackathonId(): int
    {
        return Cache::remember('current_hackathon_id', 3600, function () {
            return Hackathon::where('status', 'active')->value('id') ?? 1;
        });
    }
}

if (!function_exists('isRegistrationOpen')) {
    function isRegistrationOpen(): bool
    {
        $hackathon = Hackathon::find(currentHackathonId());
        return $hackathon && 
               now()->between($hackathon->registration_start, $hackathon->registration_end);
    }
}
```

---

## ⚙️ CONFIGURATION

**File:** `config/hackathon.php`

```php
<?php

return [
    'teams' => [
        'max_members' => 5,
        'min_members' => 2,
        'max_per_track' => 20,
    ],
    
    'ideas' => [
        'max_files' => 8,
        'max_file_size' => 15 * 1024, // 15MB in KB
        'allowed_extensions' => ['pdf', 'ppt', 'pptx', 'doc', 'docx'],
        'min_score_to_approve' => 60,
    ],
    
    'workshops' => [
        'max_registrations_per_user' => 5,
        'reminder_hours_before' => 24,
    ],
    
    'cache' => [
        'ttl' => [
            'teams' => 3600,
            'statistics' => 600,
            'tracks' => 1800,
        ]
    ]
];
```

---

## 🔄 UPDATE YOUR EXISTING FILES

### Update TeamLeader Controllers:
Replace direct model access with services

### Update API Controllers:
Use repositories instead of Eloquent

### Update Models:
Add scopes and business methods

### Create Service Classes:
For each major feature

### Create Repository Classes:
For each model

---

## BENEFITS OF THIS ARCHITECTURE:

1. **Testable**: Each layer can be tested independently
2. **Maintainable**: Changes isolated to specific layers
3. **Scalable**: Easy to add new features
4. **Reusable**: Services can be used anywhere
5. **Clean**: Separation of concerns
6. **Professional**: Industry-standard structure

This architecture will make your hackathon system robust, maintainable, and professional-grade!
