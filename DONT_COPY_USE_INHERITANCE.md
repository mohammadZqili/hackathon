# ⚠️ STOP! DON'T COPY - USE INHERITANCE INSTEAD

## 🚨 **The Problem with Copying Controllers**

If we copy all 15 System Admin controllers to Hackathon Admin, we'll have:
- **2,250+ lines of duplicated code** (15 controllers × ~150 lines each)
- **30 duplicate Vue pages** (15 resources × 2 views average)
- **Maintenance nightmare** - fixing bugs in 2 places
- **Inconsistent behavior** - changes won't sync

---

## ✅ **BETTER SOLUTION: Inheritance & Services Pattern**

Instead of copying, we'll use **inheritance** and **services** to share 90% of the code:

### 📐 **Architecture Pattern**

```
                 BaseController (Shared Logic)
                        ↓ extends
        ┌───────────────┴───────────────┐
        │                               │
   SystemAdminController         HackathonAdminController
   (No filtering)                (Edition filtering)
```

---

## 🛠️ **IMPLEMENTATION PLAN**

### **Step 1: Create Base Controllers** (2 hours)

#### 1.1 Create Base Team Controller
```php
// app/Http/Controllers/Base/BaseTeamController.php
namespace App\Http\Controllers\Base;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Team;
use App\Services\TeamService;

abstract class BaseTeamController extends Controller
{
    protected TeamService $teamService;
    protected string $viewPrefix;
    protected string $routePrefix;
    
    public function __construct(TeamService $teamService)
    {
        $this->teamService = $teamService;
    }
    
    public function index(Request $request)
    {
        // Get teams based on user role
        $teams = $this->teamService->getTeamsForUser(
            auth()->user(),
            $request->all()
        );
        
        return Inertia::render($this->viewPrefix . '/Teams/Index', [
            'teams' => $teams,
            'filters' => $request->all(),
            'permissions' => $this->getPermissions()
        ]);
    }
    
    public function create()
    {
        $editions = $this->getAvailableEditions();
        
        return Inertia::render($this->viewPrefix . '/Teams/Create', [
            'editions' => $editions,
            'formConfig' => $this->getFormConfig()
        ]);
    }
    
    public function store(Request $request)
    {
        $validated = $this->validateTeam($request);
        
        $team = $this->teamService->createTeam(
            $validated,
            auth()->user()
        );
        
        return redirect()->route($this->routePrefix . '.teams.index')
            ->with('success', 'Team created successfully.');
    }
    
    // Abstract methods for role-specific logic
    abstract protected function getAvailableEditions();
    abstract protected function getPermissions();
    abstract protected function getFormConfig();
    
    // Common validation
    protected function validateTeam(Request $request)
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'edition_id' => 'required|exists:editions,id',
            'leader_id' => 'nullable|exists:users,id',
            'max_members' => 'nullable|integer|min:1|max:10',
        ]);
    }
}
```

#### 1.2 Extend for System Admin
```php
// app/Http/Controllers/SystemAdmin/TeamController.php
namespace App\Http\Controllers\SystemAdmin;

use App\Http\Controllers\Base\BaseTeamController;
use App\Models\Edition;

class TeamController extends BaseTeamController
{
    protected string $viewPrefix = 'SystemAdmin';
    protected string $routePrefix = 'system-admin';
    
    protected function getAvailableEditions()
    {
        // System Admin can see ALL editions
        return Edition::all();
    }
    
    protected function getPermissions()
    {
        return [
            'canCreate' => true,
            'canEdit' => true,
            'canDelete' => true,
            'canExport' => true,
            'canManageAllEditions' => true
        ];
    }
    
    protected function getFormConfig()
    {
        return [
            'showEditionSelector' => true,
            'showAdvancedOptions' => true,
            'maxMembers' => 10
        ];
    }
}
```

#### 1.3 Extend for Hackathon Admin
```php
// app/Http/Controllers/HackathonAdmin/TeamController.php
namespace App\Http\Controllers\HackathonAdmin;

use App\Http\Controllers\Base\BaseTeamController;
use App\Models\Edition;

class TeamController extends BaseTeamController
{
    protected string $viewPrefix = 'HackathonAdmin';
    protected string $routePrefix = 'hackathon-admin';
    
    protected function getAvailableEditions()
    {
        // Hackathon Admin can only see their assigned edition
        return Edition::where('id', auth()->user()->edition_id)->get();
    }
    
    protected function getPermissions()
    {
        return [
            'canCreate' => true,
            'canEdit' => true,
            'canDelete' => false, // Cannot delete teams
            'canExport' => true,
            'canManageAllEditions' => false
        ];
    }
    
    protected function getFormConfig()
    {
        return [
            'showEditionSelector' => false, // Fixed to their edition
            'showAdvancedOptions' => false,
            'maxMembers' => 5
        ];
    }
}
```

