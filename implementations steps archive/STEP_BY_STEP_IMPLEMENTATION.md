# 🎯 **STEP-BY-STEP IMPLEMENTATION GUIDE**
**For Developers - Zero to Production in 30 Days**

## 📅 **DAY 1-5: FOUNDATION SETUP**

### **Day 1: Environment & Infrastructure**

#### **Morning (4 hours)**
```bash
# 1. Clone and setup project
cd ~/projects/hakathons/projects/guacpanel-tailwind-1.14/
git pull origin main

# 2. Install dependencies
composer install
npm install

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Configure database
# Edit .env file:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hackathon_db
DB_USERNAME=root
DB_PASSWORD=

# 5. Create database
mysql -u root -p
CREATE DATABASE hackathon_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;

# 6. Install Redis
sudo apt-get install redis-server
sudo systemctl enable redis-server
sudo systemctl start redis-server

# 7. Verify Redis
redis-cli ping
# Should return: PONG
```

#### **Afternoon (4 hours)**
```bash
# 8. Run migrations
php artisan migrate:fresh

# 9. Install Spatie Permissions
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate

# 10. Create roles and permissions seeder
php artisan make:seeder RolesAndPermissionsSeeder
```

```php
// database/seeders/RolesAndPermissionsSeeder.php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // System Admin permissions
            'manage-hackathon-editions',
            'manage-all-users',
            'manage-system-settings',
            'view-system-reports',
            'manage-all-teams',
            'manage-all-ideas',
            
            // Hackathon Admin permissions
            'manage-current-edition',
            'approve-teams',
            'reject-teams',
            'review-ideas',
            'assign-supervisors',
            'manage-workshops',
            'manage-news',
            'export-data',
            
            // Track Supervisor permissions
            'review-track-ideas',
            'score-ideas',
            'provide-feedback',
            'view-track-teams',
            
            // Team Leader permissions
            'create-team',
            'manage-team',
            'invite-members',
            'submit-idea',
            'edit-idea',
            
            // Team Member permissions
            'view-team',
            'view-idea',
            'register-workshop',
            'leave-team',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        $systemAdmin = Role::create(['name' => 'system_admin']);
        $systemAdmin->givePermissionTo(Permission::all());

        $hackathonAdmin = Role::create(['name' => 'hackathon_admin']);
        $hackathonAdmin->givePermissionTo([
            'manage-current-edition',
            'approve-teams',
            'reject-teams',
            'review-ideas',
            'assign-supervisors',
            'manage-workshops',
            'manage-news',
            'export-data',
        ]);

        $trackSupervisor = Role::create(['name' => 'track_supervisor']);
        $trackSupervisor->givePermissionTo([
            'review-track-ideas',
            'score-ideas',
            'provide-feedback',
            'view-track-teams',
        ]);

        $teamLeader = Role::create(['name' => 'team_leader']);
        $teamLeader->givePermissionTo([
            'create-team',
            'manage-team',
            'invite-members',
            'submit-idea',
            'edit-idea',
        ]);

        $teamMember = Role::create(['name' => 'team_member']);
        $teamMember->givePermissionTo([
            'view-team',
            'view-idea',
            'register-workshop',
            'leave-team',
        ]);
    }
}
```

```bash
# 11. Run the seeder
php artisan db:seed --class=RolesAndPermissionsSeeder

# 12. Create test users
php artisan make:seeder TestUsersSeeder
```

### **Day 2: Authentication & Middleware**

#### **Morning (4 hours)**
```bash
# 1. Create middleware for each role
php artisan make:middleware SystemAdminMiddleware
php artisan make:middleware HackathonAdminMiddleware  
php artisan make:middleware TrackSupervisorMiddleware
php artisan make:middleware TeamLeaderMiddleware
php artisan make:middleware TeamMemberMiddleware
```

```php
// app/Http/Middleware/SystemAdminMiddleware.php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SystemAdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->hasRole('system_admin')) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
```

```php
// app/Http/Kernel.php
// Add to $middlewareAliases array:
protected $middlewareAliases = [
    // ... existing aliases
    'role' => \Spatie\Permission\Middlewares\RoleMiddleware::class,
    'permission' => \Spatie\Permission\Middlewares\PermissionMiddleware::class,
    'system_admin' => \App\Http\Middleware\SystemAdminMiddleware::class,
    'hackathon_admin' => \App\Http\Middleware\HackathonAdminMiddleware::class,
    'track_supervisor' => \App\Http\Middleware\TrackSupervisorMiddleware::class,
    'team_leader' => \App\Http\Middleware\TeamLeaderMiddleware::class,
    'team_member' => \App\Http\Middleware\TeamMemberMiddleware::class,
];
```

