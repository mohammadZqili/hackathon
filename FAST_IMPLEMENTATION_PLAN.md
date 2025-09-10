# ⚡ FAST IMPLEMENTATION PLAN - ALL ROLES IN 2 DAYS
## Leveraging Existing SystemAdmin Code (90% Complete)

---

## 📊 CURRENT SYSTEM STATUS

### ✅ What's Already Built:
- **SystemAdmin**: 90% complete (15 controllers, 14 page sections)
- **Models**: All created (Team, Idea, Workshop, Track, etc.)
- **Database**: Fully structured
- **Routes**: SystemAdmin routes complete
- **Vue Pages**: SystemAdmin has full CRUD pages

### ⚠️ What's Partially Built:
- **HackathonAdmin**: 6 page sections (needs scoping)
- **TrackSupervisor**: Basic structure
- **TeamLeader/TeamMember**: Basic structure

### ❌ What's Missing:
- Role-based filtering in controllers
- Shared services layer
- Unified pages
- Workshop Supervisor pages
- Visitor pages

---

## 🚀 FAST IMPLEMENTATION STRATEGY

### Core Approach: **"Wrap, Don't Rewrite"**
Instead of rewriting, we'll wrap existing SystemAdmin code with role-based filtering.

```
SystemAdmin Code (90% complete)
        ↓
Add Service Layer (filters by role)
        ↓
All 7 Roles Working!
```

---

## 📋 DAY 1: MORNING (4 HOURS)
### Task 1: Create Service Layer Wrapper (2 hours)

#### Step 1.1: Create Base Service (30 min)
```bash
# Create services directory and base service
mkdir -p app/Services
```

Create `app/Services/BaseService.php`:
```php
<?php
namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class BaseService
{
    protected function scopeByRole(Builder $query, User $user, string $model = ''): Builder
    {
        return match($user->role) {
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
        return match($model) {
            'Team', 'Idea', 'Workshop', 'Track' => $query->where('edition_id', $user->edition_id),
            'User' => $query->whereHas('teams', fn($q) => $q->where('edition_id', $user->edition_id)),
            default => $query
        };
    }
    
    private function scopeForTrackSupervisor($query, $user, $model)
    {
        return match($model) {
            'Team' => $query->whereIn('track_id', $user->supervised_track_ids ?? []),
            'Idea' => $query->whereIn('track_id', $user->supervised_track_ids ?? []),
            default => $query->whereRaw('1 = 0')
        };
    }
    
    private function scopeForWorkshopSupervisor($query, $user, $model)
    {
        return match($model) {
            'Workshop' => $query->whereIn('id', $user->supervised_workshop_ids ?? []),
            'WorkshopRegistration' => $query->whereIn('workshop_id', $user->supervised_workshop_ids ?? []),
            default => $query->whereRaw('1 = 0')
        };
    }
    
    private function scopeForTeamMember($query, $user, $model)
    {
        return match($model) {
            'Team' => $query->where('id', $user->team_id),
            'Idea' => $query->where('team_id', $user->team_id),
            'TeamMember' => $query->where('team_id', $user->team_id),
            default => $query->whereRaw('1 = 0')
        };
    }
}
```

#### Step 1.2: Create Specific Services (1.5 hours)

Create `app/Services/TeamService.php`:
```php
<?php
namespace App\Services;

use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;

class TeamService extends BaseService
{
    public function getTeamsForUser(Request $request, User $user)
    {
        $query = Team::with(['leader', 'members', 'idea', 'edition']);
        
        // Apply role-based filtering
        $query = $this->scopeByRole($query, $user, 'Team');
        
        // Apply search if exists
        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }
        
        return $query->latest()->paginate(15);
    }
    
    public function getTeamPermissions(User $user): array
    {
        return match($user->role) {
            'system_admin' => [
                'canCreate' => true,
                'canEdit' => true,
                'canDelete' => true,
                'canExport' => true,
                'canViewAll' => true
            ],
            'hackathon_admin' => [
                'canCreate' => false,
                'canEdit' => true,
                'canDelete' => false,
                'canExport' => true,
                'canViewAll' => true
            ],
            'track_supervisor' => [
                'canCreate' => false,
                'canEdit' => false,
                'canDelete' => false,
                'canExport' => false,
                'canViewAll' => true
            ],
            'team_leader' => [
                'canCreate' => !$user->team_id,
                'canEdit' => true,
                'canDelete' => false,
                'canExport' => false,
                'canViewAll' => false
            ],
            default => [
                'canCreate' => false,
                'canEdit' => false,
                'canDelete' => false,
                'canExport' => false,
                'canViewAll' => false
            ]
        };
    }
}
```

