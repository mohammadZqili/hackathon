# 🚀 **ULTRA-DETAILED IMPLEMENTATION PLAN**
**Complete Hackathon Management System - 100% SRS Coverage**

## 🚨 **VERIFIED SYSTEM ANALYSIS & CRITICAL GAPS**

After comprehensive analysis of HackathonSRS.txt, all Figma images, existing codebase, and frontend structure:
- **Current Plan Coverage:** 50% of SRS requirements
- **Missing Critical Features:** Public pages, QR system, Twitter integration, Arabic support
- **Directory Structure:** ✅ VERIFIED - All paths below are confirmed to exist

## 📁 **VERIFIED DIRECTORY STRUCTURE**
```
✅ EXISTING (VERIFIED):
/home/geek/projects/hakathons/projects/guacpanel-tailwind-1.14/
├── app/Http/Controllers/ ✅
├── app/Models/ ✅
├── database/migrations/ ✅
├── resources/js/Components/ ✅
├── resources/js/Layouts/ ✅
├── resources/js/Pages/ ✅
│   ├── Admin/ ✅
│   ├── Auth/ ✅
│   └── UserAccount/ ✅
└── routes/ ✅

❌ TO CREATE:
├── app/Http/Controllers/SystemAdmin/ ❌
├── app/Http/Controllers/HackathonAdmin/ ❌
├── app/Http/Controllers/TrackSupervisor/ ❌
├── app/Http/Controllers/TeamLeader/ ❌
├── app/Http/Controllers/TeamMember/ ❌
├── app/Http/Controllers/Public/ ❌
├── app/Http/Requests/ ❌
├── app/Services/ ❌
├── resources/js/Pages/SystemAdmin/ ❌
├── resources/js/Pages/HackathonAdmin/ ❌
├── resources/js/Pages/TeamLeader/ ❌
├── resources/js/Pages/TeamMember/ ❌
└── resources/js/Components/Public/ ❌
```

## 🚨 **CRITICAL MISSING FEATURES (FROM SRS ANALYSIS)**

### **❌ COMPLETELY MISSING (0% implemented):**
1. **Public Landing Pages** (SRS F1-F5) - WordPress + Elementor integration
2. **Visitor Workshop Registration** (SRS F21-F28) - Public registration without accounts  
3. **QR/Barcode System** (SRS F24, F26-F28) - Attendance tracking
4. **Twitter/X Integration** (SRS F31) - Auto-posting news
5. **Arabic RTL Support** (SRS requirement) - Bilingual interface
6. **Multi-year Edition Management** (SRS F32-F34) - Historical data management

### **⚠️ PARTIALLY MISSING (30-70% implemented):**
1. **Workshop Management** - Missing public display and attendance features
2. **News System** - Missing public display and Twitter integration
3. **User Registration** - Missing visitor role and public registration
4. **Reporting System** - Missing comprehensive analytics from SRS

## 🏗️ **REVISED SYSTEM ARCHITECTURE**

```
🌐 DUAL ARCHITECTURE SYSTEM (SRS COMPLIANT):

┌─────────────────────────────────────────────────────────────┐
│                    FRONTEND ARCHITECTURE                     │
├─────────────────────────────────────────────────────────────┤
│ 1. PUBLIC SITE (ruman.sa) - WordPress + Elementor          │
│    ├── Landing page with hackathon info (SRS F1)           │
│    ├── About hackathon & organizing bodies (SRS F2)        │
│    ├── Prizes & tracks showcase (SRS F3)                   │
│    ├── Workshops public schedule (SRS F4)                  │
│    ├── News display (SRS F5)                               │
│    └── Public workshop registration (SRS F21-F28)          │
│                                                             │
│ 2. ADMIN PANEL (app.ruman.sa) - Laravel + Vue + Inertia    │
│    ├── System Admin Dashboard (Complete control)           │
│    ├── Hackathon Admin Dashboard (Edition management)      │
│    ├── Track Supervisor Dashboard (Idea review)            │
│    ├── Team Leader Dashboard (Team & idea management)      │
│    └── Team Member Dashboard (Basic participation)         │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│                     BACKEND SERVICES                        │
├─────────────────────────────────────────────────────────────┤
│ • Laravel API (app.ruman.sa/api)                           │
│ • Public APIs for WordPress integration                     │
│ • QR Code Generation & Scanning (Browser-based)            │
│ • Twitter API Integration (Auto-posting)                   │
│ • Email Services (SMTP) - Registration confirmations       │
│ • SMS Services (Optional) - 2FA and notifications          │
│ • File Storage & Management (Ideas: 8 files, 15MB each)    │
│ • Multi-language Support (Arabic RTL + English LTR)        │
└─────────────────────────────────────────────────────────────┘
```

## 📋 **EXISTING COMPONENT ANALYSIS & REUSE STRATEGY**

### **✅ Layout Components (REUSE 100%)**

#### **1. Default.vue Layout (MAIN LAYOUT - REUSE AS-IS)**
**Location:** `resources/js/Layouts/Default.vue`  
**Usage:** All authenticated pages across all roles
**Components Used:** ✅ Already has all we need
- `NavSidebarDesktop.vue` ✅
- `NavProfile.vue` ✅ 
- `Notification.vue` ✅
- `FlashMessage.vue` ✅
- `Footer.vue` ✅
- `ColorThemeSwitcher.vue` ✅
- `Logo.vue` ✅
- `Search.vue` ✅
- `SystemNotice.vue` ✅

**MODIFICATION NEEDED:** Update `NavSidebarDesktop.vue` to show role-based menus

#### **2. NavSidebarDesktop.vue (MODIFY FOR ROLES)**
**Current:** Hardcoded admin menu  
**Needed:** Dynamic menu based on user role

**EXACT MODIFICATION:**
```javascript
// Current hardcoded navigationSections (lines 47-111)
const navigationSections = reactive([
  // Current admin menu...
])

// NEW: Replace with role-based menu
const user = computed(() => page.props.auth?.user)
const userRole = computed(() => user.value?.user_type || 'guest')

const navigationSections = computed(() => {
  const roleMenus = {
    'system_admin': [
      {
        items: [
          { name: 'Dashboard', route: 'admin.dashboard', icon: dashboardIcon },
          { type: 'divider' }
        ]
      },
      {
        items: [
          { name: 'Ideas', route: 'admin.ideas.index', icon: ideasIcon },
          { name: 'Teams', route: 'admin.teams.index', icon: teamsIcon },
          { name: 'Tracks', route: 'admin.tracks.index', icon: tracksIcon },
          { name: 'Workshops', route: 'admin.workshops.index', icon: workshopsIcon },
          { name: 'Check-Ins', route: 'admin.checkins.index', icon: checkinsIcon },
          { name: 'News', route: 'admin.news.index', icon: newsIcon },
          { name: 'Editions', route: 'admin.editions.index', icon: editionsIcon },
          { name: 'Reports', route: 'admin.reports.index', icon: reportsIcon },
          { name: 'Settings', route: 'admin.settings.index', icon: settingsIcon },
          { type: 'divider' }
        ]
      }
    ],
    'hackathon_admin': [
      {
        items: [
          { name: 'Dashboard', route: 'hackathon-admin.dashboard', icon: dashboardIcon },
          { type: 'divider' }
        ]
      },
      {
        items: [
          { name: 'Ideas', route: 'hackathon-admin.ideas.index', icon: ideasIcon },
          { name: 'Teams', route: 'hackathon-admin.teams.index', icon: teamsIcon },
          { name: 'Tracks', route: 'hackathon-admin.tracks.index', icon: tracksIcon },
          { name: 'Workshops', route: 'hackathon-admin.workshops.index', icon: workshopsIcon },
          { name: 'Check-Ins', route: 'hackathon-admin.checkins.index', icon: checkinsIcon },
          { name: 'News', route: 'hackathon-admin.news.index', icon: newsIcon },
          { name: 'Reports', route: 'hackathon-admin.reports.index', icon: reportsIcon },
          { type: 'divider' }
        ]
      }
    ],
    'track_supervisor': [
      {
        items: [
          { name: 'Dashboard', route: 'supervisor.dashboard', icon: dashboardIcon },
          { type: 'divider' }
        ]
      },
      {
        items: [
          { name: 'Ideas', route: 'supervisor.ideas.index', icon: ideasIcon },
          { name: 'Tracks', route: 'supervisor.tracks.index', icon: tracksIcon },
          { name: 'Workshops', route: 'supervisor.workshops.index', icon: workshopsIcon },
          { type: 'divider' }
        ]
      }
    ],
    'team_leader': [
      {
        items: [
          { name: 'Dashboard', route: 'team-lead.dashboard', icon: dashboardIcon },
          { type: 'divider' }
        ]
      },
      {
        items: [
          { name: 'Our Idea', route: 'team-lead.ideas.index', icon: ideasIcon },
          { name: 'My Team', route: 'team-lead.team.index', icon: teamsIcon },
          { name: 'Tracks', route: 'team-lead.tracks.index', icon: tracksIcon },
          { name: 'Workshops', route: 'team-lead.workshops.index', icon: workshopsIcon },
          { type: 'divider' }
        ]
      }
    ],
    'team_member': [
      {
        items: [
          { name: 'Dashboard', route: 'team-member.dashboard', icon: dashboardIcon },
          { type: 'divider' }
        ]
      },
      {
        items: [
          { name: 'Our Idea', route: 'team-member.ideas.index', icon: ideasIcon },
          { name: 'My Team', route: 'team-member.team.index', icon: teamsIcon },
          { name: 'Tracks', route: 'team-member.tracks.index', icon: tracksIcon },
          { name: 'Workshops', route: 'team-member.workshops.index', icon: workshopsIcon },
          { name: 'News', route: 'team-member.news.index', icon: newsIcon },
          { type: 'divider' }
        ]
      }
    ]
  }
  
  return roleMenus[userRole.value] || roleMenus['team_member']
})
```

#### **3. NavProfile.vue (UPDATE ROLE DISPLAY)**
**Current:** Shows generic role from `user.roles[0]`  
**Needed:** Show Arabic role names with proper role badges

**EXACT MODIFICATION:**
```javascript
// Line 10: Update role computation
const primaryRole = computed(() => {
  const roleMap = {
    'system_admin': 'مدير النظام',
    'hackathon_admin': 'مدير الهاكاثون', 
    'track_supervisor': 'مشرف المسار',
    'team_leader': 'قائد الفريق',
    'team_member': 'عضو الفريق',
  }
  return roleMap[user.value?.user_type] || user.value?.user_type || ''
})
```

### **✅ Reusable Data Components (REUSE 100%)**

#### **1. Datatable.vue (PERFECT FOR ALL LIST PAGES)**
**Location:** `resources/js/Components/Datatable.vue`
**Usage:** All index pages (teams, ideas, workshops, news, etc.)
**Features Already Built:**
- ✅ Search functionality
- ✅ Column sorting
- ✅ Pagination (server-side & client-side)
- ✅ Bulk selection & actions
- ✅ Export to CSV
- ✅ Mobile responsive cards
- ✅ Loading states
- ✅ Empty states

**USAGE PATTERN FOR ALL LIST PAGES:**
```vue
<Datatable 
  :data="items.data"
  :columns="tableColumns" 
  :pagination="items"
  :loading="loading"
  :enable-search="true"
  :enable-export="true"
  :search-fields="['name', 'email']"
  title="Page Title"
  @update:pagination="updatePagination"
/>
```

#### **2. Modal.vue (REUSE FOR FORMS)**
**Location:** `resources/js/Components/Modal.vue`
**Usage:** All create/edit forms, confirmations

#### **3. Form Components (REUSE 100%)**
- `FormInput.vue` ✅
- `FormSelect.vue` ✅ 
- `FormTextarea.vue` ✅
- `FormCheckbox.vue` ✅
- `FormRadioGroup.vue` ✅
- `FilePondUploader.vue` ✅

#### **4. Widget Components (REUSE FOR DASHBOARDS)**
- `MetricWidget.vue` ✅
- `StatWidget.vue` ✅
- `AchievementWidget.vue` ✅
- `StockWidget.vue` ✅