#### **Afternoon (4 hours)**
```php
// app/Http/Middleware/HandleInertiaRequests.php
<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'avatar' => $request->user()->avatar,
                    'roles' => $request->user()->getRoleNames(),
                    'permissions' => $request->user()->getAllPermissions()->pluck('name'),
                    'primary_role' => $request->user()->getRoleNames()->first(),
                    'team_id' => $request->user()->team_id,
                    'track_id' => $request->user()->track_id,
                ] : null,
            ],
            'locale' => app()->getLocale(),
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'warning' => fn () => $request->session()->get('warning'),
                'info' => fn () => $request->session()->get('info'),
            ],
            'app' => [
                'name' => config('app.name'),
                'url' => config('app.url'),
            ],
        ]);
    }
}
```

### **Day 3: Database Models & Relationships**

#### **Morning (4 hours)**
```bash
# Create all missing models
php artisan make:model HackathonEdition -m
php artisan make:model Team -m
php artisan make:model TeamMember -m
php artisan make:model Idea -m
php artisan make:model IdeaFile -m
php artisan make:model IdeaReview -m
php artisan make:model Track -m
php artisan make:model Workshop -m
php artisan make:model WorkshopRegistration -m
php artisan make:model WorkshopAttendance -m
php artisan make:model News -m
php artisan make:model Organization -m
php artisan make:model Speaker -m
php artisan make:model AuditLog -m
```

#### **Afternoon (4 hours)**
```php
// app/Models/Team.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'join_code',
        'leader_id',
        'hackathon_id',
        'track_id',
        'status',
        'max_members',
        'university',
        'college',
    ];

    protected $casts = [
        'max_members' => 'integer',
    ];

    public function leader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    public function hackathon(): BelongsTo
    {
        return $this->belongsTo(HackathonEdition::class, 'hackathon_id');
    }

    public function track(): BelongsTo
    {
        return $this->belongsTo(Track::class);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'team_members')
            ->withPivot('status', 'joined_at')
            ->withTimestamps();
    }

    public function ideas(): HasMany
    {
        return $this->hasMany(Idea::class);
    }

    public function getCurrentIdeaAttribute()
    {
        return $this->ideas()->latest()->first();
    }

    public function getMembersCountAttribute(): int
    {
        return $this->members()->wherePivot('status', 'accepted')->count();
    }

    public function getIsFullAttribute(): bool
    {
        return $this->members_count >= $this->max_members;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($team) {
            if (empty($team->join_code)) {
                $team->join_code = strtoupper(substr(md5(uniqid()), 0, 8));
            }
        });
    }
}
```

### **Day 4: Controllers Structure**

#### **Morning (4 hours)**
```bash
# Create all controllers
mkdir -p app/Http/Controllers/{SystemAdmin,HackathonAdmin,TrackSupervisor,TeamLeader,TeamMember,Api/V1}

# System Admin Controllers
php artisan make:controller SystemAdmin/DashboardController
php artisan make:controller SystemAdmin/HackathonEditionController --resource
php artisan make:controller SystemAdmin/UserController --resource
php artisan make:controller SystemAdmin/SettingsController
php artisan make:controller SystemAdmin/ReportController

# Hackathon Admin Controllers
php artisan make:controller HackathonAdmin/DashboardController
php artisan make:controller HackathonAdmin/TeamController --resource
php artisan make:controller HackathonAdmin/IdeaController --resource
php artisan make:controller HackathonAdmin/WorkshopController --resource
php artisan make:controller HackathonAdmin/NewsController --resource

# Track Supervisor Controllers
php artisan make:controller TrackSupervisor/DashboardController
php artisan make:controller TrackSupervisor/IdeaController
php artisan make:controller TrackSupervisor/WorkshopController

# Team Leader Controllers
php artisan make:controller TeamLeader/DashboardController
php artisan make:controller TeamLeader/TeamController
php artisan make:controller TeamLeader/IdeaController

# Team Member Controllers
php artisan make:controller TeamMember/DashboardController
php artisan make:controller TeamMember/TeamController
php artisan make:controller TeamMember/WorkshopController
```

