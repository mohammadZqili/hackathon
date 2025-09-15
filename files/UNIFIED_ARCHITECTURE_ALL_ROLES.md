# 🏗️ UNIFIED ARCHITECTURE FOR ALL 7 ROLES
## One Codebase, Seven Roles - Complete Implementation Plan

---

## 📊 ROLE ACCESS PATTERNS

All roles access the SAME pages but see different data based on their scope:

| Role | Data Scope | Example: Teams Page | Example: Ideas Page |
|------|------------|-------------------|-------------------|
| **System Admin** | ALL data | See ALL teams | See ALL ideas |
| **Hackathon Admin** | Edition-specific | Teams in their edition | Ideas in their edition |
| **Track Supervisor** | Track-specific | Teams in their track(s) | Ideas in their track(s) to review |
| **Workshop Supervisor** | Workshop-specific | No access | No access |
| **Team Leader** | Own team only | Own team details | Own team's idea |
| **Team Member** | Own team only | Own team (read-only) | Own team's idea (read-only) |
| **Visitor** | Public only | No access | No access |

---

## 🎨 COMPLETE ARCHITECTURE

```
                    ONE SHARED CODEBASE
                            │
        ┌───────────────────┴───────────────────┐
        │           Base Controllers             │
        │         (Common CRUD Logic)            │
        └───────────────────┬───────────────────┘
                            │
        ┌───────────────────┴───────────────────┐
        │            Service Layer               │
        │      (Role-Based Business Logic)       │
        └───────────────────┬───────────────────┘
                            │
        ┌───────────────────┴───────────────────┐
        │           Repository Layer             │
        │        (Role-Based Data Access)        │
        └───────────────────┬───────────────────┘
                            │
                    ┌───────┴───────┐
                    │   Database     │
                    │  (Shared Tables)│
                    └────────────────┘
```

---

## 📁 FILE STRUCTURE (SHARED BY ALL)

```
app/
├── Http/
│   └── Controllers/
│       ├── Base/                    # Base controllers for ALL roles
│       │   ├── BaseController.php
│       │   ├── BaseTeamController.php
│       │   ├── BaseIdeaController.php
│       │   ├── BaseWorkshopController.php
│       │   └── BaseDashboardController.php
│       │
│       └── RoleControllers/         # Minimal role-specific overrides
│           ├── SystemAdminController.php    # extends BaseController
│           ├── HackathonAdminController.php # extends BaseController
│           ├── TrackSupervisorController.php # extends BaseController
│           └── TeamLeaderController.php     # extends BaseController
│
├── Services/
│   ├── TeamService.php              # Handles ALL role logic
│   ├── IdeaService.php              # Handles ALL role logic
│   ├── WorkshopService.php          # Handles ALL role logic
│   └── DashboardService.php         # Handles ALL role logic
│
└── Repositories/
    ├── TeamRepository.php            # Role-based queries
    ├── IdeaRepository.php            # Role-based queries
    └── WorkshopRepository.php        # Role-based queries

resources/js/
└── Pages/
    └── Shared/                       # ONE set of pages for ALL roles
        ├── Dashboard.vue
        ├── Teams/
        │   ├── Index.vue
        │   ├── Show.vue
        │   └── Edit.vue
        ├── Ideas/
        │   ├── Index.vue
        │   ├── Show.vue
        │   ├── Review.vue           # Only visible to Track Supervisor
        │   └── Submit.vue            # Only visible to Team Leader
        └── Workshops/
            ├── Index.vue
            ├── Show.vue
            └── CheckIn.vue           # Only visible to Workshop Supervisor
```

---

## 🔧 IMPLEMENTATION: BASE CONTROLLER

```php
// app/Http/Controllers/Base/BaseController.php
namespace App\Http\Controllers\Base;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

abstract class BaseController extends Controller
{
    protected string $resource;
    protected $service;
    
    public function index(Request $request)
    {
        // Get data based on user's role automatically
        $data = $this->service->getForUser(
            auth()->user(),
            $request->all()
        );
        
        return Inertia::render("Shared/{$this->resource}/Index", [
            'data' => $data,
            'permissions' => $this->getPermissions(),
            'role' => auth()->user()->role,
            'filters' => $request->all()
        ]);
    }
    
    public function show($id)
    {
        $item = $this->service->findForUser($id, auth()->user());
        
        return Inertia::render("Shared/{$this->resource}/Show", [
            'item' => $item,
            'permissions' => $this->getPermissions($item),
            'role' => auth()->user()->role
        ]);
    }
    
    public function create()
    {
        // Check if user can create
        if (!$this->canCreate()) {
            abort(403);
        }
        
        return Inertia::render("Shared/{$this->resource}/Create", [
            'formConfig' => $this->getFormConfig(),
            'role' => auth()->user()->role
        ]);
    }
    
    abstract protected function getPermissions($item = null): array;
    abstract protected function canCreate(): bool;
    abstract protected function getFormConfig(): array;
}
```

