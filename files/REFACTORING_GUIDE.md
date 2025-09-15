# Service-Repository Pattern Refactoring Guide

## Architecture Overview

This guide documents the complete refactoring of the GuacPanel codebase to follow a clean service-repository pattern with role-based access control.

## Architecture Layers

### 1. Repository Layer
**Purpose**: Data access and database operations only
**Location**: `app/Repositories/`

```php
class EntityRepository extends BaseRepository
{
    // Only database queries - NO business logic
    // NO permission checks
    // NO user role awareness
    
    public function getPaginatedWithFilters(array $filters, int $perPage = 15)
    public function findWithFullDetails(int $id)
    public function getStatistics(array $filters = [])
    public function getForExport(array $filters = [])
    public function hasDependencies(int $id)
}
```

### 2. Service Layer
**Purpose**: Business logic, permissions, and role-based filtering
**Location**: `app/Services/`

```php
class EntityService extends BaseService
{
    // ALL business logic
    // Role-based data filtering
    // Permission checks
    // Transaction management
    // Activity logging
    
    public function getPaginatedEntities(User $user, array $filters = [], int $perPage = 15)
    public function createEntity(array $data, User $user)
    public function updateEntity(int $id, array $data, User $user)
    public function deleteEntity(int $id, User $user)
    
    protected function buildRoleFilters(User $user, array $filters)
    protected function userCanAccessEntity(User $user, $entity)
}
```

### 3. Controller Layer
**Purpose**: HTTP concerns only (validation, routing, responses)
**Location**: `app/Http/Controllers/`

```php
class EntityController extends Controller
{
    // ONLY handles HTTP layer
    // Request validation
    // Delegates to service
    // Returns responses
    
    protected EntityService $entityService;
    
    public function index(Request $request)
    {
        $data = $this->entityService->getPaginatedEntities(
            auth()->user(),
            $request->only(['filter1', 'filter2']),
            $request->get('per_page', 15)
        );
        
        return Inertia::render('Role/Entity/Index', $data);
    }
}
```

## Role-Based Access Control

### User Types and Permissions

```php
// In Service Layer
switch ($user->user_type) {
    case 'system_admin':
        // Can access everything
        // No filters applied
        break;
        
    case 'hackathon_admin':
        // Limited to their edition
        $filters['edition_id'] = $user->edition_id;
        break;
        
    case 'track_supervisor':
        // Limited to their tracks
        $filters['track_ids'] = $this->getUserTrackIds($user);
        break;
        
    case 'team_leader':
        // Limited to their team
        $filters['team_id'] = $user->team_id;
        break;
}
```

## Complete Refactoring Example: Team Management

### 1. TeamRepository
```php
<?php
namespace App\Repositories;

use App\Models\Team;
use Illuminate\Pagination\LengthAwarePaginator;

class TeamRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new Team());
    }
    
    public function getPaginatedWithFilters(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->query()
            ->with(['leader', 'members.user', 'track', 'edition'])
            ->withCount(['members', 'ideas']);
            
        // Apply filters
        if (!empty($filters['edition_id'])) {
            $query->where('edition_id', $filters['edition_id']);
        }
        
        if (!empty($filters['track_id'])) {
            $query->where('track_id', $filters['track_id']);
        }
        
        if (!empty($filters['team_ids'])) {
            $query->whereIn('id', $filters['team_ids']);
        }
        
        return $query->latest()->paginate($perPage);
    }
}
```

### 2. TeamService
```php
<?php
namespace App\Services;

use App\Models\User;
use App\Repositories\TeamRepository;

class TeamService extends BaseService
{
    protected TeamRepository $teamRepository;
    
    public function getPaginatedTeams(User $user, array $filters = [], int $perPage = 15): array
    {
        // Apply role-based filters
        $roleFilters = $this->buildRoleFilters($user, $filters);
        
        // Get data from repository
        $teams = $this->teamRepository->getPaginatedWithFilters($roleFilters, $perPage);
        $statistics = $this->teamRepository->getStatistics($roleFilters);
        
        return [
            'teams' => $teams,
            'statistics' => $statistics,
            'filters' => $filters
        ];
    }
    
    protected function buildRoleFilters(User $user, array $filters): array
    {
        $roleFilters = $filters;
        
        switch ($user->user_type) {
            case 'hackathon_admin':
                if ($user->edition_id) {
                    $roleFilters['edition_id'] = $user->edition_id;
                }
                break;
                
            case 'track_supervisor':
                $trackIds = DB::table('track_supervisors')
                    ->where('user_id', $user->id)
                    ->pluck('track_id')
                    ->toArray();
                $roleFilters['track_id'] = $trackIds;
                break;
        }
        
        return $roleFilters;
    }
}
```

### 3. SystemAdmin/TeamController
```php
<?php
namespace App\Http\Controllers\SystemAdmin;

use App\Http\Controllers\Controller;
use App\Services\TeamService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TeamController extends Controller
{
    protected TeamService $teamService;
    
    public function __construct(TeamService $teamService)
    {
        $this->teamService = $teamService;
    }
    
    public function index(Request $request)
    {
        $data = $this->teamService->getPaginatedTeams(
            auth()->user(),
            $request->only(['edition_id', 'track_id', 'search']),
            $request->get('per_page', 15)
        );
        
        return Inertia::render('SystemAdmin/Teams/Index', $data);
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'edition_id' => 'required|exists:editions,id',
            // ... other validation
        ]);
        
        try {
            $result = $this->teamService->createTeam($validated, auth()->user());
            return redirect()->route('system-admin.teams.index')
                ->with('success', $result['message']);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
```

## Migration Checklist

For each controller to refactor:

- [ ] Create/Update Repository
  - [ ] Extend BaseRepository
  - [ ] Add data access methods
  - [ ] No business logic
  
- [ ] Create/Update Service
  - [ ] Extend BaseService
  - [ ] Add business logic methods
  - [ ] Implement role-based filtering
  - [ ] Add permission checks
  
- [ ] Refactor Controller
  - [ ] Inject service dependency
  - [ ] Move logic to service
  - [ ] Keep only HTTP concerns
  
- [ ] Test
  - [ ] System Admin access
  - [ ] Hackathon Admin access
  - [ ] Track Supervisor access
  - [ ] Permission boundaries

## Benefits of This Architecture

1. **Separation of Concerns**: Each layer has a single responsibility
2. **Reusability**: Same service works for all roles
3. **Testability**: Each layer can be tested independently
4. **Maintainability**: Changes are localized to appropriate layer
5. **Security**: Centralized permission checks in service layer
6. **Scalability**: Easy to add new features without modifying existing code

## What Cannot Move to Services

These must remain in controllers:

1. **Request Validation**: Laravel's FormRequest validation
2. **Route Model Binding**: Automatic model resolution
3. **Response Formatting**: HTTP-specific responses
4. **File Downloads**: Streaming responses
5. **Session Management**: Flash messages, redirects

## Entity Status

### ✅ Fully Refactored
- Track (Repository, Service, Controller)
- Team (Repository, Service)
- Idea (Repository)

### 🔄 In Progress
- SystemAdmin Controllers

### ⏳ Pending
- User Management
- Workshop Management
- Organization Management
- Speaker Management
- Edition Management
- News Management
- Settings Management
- Report Generation
- Check-in Management

## Next Steps

1. Complete remaining repositories
2. Create services for all entities
3. Refactor all SystemAdmin controllers
4. Refactor HackathonAdmin controllers
5. Refactor TrackSupervisor controllers
6. Add comprehensive testing
7. Document API endpoints