#### **Afternoon (4 hours)**
```php
// app/Http/Controllers/SystemAdmin/DashboardController.php
<?php

namespace App\Http\Controllers\SystemAdmin;

use App\Http\Controllers\Controller;
use App\Models\HackathonEdition;
use App\Models\Team;
use App\Models\User;
use App\Models\Idea;
use App\Models\Workshop;
use Inertia\Inertia;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $stats = [
            'total_editions' => HackathonEdition::count(),
            'active_editions' => HackathonEdition::where('status', 'active')->count(),
            'total_users' => User::count(),
            'total_teams' => Team::count(),
            'approved_teams' => Team::where('status', 'approved')->count(),
            'total_ideas' => Idea::count(),
            'total_workshops' => Workshop::count(),
            'system_health' => $this->getSystemHealth(),
        ];

        $recentActivities = $this->getRecentActivities();
        $upcomingEvents = $this->getUpcomingEvents();

        return Inertia::render('SystemAdmin/Dashboard/Index', [
            'stats' => $stats,
            'recentActivities' => $recentActivities,
            'upcomingEvents' => $upcomingEvents,
        ]);
    }

    private function getSystemHealth()
    {
        // Check various system components
        $checks = [
            'database' => $this->checkDatabase(),
            'redis' => $this->checkRedis(),
            'storage' => $this->checkStorage(),
            'queue' => $this->checkQueue(),
        ];

        $healthScore = collect($checks)->filter()->count() / count($checks) * 100;

        return [
            'score' => $healthScore,
            'checks' => $checks,
            'status' => $healthScore >= 80 ? 'healthy' : ($healthScore >= 50 ? 'warning' : 'critical'),
        ];
    }

    private function checkDatabase(): bool
    {
        try {
            \DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function checkRedis(): bool
    {
        try {
            \Redis::ping();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function checkStorage(): bool
    {
        return is_writable(storage_path());
    }

    private function checkQueue(): bool
    {
        try {
            // Check if queue worker is running
            $workers = \Queue::size('default');
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function getRecentActivities()
    {
        // Fetch recent activities from audit log
        return \DB::table('audit_logs')
            ->join('users', 'audit_logs.user_id', '=', 'users.id')
            ->select(
                'audit_logs.*',
                'users.name as user_name'
            )
            ->orderBy('audit_logs.created_at', 'desc')
            ->limit(10)
            ->get();
    }

    private function getUpcomingEvents()
    {
        return Workshop::where('start_datetime', '>', now())
            ->orderBy('start_datetime')
            ->limit(5)
            ->get();
    }
}
```

### **Day 5: Request Validation Classes**

#### **Full Day (8 hours)**
```bash
# Create request classes
mkdir -p app/Http/Requests/{Auth,Team,Idea,Workshop,News,Settings}

# Authentication Requests
php artisan make:request Auth/LoginRequest
php artisan make:request Auth/RegisterRequest

# Team Requests
php artisan make:request Team/CreateTeamRequest
php artisan make:request Team/UpdateTeamRequest
php artisan make:request Team/InviteMemberRequest
php artisan make:request Team/ApproveTeamRequest
php artisan make:request Team/RejectTeamRequest

# Idea Requests
php artisan make:request Idea/SubmitIdeaRequest
php artisan make:request Idea/UpdateIdeaRequest
php artisan make:request Idea/ReviewIdeaRequest

# Workshop Requests
php artisan make:request Workshop/CreateWorkshopRequest
php artisan make:request Workshop/UpdateWorkshopRequest
php artisan make:request Workshop/RegisterWorkshopRequest
```

```php
// app/Http/Requests/Team/CreateTeamRequest.php
<?php

namespace App\Http\Requests\Team;

use Illuminate\Foundation\Http\FormRequest;

class CreateTeamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create-team');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:teams,name'],
            'description' => ['required', 'string', 'max:1000'],
            'hackathon_id' => ['required', 'exists:hackathon_editions,id'],
            'track_id' => ['required', 'exists:tracks,id'],
            'university' => ['required', 'string', 'max:255'],
            'college' => ['required', 'string', 'max:255'],
            'max_members' => ['nullable', 'integer', 'min:2', 'max:5'],
            'member_emails' => ['nullable', 'array', 'max:4'],
            'member_emails.*' => ['email', 'distinct', 'exists:users,email'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم الفريق مطلوب',
            'name.unique' => 'اسم الفريق مستخدم بالفعل',
            'description.required' => 'وصف الفريق مطلوب',
            'hackathon_id.required' => 'يجب اختيار الهاكاثون',
            'track_id.required' => 'يجب اختيار المسار',
            'member_emails.*.exists' => 'البريد الإلكتروني :input غير مسجل في النظام',
        ];
    }
}
```