#### **5. Chart Components (REUSE FOR REPORTS)**
- `ApexAreaChart.vue` ✅
- `ApexBarChart.vue` ✅
- `ApexDonutChart.vue` ✅
- `ApexLineChart.vue` ✅

---

## 🗃️ **DETAILED SYSTEM ADMIN FLOWS WITH EXACT DATA SOURCES**

### **1. SYSTEM ADMIN DASHBOARD**

#### **Page:** `Admin/Dashboard.vue`
**Route:** `GET /admin/dashboard`  
**Controller:** `Admin\DashboardController@index`

**DATA SOURCES & APIs:**
```php
public function index(Request $request)
{
    $user = $request->user();
    
    // Statistics from services
    $stats = [
        'hackathons' => [
            'total' => $this->hackathonService->getTotalCount(),           // DB: SELECT COUNT(*) FROM hackathons
            'active' => $this->hackathonService->getActiveCount(),        // DB: SELECT COUNT(*) FROM hackathons WHERE is_active = 1
            'archived' => $this->hackathonService->getArchivedCount(),    // DB: SELECT COUNT(*) FROM hackathons WHERE is_archived = 1
        ],
        'teams' => [
            'total' => $this->teamService->getTotalCount(),               // DB: SELECT COUNT(*) FROM teams
            'pending' => $this->teamService->getPendingCount(),          // DB: SELECT COUNT(*) FROM teams WHERE status = 'pending'
            'approved' => $this->teamService->getApprovedCount(),        // DB: SELECT COUNT(*) FROM teams WHERE status = 'approved'
        ],
        'ideas' => [
            'total' => $this->ideaService->getTotalCount(),               // DB: SELECT COUNT(*) FROM ideas
            'pending' => $this->ideaService->getPendingReviewCount(),    // DB: SELECT COUNT(*) FROM ideas WHERE status = 'pending_review'
            'approved' => $this->ideaService->getApprovedCount(),        // DB: SELECT COUNT(*) FROM ideas WHERE status = 'approved'
            'rejected' => $this->ideaService->getRejectedCount(),        // DB: SELECT COUNT(*) FROM ideas WHERE status = 'rejected'
        ],
        'workshops' => [
            'total' => $this->workshopService->getTotalCount(),          // DB: SELECT COUNT(*) FROM workshops
            'upcoming' => $this->workshopService->getUpcomingCount(),    // DB: SELECT COUNT(*) FROM workshops WHERE start_date > NOW()
            'completed' => $this->workshopService->getCompletedCount(),  // DB: SELECT COUNT(*) FROM workshops WHERE end_date < NOW()
        ],
        'users' => [
            'total' => $this->userService->getTotalCount(),              // DB: SELECT COUNT(*) FROM users
            'active' => $this->userService->getActiveCount(),           // DB: SELECT COUNT(*) FROM users WHERE is_active = 1
        ]
    ];
    
    // Recent activities - from audit logs or activity tracking
    $recentActivities = [
        // DB: SELECT * FROM audit_logs ORDER BY created_at DESC LIMIT 10
        // or DB: SELECT * FROM activity_logs ORDER BY created_at DESC LIMIT 10
    ];
    
    return Inertia::render('Admin/Dashboard', [
        'stats' => $stats,
        'recentActivities' => $recentActivities,
        'user' => $user,
    ]);
}
```

**FRONTEND COMPONENT USAGE:**
```vue
<template>
  <Default>
    <!-- Page uses existing PageHeader component -->
    <PageHeader title="Dashboard" />
    
    <!-- Stats Grid using existing MetricWidget -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <MetricWidget 
        v-for="stat in statsArray" 
        :key="stat.key"
        :title="stat.title"
        :value="stat.value"
        :change="stat.change"
        :icon="stat.icon"
        :color="stat.color"
      />
    </div>
    
    <!-- Quick Actions using existing components -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <!-- Action cards with Link components -->
    </div>
    
    <!-- Recent Activity using existing Datatable -->
    <Datatable 
      :data="recentActivities"
      :columns="activityColumns"
      :enable-search="false"
      :enable-export="false"
      title="Recent Activity"
    />
  </Default>
</template>
```

### **2. HACKATHON EDITIONS MANAGEMENT**

#### **Page:** `Admin/Editions/Index.vue`
**Route:** `GET /admin/editions`
**Controller:** `Admin\EditionController@index`

**DATA SOURCES & APIs:**
```php
public function index(Request $request)
{
    // Get editions with related data
    $editions = $this->hackathonService->getAllHackathonsWithStats([
        'with' => ['hackathonAdmin', 'teams', 'ideas', 'workshops'],
        'withCount' => ['teams', 'ideas', 'workshops'],
        'orderBy' => 'year',
        'orderDirection' => 'desc',
        'paginate' => 15
    ]);
    
    // DB Query:
    /*
    SELECT h.*, 
           u.name as hackathon_admin_name,
           COUNT(DISTINCT t.id) as teams_count,
           COUNT(DISTINCT i.id) as ideas_count, 
           COUNT(DISTINCT w.id) as workshops_count
    FROM hackathons h
    LEFT JOIN users u ON h.hackathon_admin_id = u.id
    LEFT JOIN teams t ON h.id = t.hackathon_id
    LEFT JOIN ideas i ON t.id = i.team_id  
    LEFT JOIN workshops w ON h.id = w.hackathon_id
    GROUP BY h.id
    ORDER BY h.year DESC
    LIMIT 15 OFFSET ?
    */
    
    return Inertia::render('Admin/Editions/Index', [
        'editions' => $editions,
        'can' => [
            'create' => $user->can('create-hackathon'),
            'edit' => $user->can('edit-hackathon'),
            'delete' => $user->can('delete-hackathon'),
        ]
    ]);
}
```

**FRONTEND COMPONENT USAGE:**
```vue
<template>
  <Default>
    <!-- Reuse existing PageHeader -->
    <PageHeader 
      title="Hackathon Editions" 
      :show-create="can.create"
      create-route="admin.editions.create"
      create-label="Add Edition"
    />
    
    <!-- Reuse existing Datatable -->
    <Datatable 
      :data="editions.data"
      :columns="editionColumns" 
      :pagination="editions"
      :loading="loading"
      title="Editions"
      :enable-search="true"
      :enable-export="true"
      :search-fields="['name', 'hackathon_admin_name']"
      export-file-name="hackathon_editions"
      @update:pagination="updatePagination"
    />
  </Default>
</template>

<script setup>
const editionColumns = [
  {
    accessorKey: 'name',
    header: 'Hackathon Name',
    cell: ({ getValue }) => getValue()
  },
  {
    accessorKey: 'year', 
    header: 'Year',
    cell: ({ getValue }) => getValue()
  },
  {
    accessorKey: 'registration_dates',
    header: 'Registration Dates',
    cell: ({ row }) => {
      const start = new Date(row.original.registration_start_date).toLocaleDateString()
      const end = new Date(row.original.registration_end_date).toLocaleDateString()
      return `${start} - ${end}`
    }
  },
  {
    accessorKey: 'teams_count',
    header: 'Teams Count', 
    cell: ({ getValue }) => getValue() || 0
  },
  {
    accessorKey: 'hackathon_admin_name',
    header: 'Hackathon Admin',
    cell: ({ getValue }) => getValue() || 'Not Assigned'
  },
  {
    id: 'actions',
    header: 'Actions',
    cell: ({ row }) => h('div', { class: 'flex space-x-2' }, [
      h(Link, { 
        href: route('admin.editions.edit', row.original.id),
        class: 'text-blue-600 hover:text-blue-800 text-sm'
      }, 'Edit'),
      h('button', {
        onClick: () => deleteEdition(row.original.id),
        class: 'text-red-600 hover:text-red-800 text-sm'
      }, 'Delete')
    ])
  }
]
</script>
```

### **3. CREATE/EDIT EDITION**

#### **Page:** `Admin/Editions/Create.vue`
**Route:** `GET /admin/editions/create`
**Controller:** `Admin\EditionController@create`

**DATA SOURCES & APIs:**
```php
public function create(Request $request)
{
    // Get available hackathon admins
    $hackathonAdmins = $this->userService->getUsersByRole('hackathon_admin', [
        'select' => ['id', 'name', 'email'],
        'where' => ['is_active' => true]
    ]);
    
    // DB Query:
    /*
    SELECT u.id, u.name, u.email 
    FROM users u
    INNER JOIN model_has_roles mhr ON u.id = mhr.model_id
    INNER JOIN roles r ON mhr.role_id = r.id  
    WHERE r.name = 'hackathon_admin' 
    AND u.is_active = 1
    ORDER BY u.name
    */
    
    // Get existing hackathons for reference
    $existingHackathons = $this->hackathonService->getAllHackathons([
        'select' => ['id', 'name', 'year'],
        'orderBy' => 'year',
        'orderDirection' => 'desc'
    ]);
    
    return Inertia::render('Admin/Editions/Create', [
        'hackathonAdmins' => $hackathonAdmins,
        'existingHackathons' => $existingHackathons,
    ]);
}

public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'year' => 'required|integer|min:2020|max:2050',
        'hackathon_admin_id' => 'required|exists:users,id',
        'registration_start_date' => 'required|date|after:today',
        'registration_end_date' => 'required|date|after:registration_start_date',
        'idea_submission_start_date' => 'required|date|after:registration_start_date',
        'idea_submission_end_date' => 'required|date|after:idea_submission_start_date',
        'description' => 'nullable|string',
        'logo' => 'nullable|image|max:2048|mimes:jpeg,png,jpg,svg',
        'theme_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
        'max_team_size' => 'required|integer|min:1|max:10',
        'max_teams' => 'required|integer|min:1|max:1000',
    ]);

    try {
        $edition = $this->hackathonService->createHackathon($validated);
        
        return redirect()
            ->route('admin.editions.index')
            ->with('success', 'تم إنشاء نسخة الهاكاثون بنجاح');
            
    } catch (\Exception $e) {
        return back()
            ->withErrors(['error' => $e->getMessage()])
            ->withInput();
    }
}
```