---

## 🎭 ROLE-SPECIFIC CONTROLLERS (Minimal Overrides)

```php
// app/Http/Controllers/RoleControllers/SystemAdminController.php
namespace App\Http\Controllers\RoleControllers;

use App\Http\Controllers\Base\BaseController;

class SystemAdminController extends BaseController
{
    protected function getPermissions($item = null): array
    {
        return [
            'canView' => true,
            'canCreate' => true,
            'canEdit' => true,
            'canDelete' => true,
            'canExport' => true,
            'showAllEditions' => true
        ];
    }
    
    protected function canCreate(): bool
    {
        return true; // System Admin can create everything
    }
    
    protected function getFormConfig(): array
    {
        return [
            'showAdvancedOptions' => true,
            'showEditionSelector' => true,
            'showSystemSettings' => true
        ];
    }
}

// app/Http/Controllers/RoleControllers/TrackSupervisorController.php
namespace App\Http\Controllers\RoleControllers;

use App\Http\Controllers\Base\BaseController;

class TrackSupervisorController extends BaseController
{
    protected function getPermissions($item = null): array
    {
        return [
            'canView' => true,
            'canCreate' => false,
            'canEdit' => false,
            'canDelete' => false,
            'canReview' => true,  // Unique to Track Supervisor
            'canScore' => true,   // Unique to Track Supervisor
            'showTrackFilter' => true
        ];
    }
    
    protected function canCreate(): bool
    {
        return false; // Track Supervisors cannot create teams/ideas
    }
    
    protected function getFormConfig(): array
    {
        return [
            'showReviewSection' => true,
            'showScoringCriteria' => true,
            'showFeedbackForm' => true
        ];
    }
}
```

---

## 💼 SERVICE LAYER (Handles ALL Roles)

```php
// app/Services/TeamService.php
namespace App\Services;

use App\Models\Team;
use App\Models\User;
use App\Repositories\TeamRepository;

class TeamService
{
    public function __construct(
        private TeamRepository $repository
    ) {}
    
    public function getForUser(User $user, array $filters = [])
    {
        // Apply role-based filtering
        $query = $this->repository->scopeForUser($user);
        
        // Apply common filters
        if (!empty($filters['search'])) {
            $query->where('name', 'like', "%{$filters['search']}%");
        }
        
        // Role-specific data loading
        $query = $this->loadRoleRelations($query, $user);
        
        // Role-specific transformations
        $data = $query->paginate(15);
        
        return $this->transformForRole($data, $user);
    }
    
    private function loadRoleRelations($query, User $user)
    {
        return match($user->role) {
            'system_admin' => $query->with(['edition', 'leader', 'members', 'idea']),
            'hackathon_admin' => $query->with(['track', 'leader', 'members', 'idea']),
            'track_supervisor' => $query->with(['idea.reviews', 'leader', 'members']),
            'team_leader', 'team_member' => $query->with(['members', 'idea.feedback']),
            default => $query
        };
    }
    
    private function transformForRole($data, User $user)
    {
        // Add role-specific computed fields
        return $data->through(function ($team) use ($user) {
            return match($user->role) {
                'system_admin' => $this->addSystemAdminData($team),
                'hackathon_admin' => $this->addHackathonAdminData($team),
                'track_supervisor' => $this->addTrackSupervisorData($team),
                'team_leader' => $this->addTeamLeaderData($team),
                default => $team
            };
        });
    }
    
    private function addTrackSupervisorData($team)
    {
        $team->needs_review = $team->idea && $team->idea->status === 'submitted';
        $team->review_deadline = $team->idea?->review_deadline;
        $team->last_feedback = $team->idea?->reviews()->latest()->first();
        $team->can_review = $team->idea && !$team->idea->reviewed_by_me;
        
        return $team;
    }
}
```

---

## 📊 REPOSITORY LAYER (Role-Based Queries)