Create similar services for:
- `IdeaService.php`
- `WorkshopService.php`
- `DashboardService.php`
- `UserService.php`

---

## 📋 DAY 1: AFTERNOON (4 HOURS)
### Task 2: Create Shared Controller Wrapper (2 hours)

#### Step 2.1: Create Base Controller
Create `app/Http/Controllers/Shared/BaseController.php`:
```php
<?php
namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

abstract class BaseController extends Controller
{
    protected function renderPage(string $page, array $data = [])
    {
        $user = auth()->user();
        
        // Add role and permissions to every page
        $data['userRole'] = $user->role;
        $data['permissions'] = $this->getPermissions();
        
        // Use Shared pages instead of role-specific
        return Inertia::render("Shared/{$page}", $data);
    }
    
    abstract protected function getPermissions(): array;
}
```

#### Step 2.2: Wrap Existing Controllers (1.5 hours)
Create `app/Http/Controllers/Shared/TeamController.php`:
```php
<?php
namespace App\Http\Controllers\Shared;

use App\Services\TeamService;
use App\Models\Team;
use Illuminate\Http\Request;

class TeamController extends BaseController
{
    public function __construct(
        private TeamService $teamService
    ) {}
    
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Reuse SystemAdmin controller logic with role filtering
        $teams = $this->teamService->getTeamsForUser($request, $user);
        
        return $this->renderPage('Teams/Index', [
            'teams' => $teams,
            'filters' => $request->all()
        ]);
    }
    
    public function create()
    {
        // Check if user can create
        if (!$this->getPermissions()['canCreate']) {
            abort(403);
        }
        
        return $this->renderPage('Teams/Create');
    }
    
    public function store(Request $request)
    {
        // Reuse SystemAdmin validation
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            // ... same validation
        ]);
        
        // Add role-specific logic
        if (auth()->user()->role === 'hackathon_admin') {
            $validated['edition_id'] = auth()->user()->edition_id;
        }
        
        $team = Team::create($validated);
        
        return redirect()->route('teams.index');
    }
    
    protected function getPermissions(): array
    {
        return $this->teamService->getTeamPermissions(auth()->user());
    }
}
```

---

## 📋 DAY 2: MORNING (4 HOURS)
### Task 3: Copy & Modify Vue Pages (2 hours)

#### Step 3.1: Copy SystemAdmin Pages to Shared (30 min)
```bash
# Copy all SystemAdmin pages to Shared
cp -r resources/js/Pages/SystemAdmin/* resources/js/Pages/Shared/

# Remove role-specific references
find resources/js/Pages/Shared -type f -name "*.vue" -exec sed -i 's/SystemAdmin/Shared/g' {} \;
```

#### Step 3.2: Add Role-Based Logic to Pages (1.5 hours)
Update `resources/js/Pages/Shared/Teams/Index.vue`:
```vue
<template>
    <Head :title="pageTitle" />
    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Dynamic Title based on role -->
                <div class="mb-6">
                    <h2 class="text-2xl font-bold">{{ pageTitle }}</h2>
                    <p class="text-gray-600">{{ pageSubtitle }}</p>
                </div>
                
                <!-- Action Buttons (role-based) -->
                <div class="mb-4 flex justify-between">
                    <div>
                        <input 
                            v-model="search"
                            @input="searchTeams"
                            type="search"
                            placeholder="Search teams..."
                            class="rounded-lg border-gray-300"
                        />
                    </div>
                    <div>
                        <Link 
                            v-if="permissions.canCreate"
                            :href="route('teams.create')"
                            class="btn btn-primary"
                        >
                            Create Team
                        </Link>
                        <button
                            v-if="permissions.canExport"
                            @click="exportTeams"
                            class="btn btn-secondary ml-2"
                        >
                            Export
                        </button>
                    </div>
                </div>
                
                <!-- Teams Table (same for all, different data) -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <table class="min-w-full">
                        <thead>
                            <tr>
                                <th>Team Name</th>
                                <th v-if="showEditionColumn">Edition</th>
                                <th>Leader</th>
                                <th>Members</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="team in teams.data" :key="team.id">
                                <td>{{ team.name }}</td>
                                <td v-if="showEditionColumn">{{ team.edition?.name }}</td>
                                <td>{{ team.leader?.name }}</td>
                                <td>{{ team.members?.length || 0 }}</td>
                                <td>
                                    <span :class="getStatusClass(team.status)">
                                        {{ team.status }}
                                    </span>
                                </td>
                                <td>
                                    <Link :href="route('teams.show', team.id)">View</Link>
                                    <Link 
                                        v-if="canEdit(team)"
                                        :href="route('teams.edit', team.id)"
                                        class="ml-2"
                                    >
                                        Edit
                                    </Link>
                                    <button
                                        v-if="canReview(team)"
                                        @click="reviewIdea(team.idea)"
                                        class="ml-2 text-orange-600"
                                    >
                                        Review Idea
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <Pagination :links="teams.links" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const props = defineProps({
    teams: Object,
    permissions: Object,
    userRole: String
})

// Dynamic page configuration based on role
const pageTitle = computed(() => ({
    'system_admin': 'All Teams',
    'hackathon_admin': 'Edition Teams',
    'track_supervisor': 'Track Teams',
    'team_leader': 'My Team',
    'team_member': 'My Team'
}[props.userRole] || 'Teams'))

const pageSubtitle = computed(() => ({
    'system_admin': 'Manage all teams across all editions',
    'hackathon_admin': 'Manage teams in current edition',
    'track_supervisor': 'View teams in your tracks',
    'team_leader': 'Manage your team',
    'team_member': 'View your team information'
}[props.userRole] || ''))

const showEditionColumn = computed(() => 
    ['system_admin'].includes(props.userRole)
)

const canEdit = (team) => {
    if (props.userRole === 'team_leader') {
        return team.leader_id === usePage().props.auth.user.id
    }
    return props.permissions.canEdit
}

const canReview = (team) => {
    return props.userRole === 'track_supervisor' && team.idea
}
</script>
```