**FRONTEND COMPONENT USAGE:**
```vue
<template>
  <Default>
    <!-- Reuse existing PageHeader -->
    <PageHeader 
      title="Create Hackathon Edition" 
      :breadcrumbs="breadcrumbs"
    />
    
    <!-- Form using existing form components -->
    <div class="max-w-4xl mx-auto">
      <form @submit.prevent="submitForm" class="space-y-8">
        
        <!-- Basic Information Section -->
        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-lg font-medium mb-4">Basic Information</h3>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Reuse FormInput component -->
            <FormInput
              id="name"
              v-model="form.name"
              label="Hackathon Name"
              :error="errors.name"
              required
              placeholder="e.g. Environmental Innovation Hackathon 2024"
            />
            
            <FormInput
              id="year" 
              v-model="form.year"
              type="number"
              label="Year"
              :error="errors.year"
              required
              :min="2020"
              :max="2050"
            />
          </div>
          
          <!-- Reuse FormSelect component -->
          <FormSelect
            id="hackathon_admin_id"
            v-model="form.hackathon_admin_id"
            label="Hackathon Admin"
            :error="errors.hackathon_admin_id"
            :options="hackathonAdminOptions"
            required
            placeholder="Select Hackathon Administrator"
          />
          
          <!-- Reuse FormTextarea component -->
          <FormTextarea
            id="description"
            v-model="form.description" 
            label="Description"
            :error="errors.description"
            rows="4"
            placeholder="Describe the hackathon theme, goals, and objectives..."
          />
        </div>
        
        <!-- Dates Section -->
        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-lg font-medium mb-4">Important Dates</h3>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <FormInput
              id="registration_start_date"
              v-model="form.registration_start_date"
              type="datetime-local" 
              label="Registration Start Date"
              :error="errors.registration_start_date"
              required
            />
            
            <FormInput
              id="registration_end_date"
              v-model="form.registration_end_date"
              type="datetime-local"
              label="Registration End Date" 
              :error="errors.registration_end_date"
              required
            />
            
            <FormInput
              id="idea_submission_start_date"
              v-model="form.idea_submission_start_date"
              type="datetime-local"
              label="Idea Submission Start Date"
              :error="errors.idea_submission_start_date"
              required
            />
            
            <FormInput
              id="idea_submission_end_date" 
              v-model="form.idea_submission_end_date"
              type="datetime-local"
              label="Idea Submission End Date"
              :error="errors.idea_submission_end_date" 
              required
            />
          </div>
        </div>
        
        <!-- Customization Section -->
        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-lg font-medium mb-4">Customization</h3>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Reuse FilePondUploader component -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Hackathon Logo
              </label>
              <FilePondUploader
                v-model="form.logo"
                accept="image/*"
                :max-file-size="2
                label-idle="Drop logo here or <span class='filepond--label-action'>Browse</span>"
                :error="errors.logo"
              />
            </div>
            
            <FormInput
              id="theme_color"
              v-model="form.theme_color"
              type="color"
              label="Theme Color" 
              :error="errors.theme_color"
            />
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
            <FormInput
              id="max_team_size"
              v-model="form.max_team_size"
              type="number"
              label="Maximum Team Size"
              :error="errors.max_team_size"
              required
              :min="1"
              :max="10"
            />
            
            <FormInput
              id="max_teams"
              v-model="form.max_teams"
              type="number" 
              label="Maximum Number of Teams"
              :error="errors.max_teams"
              required
              :min="1"
              :max="1000"
            />
          </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="flex justify-end space-x-4">
          <Link 
            :href="route('admin.editions.index')"
            class="btn-secondary"
          >
            Cancel
          </Link>
          
          <button 
            type="submit" 
            class="btn-primary"
            :disabled="form.processing"
          >
            <span v-if="form.processing">Creating...</span>
            <span v-else>Create Edition</span>
          </button>
        </div>
      </form>
    </div>
  </Default>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
  hackathonAdmins: Array,
  existingHackathons: Array,
})

const form = useForm({
  name: '',
  year: new Date().getFullYear() + 1,
  hackathon_admin_id: '',
  registration_start_date: '',
  registration_end_date: '', 
  idea_submission_start_date: '',
  idea_submission_end_date: '',
  description: '',
  logo: null,
  theme_color: '#3B82F6',
  max_team_size: 5,
  max_teams: 100,
})

const hackathonAdminOptions = computed(() => 
  props.hackathonAdmins.map(admin => ({
    value: admin.id,
    label: `${admin.name} (${admin.email})`
  }))
)

const submitForm = () => {
  form.post(route('admin.editions.store'))
}
</script>
```

### **4. TEAMS MANAGEMENT**

#### **Page:** `Admin/Teams/Index.vue` 
**Route:** `GET /admin/teams`
**Controller:** `Admin\TeamController@index`

**DATA SOURCES & APIs:**
```php
public function index(Request $request)
{
    $filters = $request->validate([
        'search' => 'nullable|string|max:255',
        'status' => 'nullable|in:pending,approved,rejected', 
        'hackathon_id' => 'nullable|exists:hackathons,id',
        'track_id' => 'nullable|exists:tracks,id',
        'per_page' => 'nullable|integer|min:10|max:100',
        'page' => 'nullable|integer|min:1',
    ]);
    
    $teams = $this->teamService->getAllTeamsWithDetails([
        'with' => ['leader', 'members', 'ideas', 'hackathon', 'track'],
        'withCount' => ['members', 'ideas'],
        'filters' => $filters,
        'paginate' => $filters['per_page'] ?? 15
    ]);
    
    // DB Query:
    /*
    SELECT t.*,
           u.name as leader_name,
           u.email as leader_email,
           h.name as hackathon_name,
           tr.name as track_name,
           COUNT(DISTINCT tm.id) as members_count,
           COUNT(DISTINCT i.id) as ideas_count,
           i.status as idea_status
    FROM teams t
    LEFT JOIN users u ON t.leader_id = u.id
    LEFT JOIN hackathons h ON t.hackathon_id = h.id  
    LEFT JOIN tracks tr ON t.track_id = tr.id
    LEFT JOIN team_members tm ON t.id = tm.team_id
    LEFT JOIN ideas i ON t.id = i.team_id
    WHERE t.name LIKE ? OR u.name LIKE ?
    AND (? IS NULL OR t.status = ?)
    AND (? IS NULL OR t.hackathon_id = ?)
    GROUP BY t.id
    ORDER BY t.created_at DESC
    LIMIT ? OFFSET ?
    */
    
    // Get filter options
    $hackathons = $this->hackathonService->getActiveHackathons(['select' => ['id', 'name']]);
    $tracks = $this->trackService->getAllTracks(['select' => ['id', 'name']]);
    
    return Inertia::render('Admin/Teams/Index', [
        'teams' => $teams,
        'filters' => $filters,
        'filterOptions' => [
            'hackathons' => $hackathons,
            'tracks' => $tracks,
            'statuses' => [
                ['value' => 'pending', 'label' => 'Pending Review'],
                ['value' => 'approved', 'label' => 'Approved'],
                ['value' => 'rejected', 'label' => 'Rejected'],
            ]
        ],
        'can' => [
            'approve' => $user->can('approve-team'),
            'reject' => $user->can('reject-team'),
            'edit' => $user->can('edit-team'),
            'delete' => $user->can('delete-team'),
        ]
    ]);
}
```

**FRONTEND COMPONENT USAGE:**
```vue
<template>
  <Default>
    <!-- Reuse existing PageHeader with filters -->
    <PageHeader title="Teams Management">
      <template #actions>
        <!-- Filter Dropdown -->
        <div class="relative">
          <button @click="showFilters = !showFilters" class="btn-secondary">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
            Filters
          </button>
          
          <!-- Filters Panel -->
          <div v-if="showFilters" class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border p-4 z-10">
            <div class="space-y-4">
              <FormSelect
                v-model="filterForm.hackathon_id"
                label="Hackathon"
                :options="hackathonOptions"
                placeholder="All Hackathons"
              />
              
              <FormSelect
                v-model="filterForm.status"
                label="Status"
                :options="filterOptions.statuses"
                placeholder="All Statuses"
              />
              
              <FormSelect
                v-model="filterForm.track_id"
                label="Track"
                :options="trackOptions"
                placeholder="All Tracks"
              />
              
              <div class="flex justify-end space-x-2">
                <button @click="clearFilters" class="btn-secondary btn-sm">Clear</button>
                <button @click="applyFilters" class="btn-primary btn-sm">Apply</button>
              </div>
            </div>
          </div>
        </div>
      </template>
    </PageHeader>
    
    <!-- Reuse existing Datatable with bulk actions -->
    <Datatable 
      :data="teams.data"
      :columns="teamColumns"
      :pagination="teams"
      :loading="loading"
      title="Teams"
      :enable-search="true"
      :enable-export="true"
      :search-fields="['name', 'leader_name', 'hackathon_name']"
      :bulk-delete-route="can.delete ? 'admin.teams.bulk-delete' : ''"
      export-file-name="teams"
      @update:pagination="updatePagination"
      @bulk-delete="handleBulkDelete"
    >
      <!-- Bulk Actions Slot -->
      <template #bulk-actions="{ selectedRows }">
        <button 
          v-if="can.approve" 
          @click="bulkApprove(selectedRows)"
          class="btn-success btn-sm inline-flex items-center gap-2"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
          Bulk Approve
        </button>
        
        <button 
          v-if="can.reject"
          @click="bulkReject(selectedRows)" 
          class="btn-warning btn-sm inline-flex items-center gap-2"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
          Bulk Reject
        </button>
      </template>
    </Datatable>
  </Default>
</template>

<script setup>
const teamColumns = [
  {
    accessorKey: 'name',
    header: 'Team Name',
    cell: ({ getValue, row }) => h('div', { class: 'font-medium' }, [
      h('div', getValue()),
      h('div', { class: 'text-sm text-gray-500' }, `Code: ${row.original.join_code}`)
    ])
  },
  {
    accessorKey: 'leader_name',
    header: 'Team Leader',
    cell: ({ getValue, row }) => h('div', [
      h('div', { class: 'font-medium' }, getValue()),
      h('div', { class: 'text-sm text-gray-500' }, row.original.leader_email)
    ])
  },
  {
    accessorKey: 'members_count',
    header: 'Members',
    cell: ({ getValue, row }) => h('div', { class: 'text-center' }, [
      h('div', { class: 'font-medium' }, `${getValue()}/${row.original.max_members}`),
      h('div', { class: 'text-sm text-gray-500' }, 'members')
    ])
  },
  {
    accessorKey: 'hackathon_name', 
    header: 'Hackathon',
    cell: ({ getValue }) => getValue()
  },
  {
    accessorKey: 'track_name',
    header: 'Track', 
    cell: ({ getValue }) => getValue() || 'Not Assigned'
  },
  {
    accessorKey: 'status',
    header: 'Status',
    cell: ({ getValue }) => {
      const status = getValue()
      const statusConfig = {
        pending: { class: 'bg-yellow-100 text-yellow-800', label: 'Pending' },
        approved: { class: 'bg-green-100 text-green-800', label: 'Approved' },
        rejected: { class: 'bg-red-100 text-red-800', label: 'Rejected' },
      }
      return h('span', { 
        class: `px-2 py-1 text-xs font-medium rounded-full ${statusConfig[status]?.class}` 
      }, statusConfig[status]?.label || status)
    }
  },
  {
    accessorKey: 'idea_status',
    header: 'Idea Status',
    cell: ({ getValue }) => {
      const status = getValue()
      if (!status) return h('span', { class: 'text-gray-500 text-sm' }, 'No Idea')
      
      const statusConfig = {
        draft: { class: 'bg-gray-100 text-gray-800', label: 'Draft' },
        submitted: { class: 'bg-blue-100 text-blue-800', label: 'Submitted' }, 
        under_review: { class: 'bg-yellow-100 text-yellow-800', label: 'Under Review' },
        approved: { class: 'bg-green-100 text-green-800', label: 'Approved' },
        rejected: { class: 'bg-red-100 text-red-800', label: 'Rejected' },
        needs_revision: { class: 'bg-orange-100 text-orange-800', label: 'Needs Revision' },
      }
      
      return h('span', { 
        class: `px-2 py-1 text-xs font-medium rounded-full ${statusConfig[status]?.class}` 
      }, statusConfig[status]?.label || status)
    }
  },
  {
    accessorKey: 'created_at',
    header: 'Registered',
    cell: ({ getValue }) => new Date(getValue()).toLocaleDateString()
  },
  {
    id: 'actions',
    header: 'Actions',
    cell: ({ row }) => h('div', { class: 'flex items-center space-x-2' }, [
      h(Link, {
        href: route('admin.teams.show', row.original.id),
        class: 'text-blue-600 hover:text-blue-800 text-sm'
      }, 'View'),
      
      can.edit && h(Link, {
        href: route('admin.teams.edit', row.original.id), 
        class: 'text-green-600 hover:text-green-800 text-sm'
      }, 'Edit'),
      
      can.approve && row.original.status === 'pending' && h('button', {
        onClick: () => approveTeam(row.original.id),
        class: 'text-green-600 hover:text-green-800 text-sm'
      }, 'Approve'),
      
      can.reject && row.original.status === 'pending' && h('button', {
        onClick: () => rejectTeam(row.original.id),
        class: 'text-red-600 hover:text-red-800 text-sm'
      }, 'Reject'),
    ])
  }
]
</script>
```

---

## 🔄 **COMPLETE API ENDPOINT MAPPING**

### **SYSTEM ADMIN API ENDPOINTS**

#### **Dashboard APIs**
```php
// Dashboard Statistics
GET /api/admin/dashboard/stats
Response: {
  "hackathons": { "total": 5, "active": 2, "archived": 3 },
  "teams": { "total": 150, "pending": 25, "approved": 120, "rejected": 5 },
  "ideas": { "total": 120, "pending": 30, "approved": 80, "rejected": 10 }, 
  "workshops": { "total": 20, "upcoming": 8, "completed": 12 },
  "users": { "total": 500, "active": 480 }
}

// Recent Activities
GET /api/admin/dashboard/activities?limit=10
Response: {
  "data": [
    {
      "id": 1,
      "type": "team_created",
      "description": "Team 'Green Innovators' created", 
      "user": "Ahmad Ali",
      "timestamp": "2024-01-15 10:30:00",
      "metadata": { "team_id": 123 }
    }
  ]
}
```