---

### **Step 2: Create Team Service** (1 hour)

```php
// app/Services/TeamService.php
namespace App\Services;

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Collection;

class TeamService
{
    public function getTeamsForUser(User $user, array $filters = []): mixed
    {
        $query = Team::with(['leader', 'members', 'idea', 'edition']);
        
        // Apply role-based filtering
        $query = $this->applyRoleScope($query, $user);
        
        // Apply search filters
        if (!empty($filters['search'])) {
            $query->where('name', 'like', "%{$filters['search']}%");
        }
        
        // Apply status filter
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        return $query->paginate(15);
    }
    
    private function applyRoleScope($query, User $user)
    {
        return match($user->role) {
            'system_admin' => $query, // No filtering
            'hackathon_admin' => $query->where('edition_id', $user->edition_id),
            'track_supervisor' => $query->whereIn('track_id', $user->supervised_track_ids),
            'team_leader', 'team_member' => $query->where('id', $user->team_id),
            default => $query->whereRaw('1 = 0') // No results
        };
    }
    
    public function createTeam(array $data, User $creator): Team
    {
        // Check permissions based on role
        if ($creator->role === 'hackathon_admin') {
            // Force edition to be the admin's edition
            $data['edition_id'] = $creator->edition_id;
        }
        
        $team = Team::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'edition_id' => $data['edition_id'],
            'leader_id' => $data['leader_id'] ?? null,
            'max_members' => $data['max_members'] ?? 5,
            'status' => 'active',
            'created_by' => $creator->id
        ]);
        
        // Add leader as member if specified
        if (!empty($data['leader_id'])) {
            $team->members()->attach($data['leader_id'], ['role' => 'leader']);
        }
        
        return $team;
    }
    
    public function updateTeam(Team $team, array $data, User $updater): Team
    {
        // Check if user can update this team
        $this->authorize('update', $team, $updater);
        
        // Hackathon Admin cannot change edition
        if ($updater->role === 'hackathon_admin') {
            unset($data['edition_id']);
        }
        
        $team->update($data);
        
        return $team;
    }
    
    private function authorize(string $action, Team $team, User $user): void
    {
        $canPerform = match($user->role) {
            'system_admin' => true,
            'hackathon_admin' => $team->edition_id === $user->edition_id,
            'team_leader' => $team->leader_id === $user->id && in_array($action, ['update']),
            default => false
        };
        
        if (!$canPerform) {
            throw new \Exception('Unauthorized action');
        }
    }
}
```

---

### **Step 3: Create Shared Vue Pages** (2 hours)