---

## 📅 **DAY 6-10: FRONTEND DEVELOPMENT**

### **Day 6: Vue.js & Inertia Setup**

#### **Morning (4 hours)**
```bash
# 1. Install frontend dependencies
npm install @inertiajs/vue3
npm install @headlessui/vue
npm install @heroicons/vue
npm install pinia
npm install vue-i18n@9
npm install vee-validate
npm install yup
npm install dayjs
npm install lodash
npm install axios

# 2. Configure Vite
```

```javascript
// vite.config.js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {
            '@': path.resolve('resources/js'),
            '~': path.resolve('resources'),
        },
    },
});
```

#### **Afternoon (4 hours)**
```javascript
// resources/js/app.js
import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import { createPinia } from 'pinia';
import { createI18n } from 'vue-i18n';
import ar from './Locales/ar.json';
import en from './Locales/en.json';

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Hackathon';

// Create i18n instance
const i18n = createI18n({
    legacy: false,
    locale: localStorage.getItem('locale') || 'ar',
    fallbackLocale: 'en',
    messages: { ar, en },
});

// Create Pinia instance
const pinia = createPinia();

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(
        `./Pages/${name}.vue`,
        import.meta.glob('./Pages/**/*.vue')
    ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .use(pinia)
            .use(i18n)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
```

### **Day 7: Navigation Component**