#### **Hackathon Editions APIs**
```php
// List Editions with Pagination & Search
GET /api/admin/editions?page=1&per_page=15&search=2024&status=active
Response: {
  "data": [
    {
      "id": 1,
      "name": "Environmental Innovation Hackathon 2024",
      "year": 2024,
      "status": "active",
      "registration_start_date": "2024-02-01 00:00:00",
      "registration_end_date": "2024-02-28 23:59:59", 
      "idea_submission_start_date": "2024-03-01 00:00:00",
      "idea_submission_end_date": "2024-03-15 23:59:59",
      "hackathon_admin": {
        "id": 5,
        "name": "Sarah Johnson", 
        "email": "sarah@example.com"
      },
      "teams_count": 45,
      "ideas_count": 38,
      "workshops_count": 6,
      "created_at": "2024-01-15 10:00:00"
    }
  ],
  "current_page": 1,
  "per_page": 15, 
  "total": 5,
  "last_page": 1
}

// Get Single Edition
GET /api/admin/editions/1
Response: {
  "data": { /* full edition details */ },
  "teams": { /* paginated teams */ },
  "ideas": { /* idea statistics */ },
  "workshops": { /* associated workshops */ }
}

// Create Edition
POST /api/admin/editions
Payload: {
  "name": "Environmental Innovation Hackathon 2025",
  "year": 2025,
  "hackathon_admin_id": 5,
  "registration_start_date": "2025-02-01 00:00:00",
  "registration_end_date": "2025-02-28 23:59:59",
  "idea_submission_start_date": "2025-03-01 00:00:00", 
  "idea_submission_end_date": "2025-03-15 23:59:59",
  "description": "Focus on environmental sustainability solutions",
  "theme_color": "#22C55E",
  "max_team_size": 5,
  "max_teams": 100
}
Response: { "data": { /* created edition */ }, "message": "Edition created successfully" }

// Update Edition
PUT /api/admin/editions/1
Payload: { /* same as create */ }
Response: { "data": { /* updated edition */ }, "message": "Edition updated successfully" }

// Delete Edition
DELETE /api/admin/editions/1
Response: { "message": "Edition deleted successfully" }

// Archive Edition 
POST /api/admin/editions/1/archive
Response: { "message": "Edition archived successfully" }
```

#### **Teams Management APIs**
```php
// List Teams with Advanced Filtering
GET /api/admin/teams?page=1&per_page=15&search=alpha&status=approved&hackathon_id=1&track_id=2
Response: {
  "data": [
    {
      "id": 1,
      "name": "Team Alpha",
      "join_code": "ALPHA2024",
      "status": "approved", 
      "max_members": 5,
      "leader": {
        "id": 10,
        "name": "Ahmad Ali",
        "email": "ahmad@example.com"
      },
      "members": [
        {
          "id": 11, 
          "name": "Fatima Hassan",
          "email": "fatima@example.com",
          "status": "accepted",
          "joined_at": "2024-01-16 14:30:00"
        }
      ],
      "members_count": 4,
      "hackathon": {
        "id": 1,
        "name": "Environmental Innovation 2024"
      },
      "track": {
        "id": 2, 
        "name": "Renewable Energy"
      },
      "ideas": [
        {
          "id": 5,
          "title": "Solar Panel Efficiency Optimizer",
          "status": "under_review",
          "submitted_at": "2024-01-20 16:00:00"
        }
      ],
      "ideas_count": 1,
      "created_at": "2024-01-15 12:00:00"
    }
  ],
  "filters": {
    "hackathons": [
      { "id": 1, "name": "Environmental Innovation 2024" }
    ],
    "tracks": [
      { "id": 1, "name": "Smart Agriculture" },
      { "id": 2, "name": "Renewable Energy" }
    ],
    "statuses": [
      { "value": "pending", "label": "Pending Review" },
      { "value": "approved", "label": "Approved" }, 
      { "value": "rejected", "label": "Rejected" }
    ]
  },
  "current_page": 1,
  "per_page": 15,
  "total": 150
}

// Get Single Team with Full Details
GET /api/admin/teams/1
Response: {
  "data": {
    "id": 1,
    /* full team details with members, ideas, files, activity log */
    "activity_log": [
      {
        "action": "team_created",
        "user": "Ahmad Ali",
        "timestamp": "2024-01-15 12:00:00"
      }
    ]
  }
}

// Approve Team
POST /api/admin/teams/1/approve
Payload: { "notes": "Team meets all requirements" }
Response: { "message": "Team approved successfully" }

// Reject Team
POST /api/admin/teams/1/reject  
Payload: { "reason": "Incomplete registration", "notes": "Missing member information" }
Response: { "message": "Team rejected successfully" }

// Bulk Team Actions
POST /api/admin/teams/bulk-action
Payload: {
  "action": "approve", // or "reject", "delete"
  "team_ids": [1, 2, 3, 4],
  "notes": "Batch approval for qualifying teams"
}
Response: { 
  "success_count": 3,
  "failed_count": 1, 
  "message": "Bulk action completed" 
}

// Assign Team to Track
POST /api/admin/teams/1/assign-track
Payload: { "track_id": 2 }
Response: { "message": "Team assigned to track successfully" }
```

#### **Ideas Management APIs**
```php
// List Ideas with Supervisor Assignment
GET /api/admin/ideas?page=1&status=pending_review&track_id=2&supervisor_id=8
Response: {
  "data": [
    {
      "id": 1,
      "title": "Solar Panel Efficiency Optimizer",
      "description": "AI-powered system to optimize solar panel positioning...",
      "status": "pending_review",
      "team": {
        "id": 1,
        "name": "Team Alpha", 
        "leader": "Ahmad Ali"
      },
      "track": {
        "id": 2,
        "name": "Renewable Energy"
      },
      "supervisor": {
        "id": 8,
        "name": "Dr. Mohammed Khalil",
        "email": "dr.khalil@university.sa"
      },
      "files": [
        {
          "id": 10,
          "filename": "presentation.pdf",
          "size": 2048576,
          "uploaded_by": "Ahmad Ali",
          "uploaded_at": "2024-01-20 16:00:00"
        }
      ],
      "files_count": 3,
      "reviews": [
        {
          "id": 1,
          "reviewer": "Dr. Mohammed Khalil",
          "status": "under_review",
          "feedback": "Interesting concept, needs more technical details",
          "score": null,
          "reviewed_at": "2024-01-22 10:30:00"
        }
      ],
      "submitted_at": "2024-01-20 16:00:00",
      "last_modified_at": "2024-01-21 14:30:00"
    }
  ]
}

// Assign Supervisor to Idea
POST /api/admin/ideas/1/assign-supervisor
Payload: { "supervisor_id": 8, "notes": "Expertise in renewable energy systems" }
Response: { "message": "Supervisor assigned successfully" }

// Get Idea Details with Review History
GET /api/admin/ideas/1
Response: {
  "data": { /* full idea details */ },
  "review_history": [ /* complete review timeline */ ],
  "available_supervisors": [ /* supervisors for this track */ ]
}
```

#### **Workshops Management APIs** 
```php
// List Workshops with Registration Stats
GET /api/admin/workshops?page=1&hackathon_id=1&status=upcoming
Response: {
  "data": [
    {
      "id": 1,
      "title": "Introduction to Clean Energy Technologies",
      "description": "Comprehensive overview of renewable energy solutions",
      "start_datetime": "2024-03-01 09:00:00",
      "end_datetime": "2024-03-01 12:00:00", 
      "location": "Conference Hall A",
      "max_attendees": 100,
      "current_registrations": 78,
      "status": "upcoming",
      "hackathon": {
        "id": 1,
        "name": "Environmental Innovation 2024"
      },
      "speakers": [
        {
          "id": 1,
          "name": "Dr. Sarah Al-Ahmad",
          "title": "Renewable Energy Expert", 
          "organization": "KAUST",
          "bio": "Leading researcher in solar technology..."
        }
      ],
      "organizations": [
        {
          "id": 1,
          "name": "King Abdullah University of Science and Technology",
          "logo": "kaust-logo.png"
        }
      ],
      "registrations_count": 78,
      "attendance_rate": 0.85, // for completed workshops
      "created_at": "2024-01-10 15:00:00"
    }
  ]
}

// Create Workshop
POST /api/admin/workshops
Payload: {
  "title": "AI in Environmental Monitoring",
  "description": "How artificial intelligence is revolutionizing environmental data analysis",
  "hackathon_id": 1,
  "start_datetime": "2024-03-05 14:00:00",
  "end_datetime": "2024-03-05 17:00:00",
  "location": "Lab Building 2, Room 201",
  "max_attendees": 50,
  "speaker_ids": [2, 3],
  "organization_ids": [1, 2],
  "prerequisites": "Basic programming knowledge",
  "materials_url": "https://github.com/workshop/materials"
}

// Get Workshop Attendees
GET /api/admin/workshops/1/attendees?page=1&status=confirmed
Response: {
  "data": [
    {
      "registration_id": 1,
      "user": {
        "id": 10,
        "name": "Ahmad Ali",
        "email": "ahmad@example.com",
        "user_type": "team_leader"
      },
      "registration_status": "confirmed",
      "attendance_status": "present", // null if not marked yet
      "registered_at": "2024-02-15 10:30:00",
      "barcode": "WS1-REG1-AHMAD-2024"
    }
  ]
}

// Generate Attendance Report
GET /api/admin/workshops/1/attendance-report?format=pdf
Response: { 
  "report_url": "/storage/reports/workshop-1-attendance-2024-03-01.pdf",
  "statistics": {
    "total_registered": 78,
    "total_attended": 66, 
    "attendance_rate": 0.846,
    "no_shows": 12
  }
}
```

#### **News Management APIs**
```php
// List News Articles
GET /api/admin/news?page=1&status=published&hackathon_id=1
Response: {
  "data": [
    {
      "id": 1,
      "title": "Registration Opens for Environmental Innovation Hackathon 2024",
      "slug": "registration-opens-environmental-innovation-hackathon-2024",
      "excerpt": "We're excited to announce that registration is now open...",
      "content": "Full article content here...",
      "featured_image": "/storage/news/registration-opens-2024.jpg",
      "status": "published",
      "hackathon": {
        "id": 1,
        "name": "Environmental Innovation 2024"
      },
      "author": {
        "id": 5,
        "name": "Sarah Johnson"
      },
      "published_at": "2024-01-15 08:00:00",
      "twitter_posted": true,
      "twitter_post_id": "1747123456789",
      "views_count": 1250,
      "created_at": "2024-01-14 16:30:00"
    }
  ]
}

// Create News Article
POST /api/admin/news
Payload: {
  "title": "Winners Announced for Clean Energy Track",
  "content": "We're thrilled to announce the winners...",
  "excerpt": "Three outstanding teams have been selected...",
  "hackathon_id": 1,
  "featured_image": "uploaded_file_object",
  "status": "draft", // or "published"
  "schedule_publish_at": "2024-03-20 09:00:00", // optional
  "auto_tweet": true,
  "tweet_content": "🏆 Winners announced! Congratulations to our Clean Energy track champions!"
}

// Publish Article
POST /api/admin/news/1/publish
Response: { "message": "Article published successfully" }

// Post to Twitter
POST /api/admin/news/1/tweet
Payload: { 
  "custom_message": "🚀 Check out our latest update on the hackathon!" 
}
Response: { 
  "twitter_post_id": "1747123456789",
  "message": "Posted to Twitter successfully" 
}
```