```php
// app/Repositories/TeamRepository.php
namespace App\Repositories;

use App\Models\Team;
use App\Models\User;

class TeamRepository
{
    public function scopeForUser(User $user)
    {
        $query = Team::query();
        
        return match($user->role) {
            'system_admin' => $query, // No filtering
            
            'hackathon_admin' => $query->where('edition_id', $user->edition_id),
            
            'track_supervisor' => $query->whereHas('idea', function ($q) use ($user) {
                $q->whereIn('track_id', $user->supervised_track_ids);
            }),
            
            'workshop_supervisor' => $query->whereRaw('1 = 0'), // No access to teams
            
            'team_leader', 'team_member' => $query->where('id', $user->team_id),
            
            'visitor' => $query->whereRaw('1 = 0'), // No access
            
            default => $query->whereRaw('1 = 0')
        };
    }
}

// app/Repositories/IdeaRepository.php
namespace App\Repositories;

use App\Models\Idea;
use App\Models\User;

class IdeaRepository
{
    public function scopeForUser(User $user)
    {
        $query = Idea::query();
        
        return match($user->role) {
            'system_admin' => $query, // All ideas
            
            'hackathon_admin' => $query->whereHas('team', function ($q) use ($user) {
                $q->where('edition_id', $user->edition_id);
            }),
            
            'track_supervisor' => $query->whereIn('track_id', $user->supervised_track_ids),
            
            'workshop_supervisor' => $query->whereRaw('1 = 0'), // No access
            
            'team_leader', 'team_member' => $query->where('team_id', $user->team_id),
            
            'visitor' => $query->whereRaw('1 = 0'), // No access
            
            default => $query->whereRaw('1 = 0')
        };
    }
}
```

---

## 🎨 SHARED VUE PAGE (Used by ALL Roles)

```vue
<!-- resources/js/Pages/Shared/Teams/Index.vue -->
<template>
    <Head :title="pageConfig.title" />
    <AuthenticatedLayout>
        <div class="container mx-auto px-4 py-8">
            <!-- Dynamic Page Header based on role -->
            <PageHeader 
                :title="pageConfig.title"
                :subtitle="pageConfig.subtitle"
                :actions="pageConfig.actions"
            />
            
            <!-- Dynamic Filters based on role -->
            <FilterBar 
                v-if="pageConfig.showFilters"
                :filters="availableFilters"
                v-model="filters"
                @apply="applyFilters"
            />
            
            <!-- Data Display (same for all, different data) -->
            <DataTable
                :columns="pageConfig.columns"
                :data="data"
                :actions="pageConfig.rowActions"
                @action="handleAction"
            />
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { useRoleConfig } from '@/Composables/useRoleConfig'

const props = defineProps({
    data: Object,
    permissions: Object,
    role: String,
    filters: Object
})

// Get role-specific configuration
const pageConfig = computed(() => {
    const configs = {
        'system_admin': {
            title: 'All Teams',
            subtitle: 'Manage all teams across all editions',
            showFilters: true,
            columns: [
                { key: 'id', label: 'ID' },
                { key: 'name', label: 'Team' },
                { key: 'edition.name', label: 'Edition' },
                { key: 'track.name', label: 'Track' },
                { key: 'leader.name', label: 'Leader' },
                { key: 'status', label: 'Status' }
            ],
            actions: ['create', 'export'],
            rowActions: ['view', 'edit', 'delete']
        },
        
        'hackathon_admin': {
            title: 'Edition Teams',
            subtitle: 'Manage teams in current edition',
            showFilters: true,
            columns: [
                { key: 'name', label: 'Team' },
                { key: 'track.name', label: 'Track' },
                { key: 'leader.name', label: 'Leader' },
                { key: 'members_count', label: 'Members' },
                { key: 'idea.status', label: 'Idea Status' }
            ],
            actions: ['export'],
            rowActions: ['view', 'edit', 'message']
        },
        
        'track_supervisor': {
            title: 'Track Teams',
            subtitle: 'Teams in your supervised tracks',
            showFilters: false,
            columns: [
                { key: 'name', label: 'Team' },
                { key: 'leader.name', label: 'Leader' },
                { key: 'idea.status', label: 'Idea Status' },
                { key: 'needs_review', label: 'Needs Review', type: 'boolean' },
                { key: 'review_deadline', label: 'Deadline', type: 'date' }
            ],
            actions: [],
            rowActions: ['view', 'review']
        },
        
        'team_leader': {
            title: 'My Team',
            subtitle: 'Manage your team and members',
            showFilters: false,
            columns: [], // Different view - show team details instead of table
            actions: ['invite_member', 'submit_idea'],
            rowActions: []
        },
        
        'team_member': {
            title: 'My Team',
            subtitle: 'View team information',
            showFilters: false,
            columns: [], // Read-only team view
            actions: [],
            rowActions: []
        }
    }
    
    return configs[props.role] || {}
})

// Dynamic filters based on role
const availableFilters = computed(() => {
    const filters = {
        'system_admin': ['search', 'edition', 'track', 'status'],
        'hackathon_admin': ['search', 'track', 'status'],
        'track_supervisor': ['status', 'review_status'],
        'team_leader': [],
        'team_member': []
    }
    
    return filters[props.role] || []
})
</script>
```

