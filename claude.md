# Claude Instructions for Hackathon System

## 🎯 Project Context
You are working on a Hackathon Management System with 7 different user roles. The system is built with Laravel + Vue.js (Inertia) and uses GuacPanel theming.

## 📁 Project Structure
```
~/projects/hakathons/projects/guacpanel-tailwind-1.14/
├── app/
│   ├── Services/          # Business logic layer
│   ├── Repositories/      # Data access layer
│   ├── Policies/          # Authorization layer
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Shared/    # Reusable base controllers
│   │   └── Middleware/
├── resources/
│   ├── js/
│   │   ├── Pages/
│   │   │   └── Shared/    # Role-agnostic pages
│   │   └── Components/
│   │       └── Shared/    # Reusable components
│   └── views/
└── routes/
```

## 🔑 Key Principles

### 1. Code Reuse Strategy
- **NEVER duplicate code** - Always use shared components
- **Services for business logic** - Keep controllers thin
- **Repositories for data access** - Consistent query patterns
- **Policies for authorization** - Centralized permission logic

### 2. Role-Based Architecture
```php
// All controllers should extend SharedController
class SharedController extends Controller {
    protected $service;
    protected $policy;
    
    public function index() {
        // Policy checks role automatically
        $this->authorize('viewAny', $this->getModelClass());
        
        // Service applies role-based filtering
        return $this->service->getForRole(auth()->user());
    }
}
```

### 3. Frontend Reuse Pattern
```vue
<!-- Shared/Dashboard.vue - Used by ALL roles -->
<template>
    <Default>
        <DashboardLayout :role="userRole" :data="dashboardData">
            <template #widgets>
                <component :is="roleWidgets[userRole]" />
            </template>
        </DashboardLayout>
    </Default>
</template>
```

## 🎨 Theme Configuration
The system uses a **unified mint/green theme** for all roles:
- Primary: `#10B981` (mint green)
- Secondary: `#059669` (dark mint)
- Gradient: `linear-gradient(135deg, #10B981, #059669)`
- Background: `mintcream-100`
- All roles share the same color scheme

## 📋 When Implementing Features

### For Controllers
1. Check if a shared controller exists
2. Extend from shared controller
3. Override only what's different
4. Use services for business logic
5. Use policies for authorization

### For Vue Pages
1. Check if a shared page exists
2. Use composition pattern for role-specific parts
3. Pass role as prop to customize behavior
4. Use slots for role-specific content

### For Services
```php
// TeamService.php
class TeamService {
    public function getTeamsForUser(User $user) {
        return match($user->role) {
            'system_admin' => Team::all(),
            'hackathon_admin' => Team::whereEdition($user->edition_id)->get(),
            'track_supervisor' => Team::whereTrack($user->track_ids)->get(),
            'team_leader', 'team_member' => Team::find($user->team_id),
            default => collect()
        };
    }
}
```

## ⚠️ Critical Rules

1. **NEVER create duplicate pages** for different roles
2. **ALWAYS use services** for business logic
3. **ALWAYS use policies** for authorization
4. **ALWAYS check for existing shared components** before creating new ones
5. **NEVER hardcode role checks** in controllers - use policies

## 🔧 Common Patterns

### Repository Pattern
```php
interface TeamRepositoryInterface {
    public function getAllForRole(User $user);
    public function find($id, User $user);
    public function create(array $data, User $user);
}
```

### Policy Pattern
```php
class TeamPolicy {
    public function viewAny(User $user) {
        return in_array($user->role, [
            'system_admin', 
            'hackathon_admin', 
            'track_supervisor'
        ]);
    }
    
    public function view(User $user, Team $team) {
        return match($user->role) {
            'system_admin' => true,
            'hackathon_admin' => $team->edition_id === $user->edition_id,
            'track_supervisor' => in_array($team->track_id, $user->track_ids),
            'team_leader', 'team_member' => $team->id === $user->team_id,
            default => false
        };
    }
}
```

## 📝 File Naming Conventions
- Shared controllers: `app/Http/Controllers/Shared/ResourceController.php`
- Shared pages: `resources/js/Pages/Shared/ResourceIndex.vue`
- Services: `app/Services/ResourceService.php`
- Repositories: `app/Repositories/ResourceRepository.php`
- Policies: `app/Policies/ResourcePolicy.php`

## 🚀 Quick Commands
```bash
# Create a new shared controller
php artisan make:controller Shared/ResourceController

# Create a service
php artisan make:service ResourceService

# Create a repository
php artisan make:repository ResourceRepository

# Create a policy
php artisan make:policy ResourcePolicy --model=Resource
```

## 📊 Testing Strategy
Always test with multiple roles:
```php
public function test_dashboard_shows_correct_data_for_each_role() {
    $roles = ['system_admin', 'hackathon_admin', 'track_supervisor'];
    
    foreach ($roles as $role) {
        $user = User::factory()->create(['role' => $role]);
        $response = $this->actingAs($user)->get('/dashboard');
        // Assert role-specific data
    }
}
```

## 🔍 Before Starting Any Task
1. Check `REUSABLE_ARCHITECTURE_PLAN.md` for patterns
2. Review existing shared components
3. Identify what can be reused
4. Plan the minimal changes needed
5. Implement using services and policies

Remember: **The goal is maximum code reuse with role-based differentiation through services and policies, not duplication!**