#### **Reports & Analytics APIs**
```php
// System Overview Report
GET /api/admin/reports/overview?hackathon_id=1&start_date=2024-01-01&end_date=2024-03-31
Response: {
  "summary": {
    "total_registrations": 500,
    "total_teams": 125,
    "total_ideas_submitted": 98,
    "total_workshop_attendees": 1250,
    "completion_rate": 0.78
  },
  "registration_timeline": [
    { "date": "2024-02-01", "count": 25 },
    { "date": "2024-02-02", "count": 45 }
  ],
  "team_formation_stats": {
    "teams_with_full_members": 89,
    "teams_seeking_members": 36,
    "average_team_size": 4.2
  },
  "idea_submission_stats": {
    "by_status": {
      "submitted": 98,
      "under_review": 45,
      "approved": 32,
      "rejected": 8,
      "needs_revision": 13
    },
    "by_track": [
      { "track": "Renewable Energy", "count": 32 },
      { "track": "Smart Agriculture", "count": 28 }
    ]
  },
  "workshop_engagement": {
    "total_workshops": 8,
    "average_attendance": 85.6,
    "most_popular": "Introduction to Clean Energy Technologies",
    "attendance_by_workshop": [
      { "workshop": "Introduction to Clean Energy", "registered": 100, "attended": 92 }
    ]
  }
}

// Team Performance Report
GET /api/admin/reports/teams?hackathon_id=1&track_id=2&format=excel
Response: {
  "download_url": "/storage/reports/team-performance-renewable-energy-2024.xlsx",
  "data": [ /* team performance metrics */ ]
}

// Idea Evaluation Report
GET /api/admin/reports/ideas/evaluation?hackathon_id=1&supervisor_id=8
Response: {
  "evaluation_stats": {
    "total_ideas": 15,
    "completed_reviews": 12,
    "pending_reviews": 3,
    "average_score": 7.8,
    "approval_rate": 0.67
  },
  "ideas_by_status": [ /* detailed breakdown */ ],
  "supervisor_workload": [ /* workload distribution */ ]
}

// Workshop Analytics Report  
GET /api/admin/reports/workshops/analytics?hackathon_id=1
Response: {
  "overall_metrics": {
    "total_workshops": 8,
    "total_registrations": 650,
    "total_attendance": 556,
    "overall_attendance_rate": 0.856
  },
  "workshop_performance": [
    {
      "workshop_id": 1,
      "title": "Clean Energy Technologies",
      "registrations": 100,
      "attendance": 92,
      "attendance_rate": 0.92,
      "feedback_score": 4.6,
      "engagement_metrics": {
        "questions_asked": 28,
        "interaction_rate": 0.85
      }
    }
  ]
}
```

---

## 📋 **DETAILED PAGE FLOWS FROM FIGMA ANALYSIS**

### **🏢 SYSTEM ADMIN PAGES - EXACT FLOWS**

#### **1. Teams Management Page (Teams_Admin.png)**
**Route:** `GET /system-admin/teams`
**Controller:** `SystemAdmin\TeamController@index`
**Type:** Inertia Page

**PAGE FLOWS IDENTIFIED:**
1. **Search Teams Flow**
   - **Trigger:** User types in "Search teams" input
   - **API:** `GET /api/system-admin/teams?search={query}&page=1`
   - **Live Search:** Debounced 300ms
   - **Controller Action:** `TeamController@index` with search filtering
   
2. **Team Table Display Flow**
   - **Data Source:** Teams table with pagination
   - **Columns:** Team Name, Founding Date, Team Leader, Actions
   - **Per Page:** 15 teams (configurable)
   - **Sort:** By founding date descending
   
3. **New Team Flow**
   - **Trigger:** "New Team" button click
   - **Action:** Opens modal or navigates to create page
   - **Route:** `GET /system-admin/teams/create` (Inertia)
   - **Controller:** `TeamController@create`
   
4. **Edit Team Flow**
   - **Trigger:** "Edit" action button
   - **Route:** `GET /system-admin/teams/{id}/edit` (Inertia)
   - **Controller:** `TeamController@edit`
   
5. **Delete Team Flow**
   - **Trigger:** "Delete" action button
   - **API:** `DELETE /api/system-admin/teams/{id}`
   - **Controller:** `TeamController@destroy`
   - **Confirmation:** Sweet Alert modal
   - **Success:** Reload table data

**EXACT DATA RETRIEVAL:**
```php
// Controller: SystemAdmin\TeamController@index
public function index(Request $request)
{
    $filters = $request->validate([
        'search' => 'nullable|string|max:255',
        'page' => 'nullable|integer|min:1',
        'per_page' => 'nullable|integer|min:10|max:100',
        'sort_by' => 'nullable|in:name,created_at,leader_name',
        'sort_direction' => 'nullable|in:asc,desc',
    ]);
    
    // Service call
    $teams = $this->teamService->getAllTeamsWithDetails([
        'search' => $filters['search'],
        'paginate' => $filters['per_page'] ?? 15,
        'sort' => [
            'field' => $filters['sort_by'] ?? 'created_at',
            'direction' => $filters['sort_direction'] ?? 'desc'
        ]
    ]);
    
    return Inertia::render('SystemAdmin/Teams/Index', [
        'teams' => $teams,
        'filters' => $filters,
        'can' => [
            'create_teams' => auth()->user()->can('create_teams'),
            'edit_teams' => auth()->user()->can('edit_teams'),
            'delete_teams' => auth()->user()->can('delete_teams'),
        ]
    ]);
}

// Database Query in TeamRepository
/*
SELECT 
    t.id,
    t.name,
    t.created_at as founding_date,
    t.join_code,
    t.max_members,
    t.status,
    u.name as leader_name,
    u.email as leader_email,
    COUNT(tm.id) as members_count,
    h.name as hackathon_name,
    tr.name as track_name
FROM teams t
LEFT JOIN users u ON t.leader_id = u.id
LEFT JOIN team_members tm ON t.id = tm.team_id AND tm.status = 'accepted'
LEFT JOIN hackathons h ON t.hackathon_id = h.id
LEFT JOIN tracks tr ON t.track_id = tr.id
WHERE (? IS NULL OR t.name LIKE ? OR u.name LIKE ?)
GROUP BY t.id
ORDER BY t.created_at DESC
LIMIT ? OFFSET ?
*/
```

#### **2. Team Edit Page (EditTeam_Admin.png)**
**Route:** `GET /system-admin/teams/{id}/edit`
**Controller:** `SystemAdmin\TeamController@edit`
**Type:** Inertia Page

**PAGE FLOWS IDENTIFIED:**
1. **Team Information Display Flow**
   - **Team Name:** Editable input field
   - **Team Leader:** Display with dropdown to change
   - **Team Status:** Dropdown (pending, approved, rejected)
   
2. **Members Management Flow**
   - **Members Table:** Name, Email, Mobile No, Status, Actions
   - **Remove Member:** API call to remove team member
   - **Add Member:** Button opens member search modal
   
3. **Add Member Flow**
   - **Trigger:** "Add Member" button
   - **Modal:** Search existing users or send invitation
   - **API:** `POST /api/system-admin/teams/{id}/members`
   - **Data:** `{user_id: 123}` or `{email: 'user@example.com'}`
   
4. **Remove Member Flow**
   - **Trigger:** "Remove" button for each member
   - **API:** `DELETE /api/system-admin/teams/{id}/members/{member_id}`
   - **Confirmation:** Required
   
5. **Save Changes Flow**
   - **Trigger:** "Save Changes" button
   - **API:** `PUT /api/system-admin/teams/{id}`
   - **Validation:** Team name required, leader exists
   
6. **Disband Team Flow**
   - **Trigger:** "Disband Team" button
   - **API:** `DELETE /api/system-admin/teams/{id}/disband`
   - **Effect:** Removes team but keeps member records

**EXACT DATA RETRIEVAL:**
```php
// Controller: SystemAdmin\TeamController@edit
public function edit(Request $request, $teamId)
{
    $team = $this->teamService->getTeamWithFullDetails($teamId, [
        'with' => ['leader', 'members.user', 'hackathon', 'track', 'ideas'],
        'withCount' => ['members', 'ideas']
    ]);
    
    // Get available users for adding as members
    $availableUsers = $this->userService->getUsersNotInTeam($teamId, [
        'where' => ['user_type' => 'team_member'],
        'select' => ['id', 'name', 'email', 'phone']
    ]);
    
    // Get potential leaders (team_leader type users)
    $potentialLeaders = $this->userService->getPotentialLeaders([
        'where' => ['user_type' => 'team_leader'],
        'select' => ['id', 'name', 'email']
    ]);
    
    return Inertia::render('SystemAdmin/Teams/Edit', [
        'team' => $team,
        'availableUsers' => $availableUsers,
        'potentialLeaders' => $potentialLeaders,
        'can' => [
            'edit_team_details' => auth()->user()->can('edit_teams'),
            'manage_team_members' => auth()->user()->can('manage_team_members'),
            'disband_teams' => auth()->user()->can('disband_teams'),
        ]
    ]);
}
```

#### **3. Ideas Review Page (Idea.png)**
**Route:** `GET /system-admin/ideas/{id}`
**Controller:** `SystemAdmin\IdeaController@show`
**Type:** Inertia Page

**PAGE FLOWS IDENTIFIED:**
1. **Idea Overview Tab Flow**
   - **Idea Details:** Title, Team Name, Submission Date, Track, Hackathon Edition
   - **Description:** Full idea description text
   - **Related Documents:** List of uploaded files with download links
   
2. **Response Tab Flow**
   - **Review History:** All reviews and feedback
   - **Current Status:** Visual status indicator
   - **Supervisor Assignment:** If assigned
   
3. **Decision Making Flow**
   - **Accept Button:** Changes status to 'approved'
   - **Reject Button:** Changes status to 'rejected' 
   - **Need Edit Button:** Changes status to 'needs_revision'
   - **API:** `PUT /api/system-admin/ideas/{id}/decision`
   
4. **Scoring Flow**
   - **Score Input:** "Add Score From 100" field
   - **Score Submission:** Updates idea score
   - **API:** `PUT /api/system-admin/ideas/{id}/score`
   
5. **Feedback Flow**
   - **Feedback Text Area:** "Provide feedback or required changes"
   - **Submit Feedback:** Saves feedback and notifies team
   - **API:** `POST /api/system-admin/ideas/{id}/feedback`

**EXACT DATA RETRIEVAL:**
```php
// Controller: SystemAdmin\IdeaController@show
public function show(Request $request, $ideaId)
{
    $idea = $this->ideaService->getIdeaWithFullDetails($ideaId, [
        'with' => [
            'team.leader', 
            'team.members.user', 
            'track', 
            'hackathon',
            'files',
            'reviews.reviewer',
            'supervisor'
        ]
    ]);
    
    // Get review history
    $reviewHistory = $this->ideaService->getIdeaReviewHistory($ideaId);
    
    // Get available supervisors for assignment
    $availableSupervisors = $this->userService->getTrackSupervisors(
        $idea->team->track_id,
        ['select' => ['id', 'name', 'email', 'expertise']]
    );
    
    return Inertia::render('SystemAdmin/Ideas/Show', [
        'idea' => $idea,
        'reviewHistory' => $reviewHistory,
        'availableSupervisors' => $availableSupervisors,
        'can' => [
            'review_ideas' => auth()->user()->can('review_ideas'),
            'score_ideas' => auth()->user()->can('score_ideas'),
            'assign_supervisors' => auth()->user()->can('assign_supervisors'),
        ]
    ]);
}
```

#### **4. Workshops Management (Admin_Workshops.png)**
**Route:** `GET /system-admin/workshops`
**Controller:** `SystemAdmin\WorkshopController@index`
**Type:** Inertia Page

**PAGE FLOWS IDENTIFIED:**
1. **Workshop Tabs Flow**
   - **Workshops Tab:** Main workshop list (active)
   - **Speakers Tab:** Navigate to speakers management
   - **Organizations Tab:** Navigate to organizations management
   
2. **Workshop Search Flow**
   - **Search Input:** "Search workshops" with live search
   - **API:** `GET /api/system-admin/workshops?search={query}`
   - **Debounce:** 300ms delay
   
3. **Workshop Table Flow**
   - **Columns:** Title, Description, Date, Speaker, Organizing Entity, Seats
   - **Data:** Paginated list of all workshops
   - **Actions:** Edit, Delete, View Registrations
   
4. **Add Workshop Flow**
   - **Trigger:** "Add Workshop" button
   - **Route:** `GET /system-admin/workshops/create`
   - **Type:** Modal or separate page
   
5. **Workshop Actions Flow**
   - **View Registrations:** Shows attendees list
   - **Edit Workshop:** Navigate to edit form
   - **Delete Workshop:** Confirmation + API call