---

## 📋 DAY 2: AFTERNOON (4 HOURS)
### Task 4: Update Routes & Middleware (2 hours)

#### Step 4.1: Create Unified Routes (1 hour)
Update `routes/web.php`:
```php
<?php
use App\Http\Controllers\Shared\TeamController;
use App\Http\Controllers\Shared\IdeaController;
use App\Http\Controllers\Shared\WorkshopController;
use App\Http\Controllers\Shared\DashboardController;

// Remove all role-specific routes and use shared ones
Route::middleware(['auth'])->group(function () {
    // Dashboard - works for ALL roles
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Teams - works for ALL roles
    Route::resource('teams', TeamController::class);
    Route::post('teams/{team}/invite', [TeamController::class, 'inviteMember'])->name('teams.invite');
    Route::delete('teams/{team}/members/{user}', [TeamController::class, 'removeMember'])->name('teams.remove-member');
    
    // Ideas - works for ALL roles
    Route::resource('ideas', IdeaController::class);
    Route::post('ideas/{idea}/review', [IdeaController::class, 'submitReview'])->name('ideas.review');
    Route::post('ideas/{idea}/score', [IdeaController::class, 'updateScore'])->name('ideas.score');
    
    // Workshops - works for ALL roles
    Route::resource('workshops', WorkshopController::class);
    Route::post('workshops/{workshop}/register', [WorkshopController::class, 'register'])->name('workshops.register');
    Route::post('workshops/{workshop}/checkin', [WorkshopController::class, 'checkIn'])->name('workshops.checkin');
    
    // News - public for all
    Route::get('news', [NewsController::class, 'index'])->name('news.index');
    Route::get('news/{news}', [NewsController::class, 'show'])->name('news.show');
    
    // Reports - role-based access
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::post('reports/export', [ReportController::class, 'export'])->name('reports.export');
});

// Remove old routes/hackathon.php - not needed anymore!
```

#### Step 4.2: Add Role Middleware (1 hour)
Create `app/Http/Middleware/LoadUserRole.php`:
```php
<?php
namespace App\Http\Middleware;

use Closure;
use Inertia\Inertia;

class LoadUserRole
{
    public function handle($request, Closure $next)
    {
        if (auth()->check()) {
            $user = auth()->user();
            
            // Load role-specific relationships
            match($user->role) {
                'track_supervisor' => $user->load('supervisedTracks'),
                'workshop_supervisor' => $user->load('supervisedWorkshops'),
                'team_leader', 'team_member' => $user->load('team'),
                default => null
            };
            
            // Share with all Inertia responses
            Inertia::share([
                'auth.user' => $user,
                'auth.role' => $user->role,
            ]);
        }
        
        return $next($request);
    }
}
```

Register in `app/Http/Kernel.php`:
```php
protected $middlewareGroups = [
    'web' => [
        // ... existing middleware
        \App\Http\Middleware\LoadUserRole::class,
    ],
];
```

---

## 🚀 RAPID TESTING & DEPLOYMENT (2 hours)