---

## 🚦 SINGLE ROUTE FILE (Serves ALL Roles)

```php
// routes/web.php
use App\Http\Controllers\Base\BaseTeamController;
use App\Http\Controllers\Base\BaseIdeaController;
use App\Http\Controllers\Base\BaseWorkshopController;
use App\Http\Controllers\Base\BaseDashboardController;

Route::middleware(['auth'])->group(function () {
    // SAME routes work for ALL roles!
    Route::get('/dashboard', [BaseDashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('teams', BaseTeamController::class);
    Route::resource('ideas', BaseIdeaController::class);
    Route::resource('workshops', BaseWorkshopController::class);
    
    // Role-specific features as additional methods
    Route::post('ideas/{idea}/review', [BaseIdeaController::class, 'submitReview'])
        ->name('ideas.review.submit')
        ->middleware('can:review,idea');
    
    Route::post('workshops/{workshop}/checkin', [BaseWorkshopController::class, 'checkIn'])
        ->name('workshops.checkin')
        ->middleware('can:checkin,workshop');
});
```

---

## 📊 DATA FLOW EXAMPLE: Ideas Page

### When System Admin visits `/ideas`:
```
Request → BaseIdeaController@index
         → IdeaService::getForUser($systemAdmin)
         → IdeaRepository::scopeForUser($systemAdmin)
         → Returns ALL ideas
         → Render Shared/Ideas/Index.vue with all ideas
```

### When Hackathon Admin visits `/ideas`:
```
Request → BaseIdeaController@index
         → IdeaService::getForUser($hackathonAdmin)
         → IdeaRepository::scopeForUser($hackathonAdmin)
         → Returns ideas WHERE edition_id = admin's edition
         → Render Shared/Ideas/Index.vue with edition ideas
```

### When Track Supervisor visits `/ideas`:
```
Request → BaseIdeaController@index
         → IdeaService::getForUser($trackSupervisor)
         → IdeaRepository::scopeForUser($trackSupervisor)
         → Returns ideas WHERE track_id IN supervisor's tracks
         → Render Shared/Ideas/Index.vue with track ideas + review buttons
```

### When Team Leader visits `/ideas`:
```
Request → BaseIdeaController@index
         → IdeaService::getForUser($teamLeader)
         → IdeaRepository::scopeForUser($teamLeader)
         → Returns single idea WHERE team_id = leader's team
         → Render Shared/Ideas/Index.vue with their idea + submit button
```

---

## 🎯 IMPLEMENTATION TIMELINE

### Phase 1: Core Infrastructure (4 hours)
```bash
# 1. Create base controllers
mkdir app/Http/Controllers/Base
php artisan make:controller Base/BaseController
php artisan make:controller Base/BaseTeamController
php artisan make:controller Base/BaseIdeaController
php artisan make:controller Base/BaseWorkshopController

# 2. Create services
mkdir app/Services
php artisan make:class Services/TeamService
php artisan make:class Services/IdeaService
php artisan make:class Services/WorkshopService

# 3. Create repositories
mkdir app/Repositories
php artisan make:class Repositories/TeamRepository
php artisan make:class Repositories/IdeaRepository
```

### Phase 2: Migrate Existing Code (3 hours)
1. Move System Admin logic to services
2. Create shared Vue pages
3. Update routes to use base controllers
4. Test System Admin still works

### Phase 3: Add Other Roles (2 hours)
1. Add role logic to services
2. Add role scopes to repositories
3. Test each role sees correct data

---

## ✅ BENEFITS SUMMARY

### Code Statistics
- **Controllers**: 4 base controllers (not 28)
- **Services**: 4 services handle all 7 roles
- **Vue Pages**: 10 shared pages (not 70)
- **Routes**: 1 route file (not 7)
- **Total Code**: 80% reduction

### Maintenance Benefits
- Fix bugs in ONE place
- Add features once, works for all
- Consistent behavior guaranteed
- Easy to add new roles

### Performance Benefits
- Smaller JavaScript bundle
- Better caching
- Optimized queries

---

## 🚀 NEXT STEPS

1. **Start with Teams feature**
   - Create BaseTeamController
   - Create TeamService
   - Create TeamRepository
   - Test with all roles

2. **Apply pattern to Ideas**
   - Same structure
   - Add review logic for Track Supervisor

3. **Apply to remaining features**
   - Workshops
   - Dashboard
   - Reports

This architecture ensures **ALL 7 ROLES** share the same codebase with intelligent role-based filtering!