**EXACT DATA RETRIEVAL:**
```php
// Controller: SystemAdmin\WorkshopController@index
public function index(Request $request)
{
    $filters = $request->validate([
        'search' => 'nullable|string|max:255',
        'tab' => 'nullable|in:workshops,speakers,organizations',
        'date_from' => 'nullable|date',
        'date_to' => 'nullable|date',
        'status' => 'nullable|in:upcoming,ongoing,completed,cancelled',
    ]);
    
    switch($filters['tab'] ?? 'workshops') {
        case 'workshops':
            $data = $this->workshopService->getAllWorkshopsWithDetails([
                'search' => $filters['search'],
                'date_range' => [$filters['date_from'], $filters['date_to']],
                'status' => $filters['status'],
                'with' => ['speakers', 'organizations', 'hackathon'],
                'withCount' => ['registrations']
            ]);
            break;
            
        case 'speakers':
            $data = $this->speakerService->getAllSpeakersWithStats([
                'search' => $filters['search'],
                'withCount' => ['workshops']
            ]);
            break;
            
        case 'organizations':
            $data = $this->organizationService->getAllOrganizationsWithStats([
                'search' => $filters['search'],
                'withCount' => ['workshops']
            ]);
            break;
    }
    
    return Inertia::render('SystemAdmin/Workshops/Index', [
        'data' => $data,
        'currentTab' => $filters['tab'] ?? 'workshops',
        'filters' => $filters,
    ]);
}
```

#### **5. Add Workshop Page (Admin_AddWorkshops.png)**
**Route:** `GET /system-admin/workshops/create`
**Controller:** `SystemAdmin\WorkshopController@create`
**Type:** Inertia Page

**PAGE FLOWS IDENTIFIED:**
1. **Workshop Form Flow**
   - **Workshop Name:** Text input (required)
   - **Description:** Textarea (required)
   - **Speaker:** Dropdown with search (required)
   - **Organizing Entity:** Dropdown with search (required)
   
2. **DateTime Flow**
   - **Date:** Date picker input
   - **Time:** Time picker input
   - **Validation:** Date must be future, time format validation
   
3. **Location Flow**
   - **Location Type:** Radio buttons (In-Person/Remote)
   - **Location Field:** Text input for venue/address
   - **Remote Link:** URL input (shown if Remote selected)
   
4. **Capacity Flow**
   - **Seat Capacity:** Number input
   - **Validation:** Must be positive integer, max 1000
   
5. **Form Submission Flow**
   - **Submit:** "Add Workshop" button
   - **API:** `POST /api/system-admin/workshops`
   - **Validation:** All required fields
   - **Success:** Redirect to workshops list
   - **Error:** Show field-specific errors

**EXACT DATA RETRIEVAL:**
```php
// Controller: SystemAdmin\WorkshopController@create
public function create(Request $request)
{
    // Get dropdown options
    $speakers = $this->speakerService->getAllActiveSpeakers([
        'select' => ['id', 'name', 'title', 'organization']
    ]);
    
    $organizations = $this->organizationService->getAllActiveOrganizations([
        'select' => ['id', 'name', 'logo']
    ]);
    
    $hackathons = $this->hackathonService->getActiveHackathons([
        'select' => ['id', 'name', 'year']
    ]);
    
    return Inertia::render('SystemAdmin/Workshops/Create', [
        'speakers' => $speakers,
        'organizations' => $organizations,
        'hackathons' => $hackathons,
    ]);
}

// Store method
public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'required|string',
        'speaker_id' => 'required|exists:speakers,id',
        'organization_id' => 'required|exists:organizations,id',
        'hackathon_id' => 'required|exists:hackathons,id',
        'date' => 'required|date|after:today',
        'time' => 'required|date_format:H:i',
        'location_type' => 'required|in:in_person,remote',
        'location' => 'required|string|max:500',
        'remote_link' => 'nullable|required_if:location_type,remote|url',
        'max_attendees' => 'required|integer|min:1|max:1000',
    ]);
    
    $workshop = $this->workshopService->createWorkshop($validated);
    
    return redirect()->route('system-admin.workshops.index')
        ->with('success', 'Workshop created successfully');
}
```

#### **6. Reports Page (Admin_Reports.png)**
**Route:** `GET /system-admin/reports`
**Controller:** `SystemAdmin\ReportController@index`
**Type:** Inertia Page

**PAGE FLOWS IDENTIFIED:**
1. **Overall Statistics Flow**
   - **Cards Display:** Participating Teams, Members, Submitted Ideas, Workshops
   - **Real-time Data:** Auto-refresh every 5 minutes
   - **API:** `GET /api/system-admin/reports/overall-stats`
   
2. **Edition-Specific Statistics Flow**
   - **Edition Filter:** "All Editions" dropdown
   - **Period Filter:** "Summer 2023", "Winter 2023" dropdowns
   - **Download Data:** "Download Data (Excel)" button
   - **API:** `GET /api/system-admin/reports/edition-stats`
   
3. **Statistics Table Flow**
   - **Columns:** Edition, Teams, Members, Ideas, Status, Workshop Attendance, Registrations, Website Visitors
   - **Dynamic Data:** Based on selected filters
   - **Status Indicators:** Completed/Ongoing status badges
   
4. **Data Export Flow**
   - **Excel Export:** Generates and downloads Excel file
   - **API:** `GET /api/system-admin/reports/export?format=excel&edition={id}`
   - **File Generation:** Server-side Excel creation
   
5. **Filter Change Flow**
   - **Edition Change:** Triggers table update
   - **Period Change:** Triggers table update
   - **Real-time Updates:** No page reload required

**EXACT DATA RETRIEVAL:**
```php
// Controller: SystemAdmin\ReportController@index
public function index(Request $request)
{
    $filters = $request->validate([
        'edition_id' => 'nullable|exists:hackathons,id',
        'period' => 'nullable|string',
        'date_from' => 'nullable|date',
        'date_to' => 'nullable|date',
    ]);
    
    // Overall statistics (not filtered)
    $overallStats = $this->reportService->getOverallStatistics();
    
    // Edition-specific statistics
    $editionStats = $this->reportService->getEditionStatistics($filters);
    
    // Available editions for filter
    $editions = $this->hackathonService->getAllHackathons([
        'select' => ['id', 'name', 'year', 'status']
    ]);
    
    return Inertia::render('SystemAdmin/Reports/Index', [
        'overallStats' => $overallStats,
        'editionStats' => $editionStats,
        'editions' => $editions,
        'filters' => $filters,
        'can' => [
            'export_reports' => auth()->user()->can('export_reports'),
            'view_detailed_reports' => auth()->user()->can('view_detailed_reports'),
        ]
    ]);
}

// API endpoint for overall stats
public function getOverallStats()
{
    return response()->json([
        'participating_teams' => $this->teamService->getTotalTeamsCount(),
        'members' => $this->userService->getTotalParticipantsCount(),
        'submitted_ideas' => $this->ideaService->getTotalSubmittedIdeasCount(),
        'workshops' => $this->workshopService->getTotalWorkshopsCount(),
    ]);
}
```

### **👥 TEAM LEADER PAGES - EXACT FLOWS**

#### **7. Team Leader Dashboard (User_Dashboard.png)**
**Route:** `GET /team-leader/dashboard`
**Controller:** `TeamLeader\DashboardController@index`
**Type:** Inertia Page

**PAGE FLOWS IDENTIFIED:**
1. **My Team Section Flow**
   - **Team Management:** "Manage your team members and their roles"
   - **View Team Button:** Navigate to team details page
   - **Route:** `GET /team-leader/my-team`
   
2. **Idea Status Section Flow**
   - **Idea Progress:** "Track the progress of your ideas and initiatives"
   - **View Ideas Button:** Navigate to ideas management
   - **Route:** `GET /team-leader/ideas`
   
3. **Upcoming Workshops Section Flow**
   - **Workshop Schedule:** "See the schedule for upcoming workshops and training sessions"
   - **View Workshops Button:** Navigate to workshops list
   - **Route:** `GET /team-leader/workshops`
   
4. **Dashboard Cards Flow**
   - **Visual Cards:** Each section has illustration and description
   - **Click Action:** Entire card clickable to navigate
   - **Quick Access:** Main navigation shortcuts

**EXACT DATA RETRIEVAL:**
```php
// Controller: TeamLeader\DashboardController@index
public function index(Request $request)
{
    $user = $request->user();
    $team = $this->teamService->getUserTeam($user->id, [
        'with' => ['members.user', 'hackathon'],
        'withCount' => ['members', 'ideas']
    ]);
    
    $dashboardData = [
        'my_team' => [
            'team_name' => $team->name ?? null,
            'members_count' => $team->members_count ?? 0,
            'max_members' => $team->max_members ?? 5,
            'team_status' => $team->status ?? 'pending',
            'join_code' => $team->join_code ?? null,
        ],
        'idea_status' => [
            'submitted_ideas' => $this->ideaService->getTeamIdeasCount($team->id ?? null),
            'approved_ideas' => $this->ideaService->getTeamApprovedIdeasCount($team->id ?? null),
            'pending_reviews' => $this->ideaService->getTeamPendingIdeasCount($team->id ?? null),
            'latest_feedback' => $this->ideaService->getLatestFeedback($team->id ?? null),
        ],
        'upcoming_workshops' => [
            'registered_workshops' => $this->workshopService->getUserRegisteredWorkshops($user->id),
            'available_workshops' => $this->workshopService->getAvailableWorkshops($user->id, 3),
            'next_workshop' => $this->workshopService->getNextWorkshop($user->id),
        ]
    ];
    
    return Inertia::render('TeamLeader/Dashboard', [
        'dashboardData' => $dashboardData,
        'team' => $team,
        'hasTeam' => $team !== null,
    ]);
}
```

#### **8. Create Team Page (Create_team.png)**
**Route:** `GET /team-leader/create-team`
**Controller:** `TeamLeader\TeamController@create`
**Type:** Inertia Page (Modal Style)

**PAGE FLOWS IDENTIFIED:**
1. **Team Name Flow**
   - **Input:** "Team Name" text field
   - **Validation:** Required, unique, 3-50 characters
   - **Real-time Check:** API call to verify uniqueness
   
2. **Hackathon Edition Flow**
   - **Dropdown:** "Hackathon Edition" selection
   - **API:** Gets available hackathons for registration
   - **Validation:** Must select active hackathon
   
3. **Invite Members Flow**
   - **Email Input:** "Invite Members (Email)" field
   - **Add Emails:** Multiple email addresses supported
   - **Member List:** Shows "Member 1", "Member 2", etc.
   - **Remove Option:** X button to remove invited members
   
4. **Invited Members Display Flow**
   - **Dynamic List:** Shows added member emails
   - **Removal:** X button for each member
   - **Validation:** Email format validation
   
5. **Team Creation Flow**
   - **Submit:** "Create Team" button
   - **API:** `POST /api/team-leader/teams`
   - **Success:** Redirect to team dashboard
   - **Error:** Show validation errors

**EXACT DATA RETRIEVAL:**
```php
// Controller: TeamLeader\TeamController@create
public function create(Request $request)
{
    $user = $request->user();
    
    // Check if user already has a team
    $existingTeam = $this->teamService->getUserTeam($user->id);
    if ($existingTeam) {
        return redirect()->route('team-leader.my-team')
            ->with('info', 'You already have a team.');
    }
    
    // Get available hackathons for registration
    $availableHackathons = $this->hackathonService->getAvailableForRegistration([
        'select' => ['id', 'name', 'year', 'registration_end_date'],
        'where' => [
            ['registration_start_date', '<=', now()],
            ['registration_end_date', '>=', now()],
            ['status', 'active']
        ]
    ]);
    
    return Inertia::render('TeamLeader/Teams/Create', [
        'availableHackathons' => $availableHackathons,
        'maxTeamMembers' => config('hackathon.max_team_members', 5),
    ]);
}

// Store method
public function store(Request $request)
{
    $validated = $request->validate([
        'team_name' => 'required|string|min:3|max:50|unique:teams,name',
        'hackathon_id' => 'required|exists:hackathons,id',
        'member_emails' => 'nullable|array|max:4', // max 4 additional members
        'member_emails.*' => 'email|distinct|exists:users,email',
    ]);
    
    // Additional validation: hackathon must be open for registration
    $hackathon = $this->hackathonService->getHackathon($validated['hackathon_id']);
    if (!$this->hackathonService->isOpenForRegistration($hackathon)) {
        return back()->withErrors(['hackathon_id' => 'This hackathon is no longer open for registration.']);
    }
    
    $team = $this->teamService->createTeamWithInvitations($validated, $request->user());
    
    return redirect()->route('team-leader.my-team')
        ->with('success', 'Team created successfully! Invitations sent to members.');
}
```