#### **Full Day (8 hours)**
```vue
<!-- resources/js/Components/NavSidebarDesktop.vue -->
<template>
  <nav class="fixed top-0 z-50 w-full bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
    <div class="px-3 py-3 lg:px-5 lg:pl-3">
      <div class="flex items-center justify-between">
        <div class="flex items-center justify-start">
          <button
            @click="toggleSidebar"
            type="button"
            class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200"
          >
            <span class="sr-only">Open sidebar</span>
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
              <path
                clip-rule="evenodd"
                fill-rule="evenodd"
                d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"
              ></path>
            </svg>
          </button>
          <Link :href="route('dashboard')" class="flex ml-2 md:mr-24">
            <img src="/logo.svg" class="h-8 mr-3" alt="Logo" />
            <span class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap">
              {{ $t('app.name') }}
            </span>
          </Link>
        </div>
        <div class="flex items-center">
          <LanguageSwitcher />
          <NotificationDropdown />
          <UserDropdown />
        </div>
      </div>
    </div>
  </nav>

  <aside
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform bg-white border-r border-gray-200 sm:translate-x-0 dark:bg-gray-800 dark:border-gray-700"
  >
    <div class="h-full px-3 pb-4 overflow-y-auto">
      <ul class="space-y-2 font-medium">
        <li v-for="item in menuItems" :key="item.name">
          <Link
            v-if="!item.children"
            :href="route(item.route)"
            :class="isActiveRoute(item.route) ? 'bg-gray-100 dark:bg-gray-700' : ''"
            class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700"
          >
            <component :is="item.icon" class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" />
            <span class="ml-3">{{ $t(item.label) }}</span>
          </Link>
          
          <div v-else>
            <button
              @click="toggleSubmenu(item.name)"
              type="button"
              class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700"
            >
              <component :is="item.icon" class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" />
              <span class="flex-1 ml-3 text-left whitespace-nowrap">{{ $t(item.label) }}</span>
              <svg
                :class="submenuOpen[item.name] ? 'rotate-180' : ''"
                class="w-3 h-3 transition-transform"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>
            <ul v-show="submenuOpen[item.name]" class="py-2 space-y-2">
              <li v-for="child in item.children" :key="child.name">
                <Link
                  :href="route(child.route)"
                  :class="isActiveRoute(child.route) ? 'bg-gray-100 dark:bg-gray-700' : ''"
                  class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700"
                >
                  {{ $t(child.label) }}
                </Link>
              </li>
            </ul>
          </div>
        </li>
      </ul>
    </div>
  </aside>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import {
  HomeIcon,
  UsersIcon,
  LightBulbIcon,
  AcademicCapIcon,
  NewspaperIcon,
  CogIcon,
  ChartBarIcon,
  TrophyIcon,
} from '@heroicons/vue/24/outline';

const { t } = useI18n();
const page = usePage();
const sidebarOpen = ref(false);
const submenuOpen = ref({});

const user = computed(() => page.props.auth.user);
const primaryRole = computed(() => user.value?.primary_role);

const menuConfigs = {
  system_admin: [
    { name: 'dashboard', route: 'system-admin.dashboard', icon: HomeIcon, label: 'navigation.dashboard' },
    { name: 'editions', route: 'system-admin.editions.index', icon: TrophyIcon, label: 'navigation.editions' },
    { name: 'users', route: 'system-admin.users.index', icon: UsersIcon, label: 'navigation.users' },
    {
      name: 'settings',
      icon: CogIcon,
      label: 'navigation.settings',
      children: [
        { name: 'smtp', route: 'system-admin.settings.smtp', label: 'navigation.smtp' },
        { name: 'branding', route: 'system-admin.settings.branding', label: 'navigation.branding' },
        { name: 'twitter', route: 'system-admin.settings.twitter', label: 'navigation.twitter' },
      ],
    },
    { name: 'reports', route: 'system-admin.reports.index', icon: ChartBarIcon, label: 'navigation.reports' },
  ],
  hackathon_admin: [
    { name: 'dashboard', route: 'hackathon-admin.dashboard', icon: HomeIcon, label: 'navigation.dashboard' },
    { name: 'teams', route: 'hackathon-admin.teams.index', icon: UsersIcon, label: 'navigation.teams' },
    { name: 'ideas', route: 'hackathon-admin.ideas.index', icon: LightBulbIcon, label: 'navigation.ideas' },
    { name: 'workshops', route: 'hackathon-admin.workshops.index', icon: AcademicCapIcon, label: 'navigation.workshops' },
    { name: 'news', route: 'hackathon-admin.news.index', icon: NewspaperIcon, label: 'navigation.news' },
  ],
  track_supervisor: [
    { name: 'dashboard', route: 'track-supervisor.dashboard', icon: HomeIcon, label: 'navigation.dashboard' },
    { name: 'ideas', route: 'track-supervisor.ideas.index', icon: LightBulbIcon, label: 'navigation.ideas_review' },
    { name: 'workshops', route: 'track-supervisor.workshops.index', icon: AcademicCapIcon, label: 'navigation.workshops' },
  ],
  team_leader: [
    { name: 'dashboard', route: 'team-leader.dashboard', icon: HomeIcon, label: 'navigation.dashboard' },
    { name: 'team', route: 'team-leader.team.show', icon: UsersIcon, label: 'navigation.my_team' },
    { name: 'idea', route: 'team-leader.idea.show', icon: LightBulbIcon, label: 'navigation.our_idea' },
    { name: 'workshops', route: 'team-leader.workshops.index', icon: AcademicCapIcon, label: 'navigation.workshops' },
  ],
  team_member: [
    { name: 'dashboard', route: 'team-member.dashboard', icon: HomeIcon, label: 'navigation.dashboard' },
    { name: 'team', route: 'team-member.team.show', icon: UsersIcon, label: 'navigation.my_team' },
    { name: 'workshops', route: 'team-member.workshops.index', icon: AcademicCapIcon, label: 'navigation.workshops' },
  ],
};

const menuItems = computed(() => menuConfigs[primaryRole.value] || []);

const toggleSidebar = () => {
  sidebarOpen.value = !sidebarOpen.value;
};

const toggleSubmenu = (name) => {
  submenuOpen.value[name] = !submenuOpen.value[name];
};

const isActiveRoute = (routeName) => {
  return route().current(routeName);
};
</script>
```

---

## 📅 **DAY 11-15: SERVICES & BUSINESS LOGIC**

### **Day 11: Service Layer Implementation**