#### 3.1 Create Shared Team Index Page
```vue
<!-- resources/js/Pages/Shared/Teams/Index.vue -->
<template>
    <Head :title="pageTitle" />
    <AuthenticatedLayout>
        <div class="container mx-auto px-4 py-8">
            <!-- Page Header -->
            <div class="mb-6">
                <h1 class="text-3xl font-bold">{{ pageTitle }}</h1>
                <p class="text-gray-600">{{ pageSubtitle }}</p>
            </div>
            
            <!-- Action Bar -->
            <div class="flex justify-between mb-6">
                <!-- Search -->
                <div class="flex gap-4">
                    <input 
                        v-model="filters.search"
                        @input="debounceSearch"
                        type="text" 
                        placeholder="Search teams..."
                        class="px-4 py-2 border rounded-lg"
                    >
                    
                    <!-- Edition Filter (only for System Admin) -->
                    <select 
                        v-if="permissions.canManageAllEditions"
                        v-model="filters.edition_id"
                        @change="applyFilters"
                        class="px-4 py-2 border rounded-lg"
                    >
                        <option value="">All Editions</option>
                        <option v-for="edition in editions" :key="edition.id" :value="edition.id">
                            {{ edition.name }}
                        </option>
                    </select>
                </div>
                
                <!-- Actions -->
                <div class="flex gap-2">
                    <Link 
                        v-if="permissions.canCreate"
                        :href="route(`${rolePrefix}.teams.create`)"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700"
                    >
                        Create Team
                    </Link>
                    
                    <button 
                        v-if="permissions.canExport"
                        @click="exportTeams"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                    >
                        Export
                    </button>
                </div>
            </div>
            
            <!-- Teams Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Team Name
                            </th>
                            <th v-if="showEditionColumn" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Edition
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Leader
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Members
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="team in teams.data" :key="team.id">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ team.name }}
                                </div>
                            </td>
                            <td v-if="showEditionColumn" class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">
                                    {{ team.edition?.name }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ team.leader?.name || 'No leader' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">
                                    {{ team.members_count || 0 }} / {{ team.max_members }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="getStatusClass(team.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                    {{ team.status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex gap-2">
                                    <Link 
                                        :href="route(`${rolePrefix}.teams.show`, team.id)"
                                        class="text-indigo-600 hover:text-indigo-900"
                                    >
                                        View
                                    </Link>
                                    <Link 
                                        v-if="permissions.canEdit"
                                        :href="route(`${rolePrefix}.teams.edit`, team.id)"
                                        class="text-green-600 hover:text-green-900"
                                    >
                                        Edit
                                    </Link>
                                    <button 
                                        v-if="permissions.canDelete"
                                        @click="deleteTeam(team.id)"
                                        class="text-red-600 hover:text-red-900"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                <!-- Pagination -->
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    <Pagination :links="teams.links" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import { debounce } from 'lodash'

const props = defineProps({
    teams: Object,
    filters: Object,
    permissions: Object,
    editions: Array
})

// Determine role-based configuration
const userRole = computed(() => page.props.auth.user.role)
const rolePrefix = computed(() => {
    return userRole.value === 'system_admin' ? 'system-admin' : 'hackathon-admin'
})

const pageTitle = computed(() => {
    return userRole.value === 'system_admin' ? 'All Teams' : 'Edition Teams'
})

const pageSubtitle = computed(() => {
    return userRole.value === 'system_admin' 
        ? 'Manage all teams across all editions'
        : 'Manage teams in current edition'
})

const showEditionColumn = computed(() => {
    return props.permissions?.canManageAllEditions || false
})

// Methods
const debounceSearch = debounce(() => {
    router.get(route(`${rolePrefix.value}.teams.index`), filters.value, {
        preserveState: true,
        preserveScroll: true
    })
}, 300)

const exportTeams = () => {
    window.location.href = route(`${rolePrefix.value}.teams.export`)
}

const deleteTeam = (id) => {
    if (confirm('Are you sure you want to delete this team?')) {
        router.delete(route(`${rolePrefix.value}.teams.destroy`, id))
    }
}

const getStatusClass = (status) => {
    return {
        'active': 'bg-green-100 text-green-800',
        'inactive': 'bg-gray-100 text-gray-800',
        'disqualified': 'bg-red-100 text-red-800'
    }[status] || 'bg-gray-100 text-gray-800'
}
</script>
```

---

### **Step 4: Update Routes to Use Shared Logic** (30 min)

The routes stay the same, but now both System Admin and Hackathon Admin controllers inherit from the same base, so they share 90% of the code!

---

## 📊 **BENEFITS OF THIS APPROACH**

### Code Reduction
- **Before**: 30 files (15 controllers × 2 roles)
- **After**: 16 files (15 base + role overrides)
- **Shared Code**: 90%
- **Maintenance**: Fix bugs in ONE place

### How It Works
1. **Base Controller**: Contains all common logic
2. **Role Controllers**: Only override what's different
3. **Service Layer**: Handles business logic
4. **Shared Views**: One view serves all roles

---

## 🚀 **IMPLEMENTATION STEPS**

### Phase 1: Create Infrastructure (2 hours)
```bash
# Create base controllers directory
mkdir app/Http/Controllers/Base

# Create services directory
mkdir app/Services

# Create each base controller
php artisan make:controller Base/BaseTeamController
php artisan make:controller Base/BaseIdeaController
php artisan make:controller Base/BaseWorkshopController
# ... etc

# Create services
php artisan make:class Services/TeamService
php artisan make:class Services/IdeaService
php artisan make:class Services/WorkshopService
# ... etc
```

### Phase 2: Refactor Existing Controllers (3 hours)
1. Move common logic to base controllers
2. Update System Admin controllers to extend base
3. Update Hackathon Admin controllers to extend base
4. Test both roles work correctly

### Phase 3: Create Shared Views (2 hours)
1. Move System Admin views to Shared folder
2. Add role-based configuration
3. Update both roles to use shared views
4. Test UI works for both roles

---

## ✅ **FINAL RESULT**

Instead of duplicating 2,250+ lines of code, we:
- Share 90% of the code through inheritance
- Maintain single source of truth
- Easy to add new roles
- Consistent behavior across roles
- Clean, maintainable architecture

This is the **professional, scalable solution** that follows best practices!

Would you like me to start implementing this inheritance pattern, or would you still prefer to copy and duplicate all the code?