### Step 5.1: Create Test Users (30 min)
```php
// database/seeders/RoleTestSeeder.php
use App\Models\User;
use App\Models\Team;
use App\Models\Track;

class RoleTestSeeder extends Seeder
{
    public function run()
    {
        // System Admin
        User::create([
            'name' => 'System Admin',
            'email' => 'system@test.com',
            'password' => bcrypt('password'),
            'role' => 'system_admin'
        ]);
        
        // Hackathon Admin
        User::create([
            'name' => 'Hackathon Admin',
            'email' => 'hackathon@test.com',
            'password' => bcrypt('password'),
            'role' => 'hackathon_admin',
            'edition_id' => 1
        ]);
        
        // Track Supervisor
        $supervisor = User::create([
            'name' => 'Track Supervisor',
            'email' => 'track@test.com',
            'password' => bcrypt('password'),
            'role' => 'track_supervisor'
        ]);
        $supervisor->supervisedTracks()->attach([1, 2]);
        
        // Team Leader
        $leader = User::create([
            'name' => 'Team Leader',
            'email' => 'leader@test.com',
            'password' => bcrypt('password'),
            'role' => 'team_leader'
        ]);
        
        $team = Team::create([
            'name' => 'Test Team',
            'leader_id' => $leader->id,
            'edition_id' => 1,
            'track_id' => 1
        ]);
        
        $leader->update(['team_id' => $team->id]);
    }
}
```

Run: `php artisan db:seed --class=RoleTestSeeder`

### Step 5.2: Test Each Role (1 hour)
```bash
# Quick test script
php artisan tinker

# Test each role can see correct data
$systemAdmin = User::where('email', 'system@test.com')->first();
$teams = (new TeamService)->getTeamsForUser(request(), $systemAdmin);
echo "System Admin sees: " . $teams->count() . " teams\n";

$hackathonAdmin = User::where('email', 'hackathon@test.com')->first();
$teams = (new TeamService)->getTeamsForUser(request(), $hackathonAdmin);
echo "Hackathon Admin sees: " . $teams->count() . " teams\n";

# ... test other roles
```

### Step 5.3: Quick Fixes & Deploy (30 min)
```bash
# Clear all caches
php artisan optimize:clear

# Rebuild assets
npm run build

# Test in browser
# Login as each role and verify correct data appears
```

---

## ⏱️ TIMELINE SUMMARY

### DAY 1 (8 hours)
- **Morning (4h)**: Create Service Layer
  - BaseService.php ✅
  - TeamService.php ✅
  - IdeaService.php ✅
  - WorkshopService.php ✅
  - DashboardService.php ✅

- **Afternoon (4h)**: Create Shared Controllers
  - BaseController.php ✅
  - TeamController.php ✅
  - IdeaController.php ✅
  - WorkshopController.php ✅
  - DashboardController.php ✅

### DAY 2 (8 hours)
- **Morning (4h)**: Modify Vue Pages
  - Copy SystemAdmin to Shared ✅
  - Add role-based logic ✅
  - Test UI for each role ✅

- **Afternoon (4h)**: Routes & Testing
  - Unified routes ✅
  - Role middleware ✅
  - Test all roles ✅
  - Deploy ✅

---

## 🎯 RESULT

After 2 days, you'll have:
- ✅ All 7 roles working
- ✅ Using same codebase
- ✅ 90% code reuse from SystemAdmin
- ✅ Easy to maintain
- ✅ Consistent behavior

## 🔥 QUICK START COMMANDS

```bash
# Day 1 Morning
mkdir -p app/Services
touch app/Services/BaseService.php
touch app/Services/TeamService.php
touch app/Services/IdeaService.php
touch app/Services/WorkshopService.php

# Day 1 Afternoon
mkdir -p app/Http/Controllers/Shared
touch app/Http/Controllers/Shared/BaseController.php
touch app/Http/Controllers/Shared/TeamController.php

# Day 2 Morning
cp -r resources/js/Pages/SystemAdmin resources/js/Pages/Shared
find resources/js/Pages/Shared -name "*.vue" -exec sed -i 's/system-admin/shared/g' {} \;

# Day 2 Afternoon
php artisan make:middleware LoadUserRole
php artisan make:seeder RoleTestSeeder
php artisan db:seed --class=RoleTestSeeder

# Test
php artisan serve
npm run dev
```

---

## 💡 KEY SUCCESS FACTORS

1. **Don't Rewrite** - Wrap existing code
2. **Service Layer** - Handles all role logic
3. **Shared Pages** - One page, multiple roles
4. **Unified Routes** - Single route file
5. **Test Early** - Test each role as you go

This plan leverages your existing SystemAdmin code and can have all 7 roles working in just 2 days!