---

## 🔍 **CRITICAL MISSING COMPONENTS ANALYSIS**

### **🗃️ MISSING DATABASE MIGRATIONS**
Based on Vue components and Figma analysis, these migrations are MISSING:

```php
// Missing: idea_reviews table
Schema::create('idea_reviews', function (Blueprint $table) {
    $table->id();
    $table->char('idea_id', 26);
    $table->char('reviewer_id', 26);
    $table->enum('status', ['pending', 'approved', 'rejected', 'needs_revision']);
    $table->integer('score')->nullable();
    $table->text('feedback')->nullable();
    $table->text('revision_notes')->nullable();
    $table->timestamp('reviewed_at')->nullable();
    $table->timestamps();
    
    $table->foreign('idea_id')->references('id')->on('ideas')->onDelete('cascade');
    $table->foreign('reviewer_id')->references('id')->on('users')->onDelete('cascade');
});

// Missing: workshop_attendances table  
Schema::create('workshop_attendances', function (Blueprint $table) {
    $table->id();
    $table->char('workshop_registration_id', 26);
    $table->enum('status', ['present', 'absent', 'late']);
    $table->timestamp('checked_in_at')->nullable();
    $table->char('checked_in_by', 26)->nullable();
    $table->text('notes')->nullable();
    $table->timestamps();
    
    $table->foreign('workshop_registration_id')->references('id')->on('workshop_registrations')->onDelete('cascade');
    $table->foreign('checked_in_by')->references('id')->on('users')->onDelete('set null');
});

// Missing: settings additions (for tabs)
Schema::table('settings', function (Blueprint $table) {
    $table->json('smtp_settings')->nullable();
    $table->json('sms_api_settings')->nullable();
    $table->json('branding_settings')->nullable();
    $table->json('notification_settings')->nullable();
});

// Missing: news_media table
Schema::create('news_media', function (Blueprint $table) {
    $table->id();
    $table->char('news_id', 26);
    $table->string('file_name');
    $table->string('file_path');
    $table->string('file_type');
    $table->integer('file_size');
    $table->string('mime_type');
    $table->timestamps();
    
    $table->foreign('news_id')->references('id')->on('news')->onDelete('cascade');
});
```

### **📋 COMPLETE TAB-BASED PAGES FROM VUE ANALYSIS**

#### **1. SETTINGS PAGE - 4 TABS**
**Route:** `GET /system-admin/settings`  
**Tabs Identified:**
- **SMTP Tab:** Email configuration settings
- **SMS API Tab:** SMS service configuration  
- **Branding Tab:** App name, colors, logo upload
- **Notifications Tab:** Notification preferences

#### **2. WORKSHOPS PAGE - 3 TABS**
**Route:** `GET /system-admin/workshops`
**Tabs Identified:**
- **Workshops Tab:** Main workshops list (Default)
- **Speakers Tab:** Speaker management 
- **Organizations Tab:** Organization management

#### **3. IDEAS PAGE - 2 TABS** 
**Route:** `GET /system-admin/ideas`
**Tabs Identified:**
- **Overview Tab:** Idea details and information
- **Submitted Ideas Tab:** List of all submitted ideas

#### **4. NEWS PAGE - 2 TABS**
**Route:** `GET /system-admin/news`
**Tabs Identified:**
- **All News Tab:** Published news articles list
- **Media Center Tab:** Media files management

#### **5. REPORTS PAGE - 2 TABS**
**Route:** `GET /system-admin/reports`
**Tabs Identified:**
- **All Reports Tab:** Overall statistics
- **Edition Report Tab:** Edition-specific reporting

#### **6. TEAM IDEAS PAGE (User) - 3 TABS**
**Route:** `GET /team-leader/ideas` & `GET /team-member/ideas`
**Tabs Identified:**
- **Overview Tab:** Idea details view
- **Submit Idea Tab:** Idea submission form
- **Comments Tab:** Feedback and discussions
- **Instructions Tab:** Submission guidelines

### **🎯 COMPLETE REQUEST VALIDATION CLASSES**

#### **Authentication Requests**
```php
// app/Http/Requests/Auth/LoginRequest.php
class LoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8',
            'remember' => 'nullable|boolean',
        ];
    }
}

// app/Http/Requests/Auth/RegisterRequest.php  
class RegisterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:2|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'user_type' => 'required|in:team_leader,team_member',
            'hackathon_id' => 'required|exists:hackathons,id',
        ];
    }
}
```

#### **Team Management Requests**
```php
// app/Http/Requests/Team/CreateTeamRequest.php
class CreateTeamRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:3|max:50|unique:teams,name',
            'hackathon_id' => 'required|exists:hackathons,id',
            'track_id' => 'nullable|exists:tracks,id',
            'member_emails' => 'nullable|array|max:4',
            'member_emails.*' => 'email|distinct|exists:users,email',
            'max_members' => 'nullable|integer|min:2|max:10',
        ];
    }
}

// app/Http/Requests/Team/UpdateTeamRequest.php
class UpdateTeamRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:3|max:50|unique:teams,name,' . $this->route('team'),
            'track_id' => 'nullable|exists:tracks,id',
            'max_members' => 'nullable|integer|min:2|max:10',
            'status' => 'nullable|in:pending,approved,rejected',
        ];
    }
}

// app/Http/Requests/Team/AddMemberRequest.php
class AddMemberRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'user_id' => 'required_without:email|exists:users,id',
            'email' => 'required_without:user_id|email|exists:users,email',
            'role' => 'nullable|in:member,leader',
        ];
    }
}
```

#### **Idea Management Requests**
```php
// app/Http/Requests/Idea/SubmitIdeaRequest.php
class SubmitIdeaRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|min:5|max:255',
            'description' => 'required|string|min:50|max:5000',
            'problem_statement' => 'required|string|min:50|max:2000',
            'solution_approach' => 'required|string|min:50|max:2000',
            'target_audience' => 'nullable|string|max:1000',
            'technologies' => 'nullable|array',
            'technologies.*' => 'string|max:50',
            'files.*' => 'file|mimes:pdf,doc,docx,ppt,pptx|max:10240', // 10MB max
            'track_id' => 'required|exists:tracks,id',
        ];
    }
}

// app/Http/Requests/Idea/ReviewIdeaRequest.php
class ReviewIdeaRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'status' => 'required|in:approved,rejected,needs_revision',
            'score' => 'nullable|integer|min:0|max:100',
            'feedback' => 'required|string|min:10|max:2000',
            'revision_notes' => 'nullable|string|max:1000',
        ];
    }
}
```

#### **Workshop Management Requests**
```php
// app/Http/Requests/Workshop/CreateWorkshopRequest.php
class CreateWorkshopRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|min:5|max:255',
            'description' => 'required|string|min:20|max:2000',
            'hackathon_id' => 'required|exists:hackathons,id',
            'start_datetime' => 'required|date|after:now',
            'end_datetime' => 'required|date|after:start_datetime',
            'location' => 'required|string|max:500',
            'location_type' => 'required|in:in_person,remote,hybrid',
            'remote_link' => 'nullable|required_if:location_type,remote,hybrid|url',
            'max_attendees' => 'required|integer|min:1|max:1000',
            'speaker_ids' => 'required|array|min:1',
            'speaker_ids.*' => 'exists:speakers,id',
            'organization_ids' => 'required|array|min:1',
            'organization_ids.*' => 'exists:organizations,id',
            'prerequisites' => 'nullable|string|max:1000',
            'materials_url' => 'nullable|url',
        ];
    }
}

// app/Http/Requests/Workshop/RegisterWorkshopRequest.php
class RegisterWorkshopRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'workshop_id' => 'required|exists:workshops,id',
            'special_requirements' => 'nullable|string|max:500',
        ];
    }
}
```

#### **Settings Management Requests**
```php
// app/Http/Requests/Settings/UpdateSmtpRequest.php
class UpdateSmtpRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'smtp_host' => 'required|string|max:255',
            'smtp_port' => 'required|integer|min:1|max:65535',
            'smtp_username' => 'required|string|max:255',
            'smtp_password' => 'required|string|max:255',
            'smtp_encryption' => 'required|in:tls,ssl,none',
            'from_email' => 'required|email',
            'from_name' => 'required|string|max:255',
        ];
    }
}

// app/Http/Requests/Settings/UpdateBrandingRequest.php
class UpdateBrandingRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'app_name' => 'required|string|min:2|max:100',
            'primary_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'secondary_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'accent_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
        ];
    }
}

// app/Http/Requests/Settings/UpdateSmsApiRequest.php
class UpdateSmsApiRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'provider' => 'required|in:twilio,nexmo,aws_sns',
            'api_key' => 'required|string|max:255',
            'api_secret' => 'required|string|max:255',
            'from_number' => 'required|string|max:20',
            'is_enabled' => 'required|boolean',
        ];
    }
}
```

#### **News Management Requests**
```php
// app/Http/Requests/News/CreateNewsRequest.php
class CreateNewsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|min:5|max:255',
            'content' => 'required|string|min:50|max:10000',
            'excerpt' => 'nullable|string|max:500',
            'hackathon_id' => 'required|exists:hackathons,id',
            'featured_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120', // 5MB
            'status' => 'required|in:draft,published,archived',
            'publish_at' => 'nullable|date|after:now',
            'auto_tweet' => 'nullable|boolean',
            'tweet_content' => 'nullable|string|max:280',
            'media_files.*' => 'file|mimes:jpg,jpeg,png,pdf,mp4|max:20480', // 20MB
        ];
    }
}
```

### **⚡ 2-HOUR IMPLEMENTATION CHECKLIST**

#### **PHASE 1: Database Setup (15 minutes)**
```bash
# Create missing migrations
php artisan make:migration create_idea_reviews_table
php artisan make:migration create_workshop_attendances_table  
php artisan make:migration create_news_media_table
php artisan make:migration add_settings_columns_to_settings_table

# Run migrations
php artisan migrate
```

#### **PHASE 2: Request Classes (20 minutes)**
```bash
# Create all validation request classes
php artisan make:request Auth/LoginRequest
php artisan make:request Auth/RegisterRequest
php artisan make:request Team/CreateTeamRequest
php artisan make:request Team/UpdateTeamRequest
php artisan make:request Team/AddMemberRequest
php artisan make:request Idea/SubmitIdeaRequest
php artisan make:request Idea/ReviewIdeaRequest
php artisan make:request Workshop/CreateWorkshopRequest
php artisan make:request Settings/UpdateSmtpRequest
php artisan make:request Settings/UpdateBrandingRequest
php artisan make:request Settings/UpdateSmsApiRequest
php artisan make:request News/CreateNewsRequest
```

#### **PHASE 3: Controllers & Services (45 minutes)**
```bash
# System Admin Controllers
php artisan make:controller SystemAdmin/DashboardController
php artisan make:controller SystemAdmin/TeamController --resource
php artisan make:controller SystemAdmin/IdeaController --resource
php artisan make:controller SystemAdmin/WorkshopController --resource
php artisan make:controller SystemAdmin/SpeakerController --resource
php artisan make:controller SystemAdmin/OrganizationController --resource
php artisan make:controller SystemAdmin/SettingsController
php artisan make:controller SystemAdmin/NewsController --resource
php artisan make:controller SystemAdmin/ReportController

# Hackathon Admin Controllers
php artisan make:controller HackathonAdmin/DashboardController
php artisan make:controller HackathonAdmin/TeamController --resource
php artisan make:controller HackathonAdmin/IdeaController --resource
php artisan make:controller HackathonAdmin/WorkshopController --resource

# Team Leader Controllers
php artisan make:controller TeamLeader/DashboardController
php artisan make:controller TeamLeader/TeamController --resource
php artisan make:controller TeamLeader/IdeaController --resource

# Services
php artisan make:service TeamService
php artisan make:service IdeaService
php artisan make:service WorkshopService
php artisan make:service UserService
php artisan make:service HackathonService
php artisan make:service ReportService
php artisan make:service SettingsService
```