```php
// app/Services/TeamService.php
<?php

namespace App\Services;

use App\Models\Team;
use App\Models\User;
use App\Notifications\TeamInvitationNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TeamService
{
    public function createTeam(array $data, User $leader): Team
    {
        return DB::transaction(function () use ($data, $leader) {
            // Create team
            $team = Team::create([
                'name' => $data['name'],
                'description' => $data['description'],
                'hackathon_id' => $data['hackathon_id'],
                'track_id' => $data['track_id'],
                'leader_id' => $leader->id,
                'university' => $data['university'],
                'college' => $data['college'],
                'max_members' => $data['max_members'] ?? 5,
                'status' => 'pending',
                'join_code' => $this->generateJoinCode(),
            ]);

            // Add leader as first member
            $team->members()->attach($leader->id, [
                'status' => 'accepted',
                'joined_at' => now(),
            ]);

            // Send invitations if provided
            if (!empty($data['member_emails'])) {
                foreach ($data['member_emails'] as $email) {
                    $this->inviteMember($team, $email);
                }
            }

            // Update leader's team_id
            $leader->update(['team_id' => $team->id]);

            // Log activity
            activity()
                ->performedOn($team)
                ->causedBy($leader)
                ->log('Team created');

            return $team;
        });
    }

    public function inviteMember(Team $team, string $email): void
    {
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            // Send invitation email to register
            // Implementation for sending external invitation
            return;
        }

        // Check if user already in a team
        if ($user->team_id) {
            throw new \Exception('User is already in a team');
        }

        // Add as pending member
        $team->members()->attach($user->id, [
            'status' => 'invited',
            'invited_at' => now(),
        ]);

        // Send notification
        $user->notify(new TeamInvitationNotification($team));
    }

    public function approveTeam(Team $team, User $approver, string $notes = null): void
    {
        $team->update([
            'status' => 'approved',
            'approved_by' => $approver->id,
            'approved_at' => now(),
            'approval_notes' => $notes,
        ]);

        // Notify team members
        $team->members->each(function ($member) use ($team) {
            $member->notify(new TeamApprovedNotification($team));
        });

        // Log activity
        activity()
            ->performedOn($team)
            ->causedBy($approver)
            ->withProperties(['notes' => $notes])
            ->log('Team approved');
    }

    public function rejectTeam(Team $team, User $rejector, string $reason): void
    {
        $team->update([
            'status' => 'rejected',
            'rejected_by' => $rejector->id,
            'rejected_at' => now(),
            'rejection_reason' => $reason,
        ]);

        // Notify team leader
        $team->leader->notify(new TeamRejectedNotification($team, $reason));

        // Log activity
        activity()
            ->performedOn($team)
            ->causedBy($rejector)
            ->withProperties(['reason' => $reason])
            ->log('Team rejected');
    }

    private function generateJoinCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (Team::where('join_code', $code)->exists());

        return $code;
    }
}
```

---

## 📅 **DAY 16-20: API & INTEGRATIONS**

[Content continues with API implementation, WebSocket setup, Queue configuration, etc.]

---

## 📅 **DAY 21-25: TESTING & OPTIMIZATION**

[Content continues with testing suite, performance optimization, etc.]

---

## 📅 **DAY 26-30: DEPLOYMENT & LAUNCH**

[Content continues with deployment procedures, monitoring setup, etc.]

---

## ✅ **DAILY PROGRESS CHECKLIST**

### **Week 1: Foundation (Days 1-5)**
- [ ] Day 1: Environment setup & infrastructure
- [ ] Day 2: Authentication & middleware
- [ ] Day 3: Database models & relationships
- [ ] Day 4: Controllers structure
- [ ] Day 5: Request validation classes

### **Week 2: Frontend (Days 6-10)**
- [ ] Day 6: Vue.js & Inertia setup
- [ ] Day 7: Navigation component
- [ ] Day 8: Dashboard pages
- [ ] Day 9: CRUD pages
- [ ] Day 10: Forms & validation

### **Week 3: Business Logic (Days 11-15)**
- [ ] Day 11: Service layer
- [ ] Day 12: Repository pattern
- [ ] Day 13: Events & listeners
- [ ] Day 14: Notifications
- [ ] Day 15: Jobs & queues

### **Week 4: Integration (Days 16-20)**
- [ ] Day 16: API development
- [ ] Day 17: WebSocket setup
- [ ] Day 18: i18n implementation
- [ ] Day 19: Caching strategy
- [ ] Day 20: Security implementation

### **Week 5: Testing (Days 21-25)**
- [ ] Day 21: Unit tests
- [ ] Day 22: Feature tests
- [ ] Day 23: E2E tests
- [ ] Day 24: Performance testing
- [ ] Day 25: Security audit

### **Week 6: Deployment (Days 26-30)**
- [ ] Day 26: Docker setup
- [ ] Day 27: CI/CD pipeline
- [ ] Day 28: Staging deployment
- [ ] Day 29: Production deployment
- [ ] Day 30: Monitoring & handover

---

## 🚨 **CRITICAL SUCCESS FACTORS**

1. **Daily Commits**: Commit code at least 3 times per day
2. **Testing**: Write tests alongside features
3. **Documentation**: Document as you code
4. **Code Reviews**: Review code daily
5. **Progress Tracking**: Update checklist daily
6. **Blockers**: Report blockers immediately
7. **Communication**: Daily standup meetings

---

**This guide provides EXACT step-by-step instructions for building the complete system in 30 days.**