#### **PHASE 4: Frontend Components (30 minutes)**
- Update NavSidebarDesktop.vue with role-based menus
- Create tab-based components for Settings, Workshops, Ideas, News
- Implement all form components using existing Datatable.vue

#### **PHASE 5: API Routes & Testing (10 minutes)**
```bash
# Add all routes to api.php and web.php
# Test key endpoints
php artisan serve
```

### **🎯 EXACT FILE STRUCTURE TO CREATE**

#### **Controllers (23 files)**
```
app/Http/Controllers/
├── SystemAdmin/
│   ├── DashboardController.php
│   ├── TeamController.php
│   ├── IdeaController.php
│   ├── WorkshopController.php
│   ├── SpeakerController.php
│   ├── OrganizationController.php
│   ├── SettingsController.php
│   ├── NewsController.php
│   └── ReportController.php
├── HackathonAdmin/
│   ├── DashboardController.php
│   ├── TeamController.php
│   ├── IdeaController.php
│   └── WorkshopController.php
├── TrackSupervisor/
│   ├── DashboardController.php
│   ├── IdeaController.php
│   └── TeamController.php
├── TeamLeader/
│   ├── DashboardController.php
│   ├── TeamController.php
│   └── IdeaController.php
└── TeamMember/
    ├── DashboardController.php
    └── TeamController.php
```

#### **Request Classes (15 files)**
```
app/Http/Requests/
├── Auth/
│   ├── LoginRequest.php
│   └── RegisterRequest.php
├── Team/
│   ├── CreateTeamRequest.php
│   ├── UpdateTeamRequest.php
│   └── AddMemberRequest.php
├── Idea/
│   ├── SubmitIdeaRequest.php
│   └── ReviewIdeaRequest.php
├── Workshop/
│   ├── CreateWorkshopRequest.php
│   └── RegisterWorkshopRequest.php
├── Settings/
│   ├── UpdateSmtpRequest.php
│   ├── UpdateBrandingRequest.php
│   └── UpdateSmsApiRequest.php
└── News/
    └── CreateNewsRequest.php
```

#### **Services (8 files)**
```
app/Services/
├── TeamService.php
├── IdeaService.php
├── WorkshopService.php
├── UserService.php
├── HackathonService.php
├── ReportService.php
├── SettingsService.php
└── SpeakerService.php
```

#### **Vue Components (Modifications)**
```
resources/js/Components/
└── NavSidebarDesktop.vue (MODIFY - add role-based menus)

resources/js/Pages/
├── SystemAdmin/
│   ├── Dashboard.vue
│   ├── Teams/
│   │   ├── Index.vue (with Datatable)
│   │   ├── Edit.vue
│   │   └── Create.vue
│   ├── Ideas/
│   │   ├── Index.vue (2 tabs: Overview + Submitted Ideas)
│   │   └── Show.vue
│   ├── Workshops/
│   │   ├── Index.vue (3 tabs: Workshops + Speakers + Organizations)
│   │   └── Create.vue
│   ├── Settings/
│   │   └── Index.vue (4 tabs: SMTP + SMS API + Branding + Notifications)
│   ├── News/
│   │   ├── Index.vue (2 tabs: All News + Media Center)
│   │   └── Create.vue
│   └── Reports/
│       └── Index.vue (2 tabs: All Reports + Edition Report)
├── TeamLeader/
│   ├── Dashboard.vue
│   ├── Teams/
│   │   ├── Create.vue
│   │   └── Show.vue
│   └── Ideas/
│       └── Index.vue (4 tabs: Overview + Submit + Comments + Instructions)
└── TeamMember/
    ├── Dashboard.vue
    └── Ideas/
        └── Index.vue (3 tabs: Overview + Comments + Instructions)
```

---

## 🔄 **HACKATHON ADMIN DATA FLOWS**

### **1. HACKATHON ADMIN DASHBOARD**
**Route:** `GET /hackathon-admin/dashboard`
**Controller:** `HackathonAdmin\DashboardController@index`

**DATA SOURCES:**
```php
public function index(Request $request)
{
    $user = $request->user();
    $hackathonId = $user->managed_hackathon_id; // Get assigned hackathon
    
    // Statistics for assigned hackathon only
    $stats = [
        'participants' => [
            'registered' => $this->userService->getHackathonParticipantsCount($hackathonId),
            'teams_formed' => $this->teamService->getHackathonTeamsCount($hackathonId), 
            'ideas_submitted' => $this->ideaService->getHackathonIdeasCount($hackathonId),
        ],
        'teams' => [
            'total' => $this->teamService->getHackathonTeamsCount($hackathonId),
            'complete' => $this->teamService->getCompleteTeamsCount($hackathonId), // teams with max members
            'seeking_members' => $this->teamService->getIncompleteTeamsCount($hackathonId),
            'approved' => $this->teamService->getApprovedTeamsCount($hackathonId),
        ],
        'ideas' => [
            'submitted' => $this->ideaService->getSubmittedIdeasCount($hackathonId),
            'under_review' => $this->ideaService->getIdeasUnderReviewCount($hackathonId),
            'approved' => $this->ideaService->getApprovedIdeasCount($hackathonId),
            'needs_revision' => $this->ideaService->getIdeasNeedingRevisionCount($hackathonId),
        ],
        'workshops' => [
            'total' => $this->workshopService->getHackathonWorkshopsCount($hackathonId),
            'registrations' => $this->workshopService->getWorkshopRegistrationsCount($hackathonId),
            'attendance_rate' => $this->workshopService->getAverageAttendanceRate($hackathonId),
        ]
    ];
    
    // DB Queries:
    /*
    -- Participants Count
    SELECT COUNT(DISTINCT u.id) 
    FROM users u 
    INNER JOIN teams t ON u.id = t.leader_id OR u.id IN (
        SELECT tm.user_id FROM team_members tm WHERE tm.team_id = t.id
    )
    WHERE t.hackathon_id = ?
    
    -- Complete Teams (have max members)
    SELECT COUNT(*) FROM teams t
    INNER JOIN (
        SELECT team_id, COUNT(*) as member_count 
        FROM team_members 
        WHERE status = 'accepted' 
        GROUP BY team_id
    ) tm ON t.id = tm.team_id
    WHERE t.hackathon_id = ? AND tm.member_count = t.max_members
    
    -- Ideas by Status
    SELECT status, COUNT(*) as count
    FROM ideas i
    INNER JOIN teams t ON i.team_id = t.id
    WHERE t.hackathon_id = ?
    GROUP BY status
    */
    
    // Recent team registrations
    $recentTeams = $this->teamService->getRecentTeams($hackathonId, 10);
    
    // Pending idea reviews
    $pendingReviews = $this->ideaService->getPendingReviews($hackathonId, 5);
    
    return Inertia::render('HackathonAdmin/Dashboard', [
        'stats' => $stats,
        'recentTeams' => $recentTeams,
        'pendingReviews' => $pendingReviews,
        'hackathon' => $this->hackathonService->getHackathon($hackathonId),
    ]);
}
```

### **2. HACKATHON ADMIN TEAM MANAGEMENT**
**Route:** `GET /hackathon-admin/teams`
**Controller:** `HackathonAdmin\TeamController@index`

**DATA SOURCES:**
```php
public function index(Request $request)
{
    $user = $request->user();
    $hackathonId = $user->managed_hackathon_id;
    
    $filters = $request->validate([
        'search' => 'nullable|string|max:255',
        'status' => 'nullable|in:pending,approved,rejected',
        'track_id' => 'nullable|exists:tracks,id',
        'completion_status' => 'nullable|in:complete,incomplete,seeking_members',
        'idea_status' => 'nullable|in:no_idea,draft,submitted,under_review,approved,rejected',
        'per_page' => 'nullable|integer|min:10|max:100',
        'sort_by' => 'nullable|in:name,created_at,members_count,idea_status',
        'sort_direction' => 'nullable|in:asc,desc',
    ]);
    
    // Only teams from assigned hackathon
    $teams = $this->teamService->getHackathonTeamsWithDetails($hackathonId, [
        'with' => ['leader', 'members', 'ideas', 'track'],
        'withCount' => ['members', 'ideas'],
        'filters' => $filters,
        'paginate' => $filters['per_page'] ?? 20
    ]);
    
    // DB Query:
    /*
    SELECT t.*,
           u.name as leader_name,
           u.email as leader_email,
           u.phone as leader_phone,
           tr.name as track_name,
           COUNT(DISTINCT tm.id) as members_count,
           COUNT(DISTINCT i.id) as ideas_count,
           MAX(i.status) as idea_status,
           MAX(i.submitted_at) as idea_submitted_at
    FROM teams t
    LEFT JOIN users u ON t.leader_id = u.id
    LEFT JOIN tracks tr ON t.track_id = tr.id  
    LEFT JOIN team_members tm ON t.id = tm.team_id AND tm.status = 'accepted'
    LEFT JOIN ideas i ON t.id = i.team_id
    WHERE t.hackathon_id = ?
    AND (? IS NULL OR t.name LIKE ? OR u.name LIKE ?)
    AND (? IS NULL OR t.status = ?)
    AND (? IS NULL OR t.track_id = ?)
    GROUP BY t.id
    ORDER BY t.created_at DESC
    LIMIT ? OFFSET ?
    */
    
    // Get filter options for this hackathon
    $filterOptions = [
        'tracks' => $this->trackService->getHackathonTracks($hackathonId, ['select' => ['id', 'name']]),
        'statuses' => [
            ['value' => 'pending', 'label' => 'Pending Approval'],
            ['value' => 'approved', 'label' => 'Approved'],
            ['value' => 'rejected', 'label' => 'Rejected'],
        ],
        'completion_statuses' => [
            ['value' => 'complete', 'label' => 'Complete Teams'],
            ['value' => 'incomplete', 'label' => 'Incomplete Teams'],
            ['value' => 'seeking_members', 'label' => 'Seeking Members'],
        ],
        'idea_statuses' => [
            ['value' => 'no_idea', 'label' => 'No Idea Submitted'],
            ['value' => 'draft', 'label' => 'Draft'],
            ['value' => 'submitted', 'label' => 'Submitted'],
            ['value' => 'under_review', 'label' => 'Under Review'],
            ['value' => 'approved', 'label' => 'Approved'],
            ['value' => 'rejected', 'label' => 'Rejected'],
        ]
    ];
    
    return Inertia::render('HackathonAdmin/Teams/Index', [
        'teams' => $teams,
        'filters' => $filters,
        'filterOptions' => $filterOptions,
        'hackathon' => $this->hackathonService->getHackathon($hackathonId),
        'can' => [
            'approve_teams' => true,
            'reject_teams' => true,
            'assign_tracks' => true,
            'send_notifications' => true,
        ]
    ]);
}
```

---

## 🎯 **COMPLETE COMPONENT REUSE IMPLEMENTATION**

### **Task 1: Update NavSidebarDesktop.vue for Role-Based Menus**

**File:** `resources/js/Components/NavSidebarDesktop.vue`
**Lines to Modify:** 47-111 (replace navigationSections)

<function_calls>
<invoke name="TodoWrite">
<parameter name="todos">[{"content": "Analyze existing layout components for reuse", "status": "completed", "activeForm": "Analyzing existing layout components for reuse"}, {"content": "Map existing header/sidebar components to roles", "status": "completed", "activeForm": "Mapping existing header/sidebar components to roles"}, {"content": "Detail every admin flow with exact data sources", "status": "completed", "activeForm": "Detailing every admin flow with exact data sources"}, {"content": "Map all API endpoints to page interactions", "status": "completed", "activeForm": "Mapping all API endpoints to page interactions"}, {"content": "Create ultra-detailed component reuse plan", "status": "completed", "activeForm": "Creating ultra-detailed component reuse plan"}]