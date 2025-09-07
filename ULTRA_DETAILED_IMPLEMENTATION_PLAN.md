# 🚀 **ULTRA-DETAILED IMPLEMENTATION PLAN - COMPREHENSIVE MERGED VERSION**
**Complete Hackathon Management System with Enhanced Role-Based Frontend Behavior**

## 🚨 **CRITICAL ENHANCEMENTS APPLIED**

✅ **Complete Request Classes**: All controllers use specific validation classes  
✅ **Enhanced Frontend Behavior**: Ultra-detailed role-based UI specifications  
✅ **Seeder Coverage**: Complete seeder documentation with sample data  
✅ **Role-Based Flows**: Exact navigation, permissions, and component behavior per role  
✅ **Implementation Ready**: One-shot implementation with precise technical specifications  

---

## 📁 **VERIFIED DIRECTORY STRUCTURE**

```
✅ EXISTING (VERIFIED):
/home/geek/projects/hakathons/projects/guacpanel-tailwind-1.14/
├── app/Http/Controllers/ ✅
├── app/Models/ ✅
├── database/migrations/ ✅ (35 migrations exist)
├── database/seeders/ ✅ (20 seeders exist)
├── resources/js/Components/ ✅
├── resources/js/Layouts/ ✅
│   └── Default.vue ✅ (Main layout with NavSidebarDesktop)
├── resources/js/Pages/ ✅
└── routes/ ✅

❌ TO CREATE:
├── app/Http/Controllers/SystemAdmin/ ❌
├── app/Http/Controllers/HackathonAdmin/ ❌
├── app/Http/Controllers/TrackSupervisor/ ❌
├── app/Http/Controllers/TeamLeader/ ❌
├── app/Http/Controllers/TeamMember/ ❌
├── app/Http/Controllers/Public/ ❌
├── app/Http/Requests/ ❌ (CRITICAL - All validation classes)
├── app/Services/ ❌
├── resources/js/Pages/SystemAdmin/ ❌
├── resources/js/Pages/HackathonAdmin/ ❌
└── resources/js/Components/Public/ ❌
```

## 🏗️ **SYSTEM ARCHITECTURE**

### **🎯 ROLE-BASED ACCESS SYSTEM (5 ROLES)**
```
1. system_admin        - Full system control, edition management
2. hackathon_admin     - Current edition management, track oversight  
3. track_supervisor    - Track-specific team/idea oversight
4. team_leader         - Team & idea management
5. team_member         - Basic participation, view-only
```

### **📱 FRONTEND STRUCTURE**
```
🌐 DUAL ARCHITECTURE:
├── PUBLIC SITE (ruman.sa) - WordPress + Elementor
│   ├── Landing pages (SRS F1-F5)
│   └── Public workshop registration (SRS F21-F28)
└── ADMIN PANEL (app.ruman.sa) - Laravel + Vue + Inertia
    ├── 5 Role-based dashboards
    ├── QR code scanning system
    └── Twitter integration
```

---

## 🔐 **ENHANCED ROLE-BASED FRONTEND BEHAVIOR**

### **🔴 SYSTEM ADMIN ROLE (system_admin)**
**Access Level:** Full system control + multi-edition management

#### **Login Flow & Initial Landing:**
```
1. POST /login → validates credentials
2. Middleware: auth, role:system_admin 
3. HandleInertiaRequests shares: user.roles = ['system_admin']
4. REDIRECT: /system-admin/dashboard
5. NavSidebarDesktop renders: Full system admin menu
```

#### **Sidebar Navigation (NavSidebarDesktop.vue modifications):**
```javascript
// When user.roles includes 'system_admin':
const systemAdminMenu = [
  {
    items: [
      { name: 'Dashboard', route: 'system-admin.dashboard', icon: dashboardIcon },
      { type: 'divider' }
    ]
  },
  {
    items: [
      { name: 'Hackathon Editions', route: 'system-admin.editions.index', icon: editionsIcon },
      { name: 'Users Management', route: 'system-admin.users.index', icon: usersIcon },
      { name: 'All Teams', route: 'system-admin.teams.index', icon: teamsIcon },
      { name: 'All Ideas', route: 'system-admin.ideas.index', icon: ideasIcon },
      { name: 'All Workshops', route: 'system-admin.workshops.index', icon: workshopsIcon },
      { name: 'News Management', route: 'system-admin.news.index', icon: newsIcon },
      { name: 'Reports & Analytics', route: 'system-admin.reports.index', icon: reportsIcon },
      { type: 'divider' }
    ]
  },
  {
    items: [
      { 
        name: 'System Settings', 
        icon: settingsIcon,
        children: [
          { name: 'SMTP Configuration', route: 'system-admin.settings.smtp' },
          { name: 'Twitter Integration', route: 'system-admin.settings.twitter' },
          { name: 'Branding Settings', route: 'system-admin.settings.branding' },
          { name: 'System Health', route: 'system-admin.health.index' }
        ]
      }
    ]
  }
]
```

#### **Dashboard Component Behavior:**
```vue
<!-- SystemAdmin/Dashboard/Index.vue -->
<template>
  <Default>
    <!-- System-wide statistics visible -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <MetricWidget title="Total Editions" :value="stats.editions.total" color="blue" />
      <MetricWidget title="Active Editions" :value="stats.editions.active" color="green" />
      <MetricWidget title="Total Users" :value="stats.users.total" color="purple" />
      <MetricWidget title="System Health" :value="stats.health.score + '%'" color="cyan" />
    </div>
    
    <!-- Edition Switcher (UNIQUE TO SYSTEM ADMIN) -->
    <div class="bg-white rounded-lg shadow p-6">
      <h3 class="text-lg font-medium mb-4">Edition Management</h3>
      <FormSelect
        v-model="selectedEdition" 
        :options="editionOptions"
        label="Switch Active Edition"
        @change="switchEdition"
      />
      <div class="mt-4 flex space-x-3">
        <Link :href="route('system-admin.editions.create')" class="btn-primary">
          Create New Edition
        </Link>
        <Link :href="route('system-admin.editions.index')" class="btn-secondary">
          Manage All Editions
        </Link>
      </div>
    </div>
    
    <!-- Global Activity Feed -->
    <Datatable 
      title="System-wide Activity"
      :data="activities"
      :columns="activityColumns"
      :enable-export="true"
    />
  </Default>
</template>
```

#### **Page Access Control:**
```php
// Route middleware for system admin pages
Route::middleware(['auth', 'role:system_admin'])->group(function () {
    Route::get('/system-admin/dashboard', [SystemAdmin\DashboardController::class, 'index'])
        ->name('system-admin.dashboard');
    Route::resource('/system-admin/editions', SystemAdmin\HackathonEditionController::class)
        ->names('system-admin.editions');
    Route::resource('/system-admin/users', SystemAdmin\UserController::class)
        ->names('system-admin.users');
});
```

### **🟡 HACKATHON ADMIN ROLE (hackathon_admin)**
**Access Level:** Current edition management + track oversight

#### **Login Flow & Initial Landing:**
```
1. POST /login → validates credentials
2. Middleware: auth, role:hackathon_admin
3. HandleInertiaRequests shares: user.roles = ['hackathon_admin'], current_edition
4. REDIRECT: /hackathon-admin/dashboard
5. NavSidebarDesktop renders: Current edition focused menu
```

#### **Sidebar Navigation:**
```javascript
// When user.roles includes 'hackathon_admin':
const hackathonAdminMenu = [
  {
    items: [
      { name: 'Dashboard', route: 'hackathon-admin.dashboard', icon: dashboardIcon },
      { name: 'Current Edition Overview', route: 'hackathon-admin.edition.show', icon: editionIcon },
      { type: 'divider' }
    ]
  },
  {
    items: [
      { name: 'Teams Management', route: 'hackathon-admin.teams.index', icon: teamsIcon },
      { name: 'Ideas Review', route: 'hackathon-admin.ideas.index', icon: ideasIcon },
      { name: 'Track Supervision', route: 'hackathon-admin.tracks.index', icon: tracksIcon },
      { name: 'Workshops', route: 'hackathon-admin.workshops.index', icon: workshopsIcon },
      { name: 'News & Updates', route: 'hackathon-admin.news.index', icon: newsIcon },
      { type: 'divider' }
    ]
  },
  {
    items: [
      { name: 'Reports', route: 'hackathon-admin.reports.index', icon: reportsIcon },
      { name: 'Export Data', route: 'hackathon-admin.export.index', icon: exportIcon }
    ]
  }
]
```

#### **Dashboard Component Behavior:**
```vue
<!-- HackathonAdmin/Dashboard/Index.vue -->
<template>
  <Default>
    <!-- Current Edition Header (UNIQUE TO HACKATHON ADMIN) -->
    <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg shadow p-6 text-white mb-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold">{{ currentEdition.name }}</h1>
          <p class="text-blue-100">{{ currentEdition.year }} • {{ currentEdition.theme }}</p>
        </div>
        <div class="text-right">
          <div class="text-sm text-blue-100">Days Remaining</div>
          <div class="text-3xl font-bold">{{ daysUntilDeadline }}</div>
        </div>
      </div>
    </div>
    
    <!-- Current Edition Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <MetricWidget 
        title="Teams Registered" 
        :value="stats.teams.registered" 
        :subtitle="`${stats.teams.pending} pending approval`"
        color="blue" 
      />
      <MetricWidget 
        title="Ideas Submitted" 
        :value="stats.ideas.submitted" 
        :subtitle="`${stats.ideas.under_review} under review`"
        color="green" 
      />
      <MetricWidget 
        title="Workshop Registrations" 
        :value="stats.workshops.registrations" 
        :subtitle="`${stats.workshops.upcoming} upcoming`"
        color="purple" 
      />
      <MetricWidget 
        title="Track Progress" 
        :value="stats.tracks.active + '/' + stats.tracks.total" 
        subtitle="tracks active"
        color="cyan" 
      />
    </div>
    
    <!-- Quick Actions Panel -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium mb-4">Pending Approvals</h3>
        <div class="space-y-2">
          <div class="flex justify-between">
            <span>Teams waiting approval</span>
            <span class="font-medium text-yellow-600">{{ stats.teams.pending }}</span>
          </div>
          <Link :href="route('hackathon-admin.teams.index', { status: 'pending' })" 
                class="btn-primary btn-sm w-full">
            Review Teams
          </Link>
        </div>
      </div>
      
      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium mb-4">Ideas to Review</h3>
        <div class="space-y-2">
          <div class="flex justify-between">
            <span>Awaiting review</span>
            <span class="font-medium text-blue-600">{{ stats.ideas.under_review }}</span>
          </div>
          <Link :href="route('hackathon-admin.ideas.index', { status: 'under_review' })" 
                class="btn-primary btn-sm w-full">
            Review Ideas
          </Link>
        </div>
      </div>
    </div>
  </Default>
</template>
```

#### **Teams Management Page Behavior:**
```vue
<!-- HackathonAdmin/Teams/Index.vue -->
<template>
  <Default>
    <PageHeader title="Teams Management">
      <template #actions>
        <!-- Bulk Action Buttons (VISIBLE ONLY TO HACKATHON ADMIN) -->
        <div v-if="selectedTeams.length > 0" class="flex space-x-2">
          <button @click="bulkApprove" class="btn-success btn-sm">
            Approve Selected ({{ selectedTeams.length }})
          </button>
          <button @click="bulkReject" class="btn-warning btn-sm">
            Reject Selected
          </button>
        </div>
        
        <!-- Filter by Current Edition (AUTO-FILTERED) -->
        <div class="text-sm text-gray-600">
          Showing teams for: {{ currentEdition.name }}
        </div>
      </template>
    </PageHeader>
    
    <!-- Enhanced Datatable with Bulk Actions -->
    <Datatable 
      :data="teams.data"
      :columns="teamColumns"
      :enable-bulk-select="true"
      :bulk-actions="bulkActions"
      v-model:selected="selectedTeams"
      @bulk-action="handleBulkAction"
    >
      <!-- Custom Status Column -->
      <template #status="{ row }">
        <div class="flex items-center space-x-2">
          <StatusBadge :status="row.status" />
          <!-- Quick Action Buttons -->
          <button v-if="row.status === 'pending'" 
                  @click="quickApprove(row.id)"
                  class="text-green-600 hover:text-green-800 text-xs">
            ✓ Approve
          </button>
          <button v-if="row.status === 'pending'" 
                  @click="quickReject(row.id)"
                  class="text-red-600 hover:text-red-800 text-xs">
            ✗ Reject
          </button>
        </div>
      </template>
    </Datatable>
  </Default>
</template>
```

### **🟢 TRACK SUPERVISOR ROLE (track_supervisor)**
**Access Level:** Assigned track(s) only

#### **Login Flow & Initial Landing:**
```
1. POST /login → validates credentials
2. Middleware: auth, role:track_supervisor
3. HandleInertiaRequests shares: user.roles = ['track_supervisor'], assigned_tracks
4. REDIRECT: /track-supervisor/dashboard
5. NavSidebarDesktop renders: Track-specific menu
```

#### **Sidebar Navigation:**
```javascript
// When user.roles includes 'track_supervisor':
const trackSupervisorMenu = [
  {
    items: [
      { name: 'My Track Dashboard', route: 'track-supervisor.dashboard', icon: dashboardIcon },
      { type: 'divider' }
    ]
  },
  {
    items: [
      { name: 'Ideas to Review', route: 'track-supervisor.ideas.index', icon: ideasIcon },
      { name: 'My Track Teams', route: 'track-supervisor.teams.index', icon: teamsIcon },
      { name: 'Track Workshops', route: 'track-supervisor.workshops.index', icon: workshopsIcon },
      { name: 'Track Statistics', route: 'track-supervisor.stats.index', icon: chartIcon },
      { type: 'divider' }
    ]
  },
  {
    items: [
      { name: 'Track Settings', route: 'track-supervisor.track.edit', icon: settingsIcon }
    ]
  }
]
```

#### **Dashboard Component Behavior:**
```vue
<!-- TrackSupervisor/Dashboard/Index.vue -->
<template>
  <Default>
    <!-- Track Information Header (TRACK-SPECIFIC) -->
    <div class="bg-gradient-to-r from-green-500 to-blue-500 rounded-lg shadow p-6 text-white mb-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold">{{ assignedTrack.name }}</h1>
          <p class="text-green-100">{{ assignedTrack.description }}</p>
        </div>
        <div class="text-right">
          <div class="text-sm text-green-100">Your Teams</div>
          <div class="text-3xl font-bold">{{ stats.teams.total }}</div>
        </div>
      </div>
    </div>
    
    <!-- Track-Specific Statistics (DATA RESTRICTED TO ASSIGNED TRACK) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <MetricWidget 
        title="Teams in My Track" 
        :value="stats.teams.total" 
        :subtitle="`${stats.teams.approved} approved`"
        color="green" 
      />
      <MetricWidget 
        title="Ideas to Review" 
        :value="stats.ideas.pending_my_review" 
        subtitle="awaiting your review"
        color="yellow" 
      />
      <MetricWidget 
        title="Ideas Reviewed" 
        :value="stats.ideas.reviewed_by_me" 
        :subtitle="`${stats.ideas.approved_by_me} approved`"
        color="blue" 
      />
      <MetricWidget 
        title="Average Score" 
        :value="stats.ideas.average_score + '/100'" 
        subtitle="ideas I reviewed"
        color="purple" 
      />
    </div>
    
    <!-- Ideas Requiring Review (FILTERED TO ASSIGNED TRACK ONLY) -->
    <div class="bg-white rounded-lg shadow">
      <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-medium">Ideas Awaiting Your Review</h3>
        <p class="text-sm text-gray-600 mt-1">
          Only showing ideas from {{ assignedTrack.name }} track
        </p>
      </div>
      
      <div class="divide-y divide-gray-200">
        <div v-for="idea in pendingIdeas" :key="idea.id" 
             class="p-6 hover:bg-gray-50 flex items-center justify-between">
          <div>
            <h4 class="font-medium">{{ idea.title }}</h4>
            <p class="text-sm text-gray-600">Team: {{ idea.team.name }}</p>
            <p class="text-xs text-gray-500">
              Submitted: {{ formatDate(idea.submitted_at) }}
            </p>
          </div>
          <Link :href="route('track-supervisor.ideas.review', idea.id)" 
                class="btn-primary btn-sm">
            Review Now
          </Link>
        </div>
      </div>
    </div>
  </Default>
</template>
```

#### **Ideas Review Page (DATA RESTRICTION):**
```vue
<!-- TrackSupervisor/Ideas/Index.vue -->
<template>
  <Default>
    <PageHeader :title="`${assignedTrack.name} - Ideas Review`">
      <template #subtitle>
        Only showing ideas submitted to your assigned track
      </template>
    </PageHeader>
    
    <!-- IDEAS ARE AUTO-FILTERED BY BACKEND TO ASSIGNED TRACK ONLY -->
    <Datatable 
      :data="ideas.data"
      :columns="reviewColumns"
      title="Ideas to Review"
      :filters="trackSpecificFilters"
    >
      <!-- Custom Review Actions Column -->
      <template #actions="{ row }">
        <div class="flex space-x-2">
          <Link :href="route('track-supervisor.ideas.show', row.id)"
                class="text-blue-600 hover:text-blue-800 text-sm">
            View Details
          </Link>
          <Link v-if="row.status === 'submitted'" 
                :href="route('track-supervisor.ideas.review', row.id)"
                class="text-green-600 hover:text-green-800 text-sm">
            Review
          </Link>
          <span v-if="row.my_review_status" 
                class="text-xs px-2 py-1 rounded-full bg-blue-100 text-blue-800">
            {{ row.my_review_status }}
          </span>
        </div>
      </template>
    </Datatable>
  </Default>
</template>
```

### **🔵 TEAM LEADER ROLE (team_leader)**
**Access Level:** Own team + idea management

#### **Login Flow & Initial Landing:**
```
1. POST /login → validates credentials
2. Middleware: auth, role:team_leader
3. HandleInertiaRequests shares: user.roles = ['team_leader'], my_team
4. REDIRECT: /team-leader/dashboard
5. NavSidebarDesktop renders: Team management focused menu
```

#### **Sidebar Navigation:**
```javascript
// When user.roles includes 'team_leader':
const teamLeaderMenu = [
  {
    items: [
      { name: 'Dashboard', route: 'team-leader.dashboard', icon: dashboardIcon },
      { type: 'divider' }
    ]
  },
  {
    items: [
      { name: 'My Team', route: 'team-leader.team.show', icon: teamIcon },
      { name: 'Our Idea', route: 'team-leader.idea.show', icon: ideaIcon },
      { name: 'Team Members', route: 'team-leader.members.index', icon: membersIcon },
      { type: 'divider' }
    ]
  },
  {
    items: [
      { name: 'Available Workshops', route: 'team-leader.workshops.index', icon: workshopsIcon },
      { name: 'Track Information', route: 'team-leader.track.show', icon: trackIcon },
      { name: 'News & Updates', route: 'team-leader.news.index', icon: newsIcon }
    ]
  }
]
```

#### **Dashboard Component Behavior:**
```vue
<!-- TeamLeader/Dashboard/Index.vue -->
<template>
  <Default>
    <!-- Team Status Header (TEAM-SPECIFIC) -->
    <div v-if="myTeam" class="bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg shadow p-6 text-white mb-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold">{{ myTeam.name }}</h1>
          <p class="text-purple-100">{{ myTeam.track.name }} Track</p>
          <StatusBadge :status="myTeam.status" class="mt-2" />
        </div>
        <div class="text-right">
          <div class="text-sm text-purple-100">Team Members</div>
          <div class="text-3xl font-bold">{{ myTeam.members_count }}/{{ myTeam.max_members }}</div>
        </div>
      </div>
    </div>
    
    <!-- No Team Warning -->
    <div v-else class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-6">
      <div class="flex items-center">
        <svg class="w-8 h-8 text-yellow-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z" />
        </svg>
        <div>
          <h3 class="text-lg font-medium text-yellow-800">No Team Found</h3>
          <p class="text-yellow-700">You need to create or join a team to participate.</p>
          <Link :href="route('team-leader.team.create')" class="btn-primary mt-3">
            Create New Team
          </Link>
        </div>
      </div>
    </div>
    
    <!-- Team Progress Cards (ONLY VISIBLE IF HAS TEAM) -->
    <div v-if="myTeam" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <!-- Team Status Card -->
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-medium">Team Status</h3>
          <StatusIcon :status="myTeam.status" />
        </div>
        <div class="space-y-2">
          <div class="flex justify-between text-sm">
            <span>Registration Status:</span>
            <StatusBadge :status="myTeam.status" />
          </div>
          <div class="flex justify-between text-sm">
            <span>Members:</span>
            <span>{{ myTeam.members_count }}/{{ myTeam.max_members }}</span>
          </div>
          <div class="flex justify-between text-sm">
            <span>Join Code:</span>
            <code class="bg-gray-100 px-2 py-1 rounded">{{ myTeam.join_code }}</code>
          </div>
        </div>
        <Link :href="route('team-leader.team.edit')" class="btn-primary btn-sm w-full mt-4">
          Manage Team
        </Link>
      </div>
      
      <!-- Idea Submission Card -->
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-medium">Our Idea</h3>
          <IdeaStatusIcon :status="idea?.status" />
        </div>
        <div v-if="idea">
          <h4 class="font-medium">{{ idea.title }}</h4>
          <StatusBadge :status="idea.status" class="mt-2" />
          <div class="mt-4 space-y-2">
            <Link :href="route('team-leader.idea.edit')" class="btn-secondary btn-sm w-full">
              Edit Idea
            </Link>
            <Link v-if="idea.status === 'draft'" 
                  :href="route('team-leader.idea.submit')" 
                  class="btn-primary btn-sm w-full">
              Submit for Review
            </Link>
          </div>
        </div>
        <div v-else>
          <p class="text-gray-600 text-sm mb-4">No idea submitted yet</p>
          <Link :href="route('team-leader.idea.create')" class="btn-primary btn-sm w-full">
            Submit Our Idea
          </Link>
        </div>
      </div>
      
      <!-- Workshop Registration Card -->
      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium mb-4">Workshop Registration</h3>
        <div class="space-y-2 text-sm">
          <div class="flex justify-between">
            <span>Registered:</span>
            <span class="font-medium">{{ stats.workshops.registered }}</span>
          </div>
          <div class="flex justify-between">
            <span>Upcoming:</span>
            <span class="font-medium">{{ stats.workshops.upcoming }}</span>
          </div>
        </div>
        <Link :href="route('team-leader.workshops.index')" class="btn-primary btn-sm w-full mt-4">
          Browse Workshops
        </Link>
      </div>
    </div>
    
    <!-- Deadlines and Timeline (HACKATHON SPECIFIC) -->
    <div v-if="myTeam" class="bg-white rounded-lg shadow p-6">
      <h3 class="text-lg font-medium mb-4">Important Deadlines</h3>
      <div class="space-y-4">
        <div v-for="deadline in upcomingDeadlines" :key="deadline.type" 
             class="flex items-center justify-between p-3 border rounded-lg"
             :class="deadline.urgent ? 'border-red-200 bg-red-50' : 'border-gray-200'">
          <div>
            <h4 class="font-medium" :class="deadline.urgent ? 'text-red-800' : ''">
              {{ deadline.title }}
            </h4>
            <p class="text-sm text-gray-600">{{ deadline.description }}</p>
          </div>
          <div class="text-right">
            <div class="font-medium" :class="deadline.urgent ? 'text-red-600' : 'text-gray-900'">
              {{ deadline.remaining_days }} days
            </div>
            <div class="text-xs text-gray-500">{{ formatDate(deadline.date) }}</div>
          </div>
        </div>
      </div>
    </div>
  </Default>
</template>
```

#### **Team Management Page:**
```vue
<!-- TeamLeader/Team/Show.vue -->
<template>
  <Default>
    <PageHeader :title="myTeam.name" :subtitle="`${myTeam.track.name} Track`">
      <template #actions>
        <Link :href="route('team-leader.team.edit')" class="btn-primary">
          Edit Team Details
        </Link>
      </template>
    </PageHeader>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Team Information -->
      <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow p-6 mb-6">
          <h3 class="text-lg font-medium mb-4">Team Information</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Team Name</label>
              <p class="mt-1 text-sm text-gray-900">{{ myTeam.name }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Join Code</label>
              <div class="mt-1 flex items-center space-x-2">
                <code class="bg-gray-100 px-3 py-1 rounded text-sm">{{ myTeam.join_code }}</code>
                <button @click="copyJoinCode" class="text-blue-600 hover:text-blue-800 text-sm">
                  Copy
                </button>
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Track</label>
              <p class="mt-1 text-sm text-gray-900">{{ myTeam.track.name }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Status</label>
              <StatusBadge :status="myTeam.status" class="mt-1" />
            </div>
          </div>
          
          <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700">Description</label>
            <p class="mt-1 text-sm text-gray-900">{{ myTeam.description || 'No description provided' }}</p>
          </div>
        </div>
        
        <!-- Team Members Management -->
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium">Team Members ({{ myTeam.members_count }}/{{ myTeam.max_members }})</h3>
            <button v-if="myTeam.members_count < myTeam.max_members" 
                    @click="showInviteModal = true" 
                    class="btn-primary btn-sm">
              Invite Member
            </button>
          </div>
          
          <div class="space-y-3">
            <!-- Team Leader (You) -->
            <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
              <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-medium">
                  {{ user.name.charAt(0) }}
                </div>
                <div>
                  <p class="font-medium">{{ user.name }} (You)</p>
                  <p class="text-sm text-gray-600">{{ user.email }}</p>
                </div>
              </div>
              <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">Team Leader</span>
            </div>
            
            <!-- Team Members -->
            <div v-for="member in myTeam.members" :key="member.id" 
                 class="flex items-center justify-between p-3 border rounded-lg">
              <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-gray-400 text-white rounded-full flex items-center justify-center text-sm font-medium">
                  {{ member.name.charAt(0) }}
                </div>
                <div>
                  <p class="font-medium">{{ member.name }}</p>
                  <p class="text-sm text-gray-600">{{ member.email }}</p>
                </div>
              </div>
              <div class="flex items-center space-x-2">
                <StatusBadge :status="member.status" />
                <button v-if="member.status === 'invited'" 
                        @click="resendInvitation(member.id)"
                        class="text-blue-600 hover:text-blue-800 text-xs">
                  Resend
                </button>
                <button @click="removeMember(member.id)" 
                        class="text-red-600 hover:text-red-800 text-xs">
                  Remove
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Side Panel -->
      <div>
        <!-- Next Steps Card -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
          <h3 class="text-lg font-medium mb-4">Next Steps</h3>
          <div class="space-y-3">
            <div v-for="step in nextSteps" :key="step.id" 
                 class="flex items-start space-x-3">
              <div class="flex-shrink-0 w-5 h-5 rounded-full border-2 mt-1"
                   :class="step.completed ? 'bg-green-500 border-green-500' : 'border-gray-300'">
                <svg v-if="step.completed" class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
              </div>
              <div>
                <p class="text-sm font-medium" :class="step.completed ? 'text-gray-500' : ''">
                  {{ step.title }}
                </p>
                <p class="text-xs text-gray-600">{{ step.description }}</p>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-lg font-medium mb-4">Quick Actions</h3>
          <div class="space-y-2">
            <Link :href="route('team-leader.idea.create')" 
                  class="btn-secondary btn-sm w-full text-left">
              Submit Our Idea
            </Link>
            <Link :href="route('team-leader.workshops.index')" 
                  class="btn-secondary btn-sm w-full text-left">
              Register for Workshops
            </Link>
            <Link :href="route('team-leader.track.show')" 
                  class="btn-secondary btn-sm w-full text-left">
              View Track Details
            </Link>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Invite Member Modal -->
    <Modal v-model="showInviteModal" title="Invite Team Member">
      <form @submit.prevent="inviteMember">
        <FormInput
          v-model="inviteForm.email"
          label="Email Address"
          type="email"
          required
          placeholder="member@university.edu"
          :error="inviteForm.errors.email"
        />
        <FormTextarea
          v-model="inviteForm.message"
          label="Personal Message (Optional)"
          rows="3"
          placeholder="Hi! I'd like to invite you to join our hackathon team..."
        />
        <div class="flex justify-end space-x-3 mt-6">
          <button type="button" @click="showInviteModal = false" class="btn-secondary">
            Cancel
          </button>
          <button type="submit" class="btn-primary" :disabled="inviteForm.processing">
            Send Invitation
          </button>
        </div>
      </form>
    </Modal>
  </Default>
</template>
```

### **🟣 TEAM MEMBER ROLE (team_member)**
**Access Level:** View-only participation

#### **Login Flow & Initial Landing:**
```
1. POST /login → validates credentials
2. Middleware: auth, role:team_member
3. HandleInertiaRequests shares: user.roles = ['team_member'], my_team
4. REDIRECT: /team-member/dashboard
5. NavSidebarDesktop renders: Limited access menu
```

#### **Sidebar Navigation:**
```javascript
// When user.roles includes 'team_member':
const teamMemberMenu = [
  {
    items: [
      { name: 'Dashboard', route: 'team-member.dashboard', icon: dashboardIcon },
      { type: 'divider' }
    ]
  },
  {
    items: [
      { name: 'My Team', route: 'team-member.team.show', icon: teamIcon },
      { name: 'Our Idea', route: 'team-member.idea.show', icon: ideaIcon },
      { type: 'divider' }
    ]
  },
  {
    items: [
      { name: 'Workshops', route: 'team-member.workshops.index', icon: workshopsIcon },
      { name: 'Track Info', route: 'team-member.track.show', icon: trackIcon },
      { name: 'News', route: 'team-member.news.index', icon: newsIcon }
    ]
  }
]
```

#### **Dashboard Component Behavior:**
```vue
<!-- TeamMember/Dashboard/Index.vue -->
<template>
  <Default>
    <!-- Team Overview (READ-ONLY) -->
    <div v-if="myTeam" class="bg-gradient-to-r from-gray-500 to-gray-600 rounded-lg shadow p-6 text-white mb-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold">{{ myTeam.name }}</h1>
          <p class="text-gray-100">{{ myTeam.track.name }} Track</p>
          <p class="text-sm text-gray-200">Team Member</p>
        </div>
        <div class="text-right">
          <div class="text-sm text-gray-100">Team Leader</div>
          <div class="text-lg font-medium">{{ myTeam.leader.name }}</div>
        </div>
      </div>
    </div>
    
    <!-- No Team Message -->
    <div v-else class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
      <div class="flex items-center">
        <svg class="w-8 h-8 text-blue-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
        <div>
          <h3 class="text-lg font-medium text-blue-800">No Team Yet</h3>
          <p class="text-blue-700">You haven't joined a team yet. Ask a team leader for an invitation!</p>
        </div>
      </div>
    </div>
    
    <!-- Read-Only Information Cards -->
    <div v-if="myTeam" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <!-- Team Status (READ-ONLY) -->
      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium mb-4">Team Status</h3>
        <div class="space-y-3">
          <div class="flex justify-between text-sm">
            <span>Status:</span>
            <StatusBadge :status="myTeam.status" />
          </div>
          <div class="flex justify-between text-sm">
            <span>Members:</span>
            <span>{{ myTeam.members_count }}/{{ myTeam.max_members }}</span>
          </div>
          <div class="flex justify-between text-sm">
            <span>Track:</span>
            <span>{{ myTeam.track.name }}</span>
          </div>
        </div>
        <!-- NO EDIT BUTTON - READ ONLY -->
        <Link :href="route('team-member.team.show')" class="btn-secondary btn-sm w-full mt-4">
          View Team Details
        </Link>
      </div>
      
      <!-- Idea Status (READ-ONLY) -->
      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium mb-4">Our Idea</h3>
        <div v-if="idea">
          <h4 class="font-medium text-sm">{{ idea.title }}</h4>
          <StatusBadge :status="idea.status" class="mt-2" />
          <p class="text-xs text-gray-600 mt-2">{{ idea.description.substring(0, 100) }}...</p>
          <!-- NO EDIT BUTTON - READ ONLY -->
          <Link :href="route('team-member.idea.show')" class="btn-secondary btn-sm w-full mt-4">
            View Idea Details
          </Link>
        </div>
        <div v-else>
          <p class="text-gray-600 text-sm">No idea submitted yet</p>
          <p class="text-xs text-gray-500 mt-2">Ask your team leader to submit an idea</p>
        </div>
      </div>
      
      <!-- Workshop Registration (FULL ACCESS) -->
      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium mb-4">My Workshop Registrations</h3>
        <div class="space-y-2 text-sm">
          <div class="flex justify-between">
            <span>Registered:</span>
            <span class="font-medium">{{ stats.workshops.registered }}</span>
          </div>
          <div class="flex justify-between">
            <span>Attended:</span>
            <span class="font-medium">{{ stats.workshops.attended }}</span>
          </div>
          <div class="flex justify-between">
            <span>Upcoming:</span>
            <span class="font-medium">{{ stats.workshops.upcoming }}</span>
          </div>
        </div>
        <!-- CAN REGISTER FOR WORKSHOPS -->
        <Link :href="route('team-member.workshops.index')" class="btn-primary btn-sm w-full mt-4">
          Browse & Register
        </Link>
      </div>
    </div>
    
    <!-- Upcoming Workshops (ACCESSIBLE TO TEAM MEMBERS) -->
    <div v-if="upcomingWorkshops.length > 0" class="bg-white rounded-lg shadow p-6">
      <h3 class="text-lg font-medium mb-4">Upcoming Workshops</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div v-for="workshop in upcomingWorkshops" :key="workshop.id" 
             class="border rounded-lg p-4 hover:border-blue-300">
          <div class="flex justify-between items-start mb-2">
            <h4 class="font-medium text-sm">{{ workshop.title }}</h4>
            <span class="text-xs text-gray-500">{{ formatDate(workshop.start_datetime) }}</span>
          </div>
          <p class="text-xs text-gray-600 mb-3">{{ workshop.description }}</p>
          <div class="flex justify-between items-center">
            <span class="text-xs text-gray-500">
              {{ workshop.registrations_count }}/{{ workshop.max_attendees }} registered
            </span>
            <button v-if="!workshop.is_registered" 
                    @click="registerForWorkshop(workshop.id)"
                    class="btn-primary btn-xs">
              Register
            </button>
            <span v-else class="text-xs text-green-600 font-medium">Registered</span>
          </div>
        </div>
      </div>
    </div>
  </Default>
</template>
```

#### **Team View Page (READ-ONLY):**
```vue
<!-- TeamMember/Team/Show.vue -->
<template>
  <Default>
    <PageHeader :title="myTeam.name" :subtitle="`${myTeam.track.name} Track - Team Member View`">
      <!-- NO EDIT ACTIONS FOR TEAM MEMBERS -->
    </PageHeader>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Team Information (READ-ONLY) -->
      <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow p-6 mb-6">
          <h3 class="text-lg font-medium mb-4">Team Information</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Team Name</label>
              <p class="mt-1 text-sm text-gray-900">{{ myTeam.name }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Track</label>
              <p class="mt-1 text-sm text-gray-900">{{ myTeam.track.name }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Status</label>
              <StatusBadge :status="myTeam.status" class="mt-1" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Members</label>
              <p class="mt-1 text-sm text-gray-900">{{ myTeam.members_count }}/{{ myTeam.max_members }}</p>
            </div>
          </div>
          
          <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700">Description</label>
            <p class="mt-1 text-sm text-gray-900">{{ myTeam.description || 'No description provided' }}</p>
          </div>
        </div>
        
        <!-- Team Members (VIEW-ONLY) -->
        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-lg font-medium mb-4">Team Members</h3>
          
          <div class="space-y-3">
            <!-- Team Leader -->
            <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
              <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-medium">
                  {{ myTeam.leader.name.charAt(0) }}
                </div>
                <div>
                  <p class="font-medium">{{ myTeam.leader.name }}</p>
                  <p class="text-sm text-gray-600">{{ myTeam.leader.email }}</p>
                </div>
              </div>
              <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">Team Leader</span>
            </div>
            
            <!-- Team Members (including current user) -->
            <div v-for="member in myTeam.members" :key="member.id" 
                 class="flex items-center justify-between p-3 border rounded-lg"
                 :class="member.id === user.id ? 'bg-green-50 border-green-200' : ''">
              <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-gray-400 text-white rounded-full flex items-center justify-center text-sm font-medium">
                  {{ member.name.charAt(0) }}
                </div>
                <div>
                  <p class="font-medium">
                    {{ member.name }}
                    <span v-if="member.id === user.id" class="text-green-600">(You)</span>
                  </p>
                  <p class="text-sm text-gray-600">{{ member.email }}</p>
                </div>
              </div>
              <StatusBadge :status="member.status" />
            </div>
          </div>
        </div>
      </div>
      
      <!-- Side Panel (LIMITED ACTIONS) -->
      <div>
        <!-- Contact Team Leader -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
          <h3 class="text-lg font-medium mb-4">Contact Team Leader</h3>
          <div class="flex items-center space-x-3 mb-4">
            <div class="w-10 h-10 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-medium">
              {{ myTeam.leader.name.charAt(0) }}
            </div>
            <div>
              <p class="font-medium">{{ myTeam.leader.name }}</p>
              <p class="text-sm text-gray-600">{{ myTeam.leader.email }}</p>
            </div>
          </div>
          <a :href="`mailto:${myTeam.leader.email}`" class="btn-primary btn-sm w-full">
            Send Email
          </a>
        </div>
        
        <!-- Available Actions -->
        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-lg font-medium mb-4">Available Actions</h3>
          <div class="space-y-2">
            <Link :href="route('team-member.idea.show')" 
                  class="btn-secondary btn-sm w-full text-left">
              View Our Idea
            </Link>
            <Link :href="route('team-member.workshops.index')" 
                  class="btn-secondary btn-sm w-full text-left">
              Register for Workshops
            </Link>
            <Link :href="route('team-member.track.show')" 
                  class="btn-secondary btn-sm w-full text-left">
              View Track Info
            </Link>
            <!-- LEAVE TEAM OPTION -->
            <button @click="showLeaveConfirmation = true" 
                    class="btn-warning btn-sm w-full text-left">
              Leave Team
            </button>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Leave Team Confirmation Modal -->
    <Modal v-model="showLeaveConfirmation" title="Leave Team">
      <div class="text-sm text-gray-600 mb-4">
        <p>Are you sure you want to leave <strong>{{ myTeam.name }}</strong>?</p>
        <p class="mt-2 text-red-600">This action cannot be undone. You will need a new invitation to rejoin.</p>
      </div>
      <div class="flex justify-end space-x-3">
        <button @click="showLeaveConfirmation = false" class="btn-secondary">
          Cancel
        </button>
        <button @click="leaveTeam" class="btn-danger">
          Leave Team
        </button>
      </div>
    </Modal>
  </Default>
</template>
```

---

## 🔐 **AUTHENTICATION & MIDDLEWARE SYSTEM**

### **HandleInertiaRequests.php Enhancements:**
```php
// app/Http/Middleware/HandleInertiaRequests.php
public function share(Request $request): array
{
    return array_merge(parent::share($request), [
        'auth' => [
            'user' => $request->user() ? [
                'id' => $request->user()->id,
                'name' => $request->user()->name,
                'email' => $request->user()->email,
                'roles' => $request->user()->roles->pluck('name'),
                'primary_role' => $request->user()->roles->first()?->name,
                'permissions' => $request->user()->getAllPermissions()->pluck('name'),
                'avatar' => $request->user()->avatar,
                'university' => $request->user()->university,
                'college' => $request->user()->college,
            ] : null,
        ],
        'hackathon' => [
            'current_edition' => app(HackathonService::class)->getCurrentEdition(),
            'my_team' => $request->user() ? 
                app(TeamService::class)->getUserTeam($request->user()->id) : null,
            'assigned_tracks' => $request->user() && $request->user()->hasRole('track_supervisor') ? 
                app(TrackService::class)->getUserAssignedTracks($request->user()->id) : null,
        ],
        'flash' => [
            'message' => fn () => $request->session()->get('message'),
            'error' => fn () => $request->session()->get('error'),
            'success' => fn () => $request->session()->get('success'),
            'warning' => fn () => $request->session()->get('warning'),
        ],
    ]);
}
```

### **Role-Based Route Protection:**
```php
// routes/web.php

// System Admin Routes
Route::middleware(['auth', 'role:system_admin'])->prefix('system-admin')->group(function () {
    Route::get('/dashboard', [SystemAdmin\DashboardController::class, 'index'])
        ->name('system-admin.dashboard');
    Route::resource('editions', SystemAdmin\HackathonEditionController::class)
        ->names('system-admin.editions');
    Route::resource('users', SystemAdmin\UserController::class)
        ->names('system-admin.users');
    Route::resource('teams', SystemAdmin\TeamController::class)
        ->names('system-admin.teams');
    Route::resource('ideas', SystemAdmin\IdeaController::class)
        ->names('system-admin.ideas');
});

// Hackathon Admin Routes
Route::middleware(['auth', 'role:hackathon_admin'])->prefix('hackathon-admin')->group(function () {
    Route::get('/dashboard', [HackathonAdmin\DashboardController::class, 'index'])
        ->name('hackathon-admin.dashboard');
    Route::resource('teams', HackathonAdmin\TeamController::class)
        ->names('hackathon-admin.teams');
    Route::resource('ideas', HackathonAdmin\IdeaController::class)
        ->names('hackathon-admin.ideas');
    Route::post('teams/bulk-action', [HackathonAdmin\TeamController::class, 'bulkAction'])
        ->name('hackathon-admin.teams.bulk-action');
});

// Track Supervisor Routes
Route::middleware(['auth', 'role:track_supervisor'])->prefix('track-supervisor')->group(function () {
    Route::get('/dashboard', [TrackSupervisor\DashboardController::class, 'index'])
        ->name('track-supervisor.dashboard');
    Route::resource('ideas', TrackSupervisor\IdeaController::class)
        ->only(['index', 'show', 'update'])->names('track-supervisor.ideas');
    Route::post('ideas/{idea}/review', [TrackSupervisor\IdeaController::class, 'submitReview'])
        ->name('track-supervisor.ideas.review');
});

// Team Leader Routes
Route::middleware(['auth', 'role:team_leader'])->prefix('team-leader')->group(function () {
    Route::get('/dashboard', [TeamLeader\DashboardController::class, 'index'])
        ->name('team-leader.dashboard');
    Route::resource('team', TeamLeader\TeamController::class)
        ->only(['show', 'create', 'store', 'edit', 'update'])->names('team-leader.team');
    Route::resource('idea', TeamLeader\IdeaController::class)
        ->only(['show', 'create', 'store', 'edit', 'update'])->names('team-leader.idea');
    Route::post('team/invite-member', [TeamLeader\TeamController::class, 'inviteMember'])
        ->name('team-leader.team.invite-member');
});

// Team Member Routes
Route::middleware(['auth', 'role:team_member'])->prefix('team-member')->group(function () {
    Route::get('/dashboard', [TeamMember\DashboardController::class, 'index'])
        ->name('team-member.dashboard');
    Route::get('team', [TeamMember\TeamController::class, 'show'])
        ->name('team-member.team.show');
    Route::get('idea', [TeamMember\IdeaController::class, 'show'])
        ->name('team-member.idea.show');
    Route::resource('workshops', TeamMember\WorkshopController::class)
        ->only(['index', 'show'])->names('team-member.workshops');
    Route::post('team/leave', [TeamMember\TeamController::class, 'leaveTeam'])
        ->name('team-member.team.leave');
});
```

---

## 📋 **COMPLETE REQUEST CLASSES**

### **🔐 Authentication Requests**
```php
// app/Http/Requests/Auth/LoginRequest.php
class LoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8',
            'remember' => 'boolean',
        ];
    }
    
    public function messages(): array
    {
        return [
            'email.exists' => 'هذا البريد الإلكتروني غير مسجل في النظام',
            'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
        ];
    }
}

// app/Http/Requests/Auth/RegisterRequest.php  
class RegisterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'university' => 'required|string|max:255',
            'college' => 'required|string|max:255',
            'student_id' => 'nullable|string|max:50',
            'national_id' => 'required|string|max:20',
        ];
    }
}
```

### **👥 Team Management Requests**
```php
// app/Http/Requests/Team/CreateTeamRequest.php
class CreateTeamRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:teams,name',
            'description' => 'required|string|max:1000',
            'track_id' => 'required|exists:tracks,id',
            'university' => 'required|string|max:255',
            'college' => 'required|string|max:255',
            'expected_members' => 'required|integer|min:2|max:5',
        ];
    }
    
    public function authorize(): bool
    {
        return $this->user()->hasRole(['team_leader', 'system_admin']);
    }
}

// app/Http/Requests/Team/UpdateTeamRequest.php
class UpdateTeamRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:teams,name,' . $this->team->id,
            'description' => 'required|string|max:1000',
            'track_id' => 'required|exists:tracks,id',
            'university' => 'required|string|max:255',
            'college' => 'required|string|max:255',
        ];
    }
    
    public function authorize(): bool
    {
        return $this->user()->hasRole(['team_leader', 'hackathon_admin', 'system_admin']) &&
               ($this->user()->hasRole(['hackathon_admin', 'system_admin']) || 
                $this->team->leader_id === $this->user()->id);
    }
}

// app/Http/Requests/Team/AddMemberRequest.php
class AddMemberRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|email|exists:users,email',
            'role' => 'required|in:member',
            'personal_message' => 'nullable|string|max:500',
        ];
    }
}

// app/Http/Requests/Team/TeamIndexRequest.php
class TeamIndexRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'search' => 'nullable|string|max:255',
            'track_id' => 'nullable|exists:tracks,id',
            'status' => 'nullable|in:pending,approved,rejected',
            'hackathon_id' => 'nullable|exists:hackathon_editions,id',
            'per_page' => 'nullable|integer|min:10|max:100',
            'sort' => 'nullable|in:name,created_at,updated_at,status',
            'direction' => 'nullable|in:asc,desc',
        ];
    }
}

// app/Http/Requests/Team/TeamApprovalRequest.php
class TeamApprovalRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'status' => 'required|in:approved,rejected',
            'notes' => 'nullable|string|max:1000',
            'notify_team' => 'boolean',
        ];
    }
    
    public function authorize(): bool
    {
        return $this->user()->hasRole(['hackathon_admin', 'system_admin']);
    }
}
```

### **💡 Idea Management Requests**
```php
// app/Http/Requests/Idea/SubmitIdeaRequest.php
class SubmitIdeaRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'problem_statement' => 'required|string|max:1000',
            'proposed_solution' => 'required|string|max:2000',
            'target_audience' => 'required|string|max:500',
            'technologies' => 'required|array|min:1|max:10',
            'technologies.*' => 'string|max:100',
            'market_research' => 'nullable|string|max:1000',
            'competitive_analysis' => 'nullable|string|max:1000',
            'business_model' => 'nullable|string|max:1000',
            'implementation_plan' => 'nullable|string|max:1000',
            'team_experience' => 'nullable|string|max:1000',
            'files' => 'nullable|array|max:8',
            'files.*' => 'file|mimes:pdf,doc,docx,ppt,pptx,jpg,jpeg,png,gif|max:15360', // 15MB
        ];
    }
    
    public function authorize(): bool
    {
        $user = $this->user();
        return $user->hasRole('team_leader') && 
               $user->team && 
               $user->team->status === 'approved';
    }
}

// app/Http/Requests/Idea/ReviewIdeaRequest.php
class ReviewIdeaRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'status' => 'required|in:approved,rejected,needs_revision,under_review',
            'feedback' => 'required|string|max:2000',
            'score' => 'nullable|integer|min:0|max:100',
            'criteria_scores' => 'nullable|array',
            'criteria_scores.innovation' => 'nullable|integer|min:0|max:20',
            'criteria_scores.feasibility' => 'nullable|integer|min:0|max:20',
            'criteria_scores.impact' => 'nullable|integer|min:0|max:20',
            'criteria_scores.presentation' => 'nullable|integer|min:0|max:20',
            'criteria_scores.technical' => 'nullable|integer|min:0|max:20',
            'internal_notes' => 'nullable|string|max:1000',
            'recommend_for_final' => 'boolean',
        ];
    }
    
    public function authorize(): bool
    {
        $user = $this->user();
        $idea = $this->route('idea');
        
        // System admin and hackathon admin can review any idea
        if ($user->hasRole(['system_admin', 'hackathon_admin'])) {
            return true;
        }
        
        // Track supervisor can only review ideas in their assigned tracks
        if ($user->hasRole('track_supervisor')) {
            return $user->assignedTracks->contains($idea->team->track_id);
        }
        
        return false;
    }
}

// app/Http/Requests/Idea/IdeaIndexRequest.php
class IdeaIndexRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'search' => 'nullable|string|max:255',
            'team_id' => 'nullable|exists:teams,id',
            'track_id' => 'nullable|exists:tracks,id',
            'status' => 'nullable|in:draft,submitted,under_review,approved,rejected,needs_revision',
            'hackathon_id' => 'nullable|exists:hackathon_editions,id',
            'supervisor_id' => 'nullable|exists:users,id',
            'score_min' => 'nullable|integer|min:0|max:100',
            'score_max' => 'nullable|integer|min:0|max:100',
            'per_page' => 'nullable|integer|min:10|max:100',
            'sort' => 'nullable|in:title,created_at,updated_at,score,status',
            'direction' => 'nullable|in:asc,desc',
        ];
    }
}

// app/Http/Requests/Idea/UpdateIdeaRequest.php
class UpdateIdeaRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'problem_statement' => 'required|string|max:1000',
            'proposed_solution' => 'required|string|max:2000',
            'target_audience' => 'required|string|max:500',
            'technologies' => 'required|array|min:1|max:10',
            'technologies.*' => 'string|max:100',
            'market_research' => 'nullable|string|max:1000',
            'competitive_analysis' => 'nullable|string|max:1000',
            'business_model' => 'nullable|string|max:1000',
            'implementation_plan' => 'nullable|string|max:1000',
            'team_experience' => 'nullable|string|max:1000',
            'remove_files' => 'nullable|array',
            'remove_files.*' => 'integer|exists:idea_files,id',
            'new_files' => 'nullable|array|max:8',
            'new_files.*' => 'file|mimes:pdf,doc,docx,ppt,pptx,jpg,jpeg,png,gif|max:15360',
        ];
    }
    
    public function authorize(): bool
    {
        $user = $this->user();
        $idea = $this->route('idea');
        
        // System admin can edit any idea
        if ($user->hasRole('system_admin')) {
            return true;
        }
        
        // Team leader can only edit their team's idea and only if not submitted
        if ($user->hasRole('team_leader')) {
            return $idea->team->leader_id === $user->id && 
                   in_array($idea->status, ['draft', 'needs_revision']);
        }
        
        return false;
    }
}
```

### **🏢 Workshop Management Requests**
```php
// app/Http/Requests/Workshop/CreateWorkshopRequest.php
class CreateWorkshopRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'hackathon_id' => 'required|exists:hackathon_editions,id',
            'start_datetime' => 'required|date|after:now',
            'end_datetime' => 'required|date|after:start_datetime',
            'location' => 'required|string|max:255',
            'max_attendees' => 'required|integer|min:1|max:1000',
            'requirements' => 'nullable|string|max:1000',
            'materials_url' => 'nullable|url|max:500',
            'speaker_ids' => 'required|array|min:1',
            'speaker_ids.*' => 'exists:speakers,id',
            'organization_ids' => 'nullable|array',
            'organization_ids.*' => 'exists:organizations,id',
            'is_public' => 'boolean',
            'requires_registration' => 'boolean',
            'send_notifications' => 'boolean',
            'track_ids' => 'nullable|array',
            'track_ids.*' => 'exists:tracks,id',
        ];
    }
    
    public function authorize(): bool
    {
        return $this->user()->hasRole(['hackathon_admin', 'system_admin']);
    }
}

// app/Http/Requests/Workshop/UpdateWorkshopRequest.php
class UpdateWorkshopRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after:start_datetime',
            'location' => 'required|string|max:255',
            'max_attendees' => 'required|integer|min:1|max:1000',
            'requirements' => 'nullable|string|max:1000',
            'materials_url' => 'nullable|url|max:500',
            'speaker_ids' => 'required|array|min:1',
            'speaker_ids.*' => 'exists:speakers,id',
            'organization_ids' => 'nullable|array',
            'organization_ids.*' => 'exists:organizations,id',
            'status' => 'required|in:draft,published,cancelled,completed',
            'track_ids' => 'nullable|array',
            'track_ids.*' => 'exists:tracks,id',
        ];
    }
}

// app/Http/Requests/Workshop/RegisterWorkshopRequest.php (Public registration)
class RegisterWorkshopRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'national_id' => 'required|string|max:20|unique:workshop_registrations,national_id,NULL,id,workshop_id,' . $this->route('workshop')->id,
            'job_title' => 'nullable|string|max:255',
            'job_type' => 'required|in:student,employee,entrepreneur,other',
            'organization' => 'nullable|string|max:255',
            'experience_level' => 'required|in:beginner,intermediate,advanced',
            'interests' => 'nullable|array|max:5',
            'interests.*' => 'string|max:100',
            'dietary_requirements' => 'nullable|string|max:200',
            'accessibility_needs' => 'nullable|string|max:200',
        ];
    }
    
    public function authorize(): bool
    {
        $workshop = $this->route('workshop');
        return $workshop->is_public && 
               $workshop->requires_registration &&
               $workshop->start_datetime > now() &&
               $workshop->current_registrations < $workshop->max_attendees;
    }
}

// app/Http/Requests/Workshop/WorkshopIndexRequest.php
class WorkshopIndexRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'search' => 'nullable|string|max:255',
            'hackathon_id' => 'nullable|exists:hackathon_editions,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'status' => 'nullable|in:draft,published,cancelled,completed',
            'speaker_id' => 'nullable|exists:speakers,id',
            'organization_id' => 'nullable|exists:organizations,id',
            'track_id' => 'nullable|exists:tracks,id',
            'has_available_spots' => 'nullable|boolean',
            'per_page' => 'nullable|integer|min:10|max:100',
            'sort' => 'nullable|in:start_datetime,title,created_at',
            'direction' => 'nullable|in:asc,desc',
        ];
    }
}

// app/Http/Requests/Workshop/MarkAttendanceRequest.php
class MarkAttendanceRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'registration_id' => 'required|exists:workshop_registrations,id',
            'attendance_status' => 'required|in:present,absent,late',
            'notes' => 'nullable|string|max:500',
        ];
    }
    
    public function authorize(): bool
    {
        $user = $this->user();
        $workshop = $this->route('workshop');
        
        return $user->hasRole(['hackathon_admin', 'system_admin', 'track_supervisor']) ||
               $workshop->speakers->contains('user_id', $user->id);
    }
}
```

### **📰 News Management Requests**
```php
// app/Http/Requests/News/CreateNewsRequest.php
class CreateNewsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:5000',
            'excerpt' => 'nullable|string|max:500',
            'category' => 'required|string|in:announcements,workshops,partnerships,winners,general',
            'hackathon_id' => 'required|exists:hackathon_editions,id',
            'featured_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status' => 'required|in:draft,published,archived',
            'publish_at' => 'nullable|date|after_or_equal:now',
            'auto_tweet' => 'boolean',
            'tweet_content' => 'nullable|string|max:280',
            'tags' => 'nullable|array|max:10',
            'tags.*' => 'string|max:50',
            'target_audience' => 'nullable|array',
            'target_audience.*' => 'in:all,participants,team_leaders,supervisors,admins',
            'priority' => 'required|in:low,normal,high,urgent',
            'expires_at' => 'nullable|date|after:now',
        ];
    }
    
    public function authorize(): bool
    {
        return $this->user()->hasRole(['hackathon_admin', 'system_admin']);
    }
}

// app/Http/Requests/News/UpdateNewsRequest.php
class UpdateNewsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:5000',
            'excerpt' => 'nullable|string|max:500',
            'category' => 'required|string|in:announcements,workshops,partnerships,winners,general',
            'featured_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status' => 'required|in:draft,published,archived',
            'publish_at' => 'nullable|date',
            'auto_tweet' => 'boolean',
            'tweet_content' => 'nullable|string|max:280',
            'tags' => 'nullable|array|max:10',
            'tags.*' => 'string|max:50',
            'target_audience' => 'nullable|array',
            'target_audience.*' => 'in:all,participants,team_leaders,supervisors,admins',
            'priority' => 'required|in:low,normal,high,urgent',
            'expires_at' => 'nullable|date|after:now',
            'send_update_notification' => 'boolean',
        ];
    }
}

// app/Http/Requests/News/NewsIndexRequest.php
class NewsIndexRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'search' => 'nullable|string|max:255',
            'category' => 'nullable|string|in:announcements,workshops,partnerships,winners,general',
            'status' => 'nullable|in:draft,published,archived',
            'hackathon_id' => 'nullable|exists:hackathon_editions,id',
            'author_id' => 'nullable|exists:users,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'priority' => 'nullable|in:low,normal,high,urgent',
            'has_twitter_post' => 'nullable|boolean',
            'per_page' => 'nullable|integer|min:10|max:100',
            'sort' => 'nullable|in:title,created_at,updated_at,publish_at,priority',
            'direction' => 'nullable|in:asc,desc',
        ];
    }
}
```

### **⚙️ Settings Management Requests**
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
            'mail_from_address' => 'required|email|max:255',
            'mail_from_name' => 'required|string|max:255',
            'test_email' => 'nullable|email|max:255',
        ];
    }
    
    public function authorize(): bool
    {
        return $this->user()->hasRole('system_admin');
    }
}

// app/Http/Requests/Settings/UpdateBrandingRequest.php
class UpdateBrandingRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'app_name' => 'required|string|max:255',
            'app_description' => 'nullable|string|max:500',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
            'logo_dark' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
            'favicon' => 'nullable|image|mimes:ico,png|max:512',
            'primary_color' => 'required|string|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
            'secondary_color' => 'required|string|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
            'accent_color' => 'nullable|string|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
            'footer_text' => 'nullable|string|max:500',
            'copyright_text' => 'nullable|string|max:200',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'social_media' => 'nullable|array',
            'social_media.twitter' => 'nullable|url|max:255',
            'social_media.linkedin' => 'nullable|url|max:255',
            'social_media.facebook' => 'nullable|url|max:255',
            'social_media.instagram' => 'nullable|url|max:255',
        ];
    }
}

// app/Http/Requests/Settings/UpdateTwitterRequest.php
class UpdateTwitterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'twitter_api_key' => 'required|string|max:255',
            'twitter_api_secret' => 'required|string|max:255',
            'twitter_access_token' => 'required|string|max:255',
            'twitter_access_token_secret' => 'required|string|max:255',
            'twitter_bearer_token' => 'nullable|string|max:255',
            'auto_tweet_news' => 'boolean',
            'auto_tweet_workshops' => 'boolean',
            'auto_tweet_deadlines' => 'boolean',
            'tweet_template_news' => 'nullable|string|max:200',
            'tweet_template_workshop' => 'nullable|string|max:200',
            'tweet_template_deadline' => 'nullable|string|max:200',
            'default_hashtags' => 'nullable|array|max:10',
            'default_hashtags.*' => 'string|max:50|regex:/^[a-zA-Z0-9_]+$/',
        ];
    }
}

// app/Http/Requests/Settings/UpdateSmsRequest.php
class UpdateSmsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'sms_provider' => 'required|in:twilio,nexmo,unifonic,msegat',
            'sms_api_key' => 'required|string|max:255',
            'sms_api_secret' => 'required|string|max:255',
            'sms_from_number' => 'required|string|max:20',
            'sms_enabled' => 'boolean',
            'sms_notifications' => 'nullable|array',
            'sms_notifications.*' => 'in:team_approval,idea_feedback,workshop_reminder,deadline_warning',
            'test_phone_number' => 'nullable|string|max:20',
        ];
    }
}
```

### **🏆 Edition Management Requests**
```php
// app/Http/Requests/Edition/CreateHackathonEditionRequest.php
class CreateHackathonEditionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'year' => 'required|integer|min:2024|max:2030|unique:hackathon_editions,year',
            'description' => 'nullable|string|max:2000',
            'theme' => 'nullable|string|max:255',
            'hackathon_admin_id' => 'required|exists:users,id',
            'registration_start_date' => 'required|date|after_or_equal:today',
            'registration_end_date' => 'required|date|after:registration_start_date',
            'idea_submission_start_date' => 'required|date|after_or_equal:registration_start_date',
            'idea_submission_end_date' => 'required|date|after:idea_submission_start_date',
            'event_start_date' => 'required|date|after_or_equal:idea_submission_end_date',
            'event_end_date' => 'required|date|after:event_start_date',
            'location' => 'nullable|string|max:255',
            'max_teams' => 'required|integer|min:1|max:1000',
            'max_team_size' => 'required|integer|min:2|max:10',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
            'banner' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
            'theme_color' => 'nullable|string|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
            'prizes' => 'nullable|array',
            'prizes.*.title' => 'required|string|max:255',
            'prizes.*.amount' => 'required|numeric|min:0',
            'prizes.*.currency' => 'required|string|in:SAR,USD',
            'prizes.*.description' => 'nullable|string|max:500',
            'rules_and_regulations' => 'nullable|string|max:5000',
            'judging_criteria' => 'nullable|array',
            'judging_criteria.*' => 'required|string|max:255',
            'sponsors' => 'nullable|array',
            'sponsors.*' => 'exists:organizations,id',
        ];
    }
    
    public function authorize(): bool
    {
        return $this->user()->hasRole('system_admin');
    }
}

// app/Http/Requests/Edition/UpdateHackathonEditionRequest.php
class UpdateHackathonEditionRequest extends FormRequest
{
    public function rules(): array
    {
        $editionId = $this->route('edition')?->id;
        
        return [
            'name' => 'required|string|max:255',
            'year' => 'required|integer|min:2024|max:2030|unique:hackathon_editions,year,' . $editionId,
            'description' => 'nullable|string|max:2000',
            'theme' => 'nullable|string|max:255',
            'hackathon_admin_id' => 'required|exists:users,id',
            'registration_start_date' => 'required|date',
            'registration_end_date' => 'required|date|after:registration_start_date',
            'idea_submission_start_date' => 'required|date|after_or_equal:registration_start_date',
            'idea_submission_end_date' => 'required|date|after:idea_submission_start_date',
            'event_start_date' => 'required|date|after_or_equal:idea_submission_end_date',
            'event_end_date' => 'required|date|after:event_start_date',
            'location' => 'nullable|string|max:255',
            'status' => 'required|in:draft,active,completed,archived',
            'is_current' => 'boolean',
            'max_teams' => 'required|integer|min:1|max:1000',
            'max_team_size' => 'required|integer|min:2|max:10',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
            'banner' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
            'theme_color' => 'nullable|string|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
            'prizes' => 'nullable|array',
            'prizes.*.title' => 'required|string|max:255',
            'prizes.*.amount' => 'required|numeric|min:0',
            'prizes.*.currency' => 'required|string|in:SAR,USD',
            'prizes.*.description' => 'nullable|string|max:500',
            'rules_and_regulations' => 'nullable|string|max:5000',
            'judging_criteria' => 'nullable|array',
            'judging_criteria.*' => 'required|string|max:255',
            'sponsors' => 'nullable|array',
            'sponsors.*' => 'exists:organizations,id',
            'allow_late_registration' => 'boolean',
            'allow_late_submission' => 'boolean',
        ];
    }
}
```

---

## 🗄️ **COMPLETE SEEDER DOCUMENTATION**

### **✅ EXISTING SEEDERS ANALYSIS**
```
✅ Already exist (20 seeders):
DatabaseSeeder.php ✅              - Main seeder orchestrator
RolesAndPermissionsSeeder.php ✅   - 5 roles + permissions
UserSeeder.php ✅                  - Admin users for each role
SettingSeeder.php ✅               - System settings
HackathonSeeder.php ✅             - Current hackathon edition
TrackSeeder.php ✅                 - Competition tracks
TeamSeeder.php ✅                  - Sample teams
IdeaSeeder.php ✅                  - Sample ideas
NewsSeeder.php ✅                  - Sample news articles
OrganizationSeeder.php ✅          - Partner organizations
SpeakerSeeder.php ✅               - Workshop speakers
WorkshopSeeder.php ✅              - Sample workshops (via HackathonSeeder)
And 8 more supporting seeders ✅
```

### **📊 DETAILED SEEDER RESPONSIBILITIES**

#### **1. RolesAndPermissionsSeeder.php** 
```php
// Creates exactly these 5 roles with specific permissions:
'system_admin' => [
    'manage-hackathon-editions', 'manage-users', 'manage-system-settings',
    'view-all-teams', 'manage-all-ideas', 'manage-all-workshops',
    'manage-all-news', 'view-system-reports', 'manage-twitter-integration'
]

'hackathon_admin' => [
    'manage-current-edition', 'approve-reject-teams', 'review-ideas',
    'manage-workshops', 'manage-news', 'assign-supervisors',
    'view-edition-reports', 'export-edition-data'
]

'track_supervisor' => [
    'view-assigned-tracks', 'review-track-ideas', 'manage-track-workshops',
    'view-track-teams', 'provide-feedback', 'score-ideas'
]

'team_leader' => [
    'create-manage-team', 'submit-edit-ideas', 'invite-team-members',
    'register-workshops', 'view-team-progress', 'access-team-resources'
]

'team_member' => [
    'view-team-info', 'view-team-ideas', 'register-workshops',
    'view-track-info', 'view-news', 'leave-team'
]
```

#### **2. UserSeeder.php**
```php
// Creates admin users for each role with realistic Saudi data:
1. System Admin: 
   - Email: admin@ruman.sa 
   - Name: المدير العام
   - University: جامعة الملك سعود
   - College: كلية علوم الحاسب والمعلومات

2. Hackathon Admin: 
   - Email: hackathon@ruman.sa
   - Name: مدير الهاكاثون
   - University: جامعة الملك فهد للبترول والمعادن
   - College: كلية الهندسة

3. Track Supervisor: 
   - Email: supervisor@ruman.sa
   - Name: مشرف المسار
   - Assigned to: FinTech Track
   - University: جامعة الإمام محمد بن سعود

4. Team Leader: 
   - Email: leader@ruman.sa
   - Name: قائد الفريق
   - Team: Team Alpha (FinTech Track)

5. Team Member: 
   - Email: member@ruman.sa
   - Name: عضو الفريق
   - Member of: Team Alpha

// All users have 2FA disabled for testing and realistic Saudi phone numbers
```

#### **3. HackathonSeeder.php**
```php
// Creates current hackathon edition with proper Arabic/English data:
'name' => 'Ruman Hackathon 2025 - رومان هاكاثون ٢٠٢٥',
'year' => 2025,
'status' => 'active',
'is_current' => true,
'theme' => 'Digital Transformation in Saudi Arabia',
'description' => 'Hackathon focused on digital innovation supporting Saudi Vision 2030',
'registration_start_date' => '2025-01-15 00:00:00',
'registration_end_date' => '2025-02-15 23:59:59',
'idea_submission_start_date' => '2025-02-01 00:00:00',
'idea_submission_end_date' => '2025-03-01 23:59:59',
'event_start_date' => '2025-03-15 08:00:00',
'event_end_date' => '2025-03-17 18:00:00',
'location' => 'King Abdulaziz City for Science and Technology, Riyadh',
'max_teams' => 200,
'max_team_size' => 5,
'prizes' => [
    ['title' => 'First Place', 'amount' => 50000, 'currency' => 'SAR'],
    ['title' => 'Second Place', 'amount' => 30000, 'currency' => 'SAR'],
    ['title' => 'Third Place', 'amount' => 20000, 'currency' => 'SAR'],
    ['title' => 'Best Innovation', 'amount' => 15000, 'currency' => 'SAR']
]
```

#### **4. TrackSeeder.php**
```php
// Creates 8 competition tracks aligned with Saudi Vision 2030:
1. FinTech - التقنية المالية
   - Description: Digital payment solutions, blockchain, Islamic finance
   - Max Teams: 25
   - Supervisor: Dr. Ahmed Al-Rashid

2. HealthTech - تقنية الصحة  
   - Description: Digital health solutions, telemedicine, health data
   - Max Teams: 25
   - Supervisor: Dr. Fatima Al-Zahra

3. EdTech - التقنية التعليمية
   - Description: E-learning platforms, educational AI, digital literacy
   - Max Teams: 25
   - Supervisor: Dr. Mohammed Al-Ghamdi

4. Smart Cities - المدن الذكية
   - Description: IoT for cities, traffic optimization, sustainability
   - Max Teams: 25
   - Supervisor: Eng. Noura Al-Mansouri

5. AI & Machine Learning - الذكاء الاصطناعي
   - Description: AI applications, natural language processing, computer vision
   - Max Teams: 30
   - Supervisor: Dr. Khalid Al-Dosari

6. Cybersecurity - الأمن السيبراني
   - Description: Security solutions, privacy protection, secure communications
   - Max Teams: 20
   - Supervisor: Dr. Sarah Al-Mutairi

7. E-commerce - التجارة الإلكترونية
   - Description: Online marketplaces, logistics, supply chain
   - Max Teams: 25
   - Supervisor: Mr. Omar Al-Harbi

8. Green Technology - التقنية الخضراء
   - Description: Renewable energy, environmental monitoring, sustainability
   - Max Teams: 25
   - Supervisor: Dr. Amina Al-Rasheed
```

#### **5. TeamSeeder.php** 
```php
// Creates 40 sample teams (5 per track) with realistic Saudi names:
Teams per track:
- "فريق الابتكار" (Innovation Team)  
- "فريق المستقبل" (Future Team)
- "فريق الرؤية" (Vision Team)
- "فريق التقنية" (Technology Team)
- "فريق النجاح" (Success Team)

Each team has:
- Leader from team_leader role
- 2-4 members from team_member role
- Status: mix of "pending", "approved", "rejected"
- Universities: Real Saudi universities (جامعة الملك سعود، جامعة الملك فهد، جامعة الإمام)
- Colleges: Computer Science, Engineering, Business, Medicine
- Join codes: AUTO-GENERATED (e.g., "INNO2025", "FUTR2025")
- Realistic Arabic team descriptions
```

#### **6. IdeaSeeder.php**
```php
// Creates 32 sample ideas (4 per track) with comprehensive Saudi context:

FinTech Ideas:
1. "منصة الدفع الذكية" - Smart Payment Platform
   - Islamic finance compliant digital wallet
   - Technologies: ["React Native", "Node.js", "MongoDB", "Blockchain"]
   
2. "حلول التمويل الجماعي" - Crowdfunding Solutions
   - Sharia-compliant crowdfunding platform
   - Technologies: ["Vue.js", "Laravel", "PostgreSQL", "Stripe"]

HealthTech Ideas:
1. "مساعد الصحة الذكي" - Smart Health Assistant  
   - AI-powered health monitoring for elderly
   - Technologies: ["Python", "TensorFlow", "Flutter", "AWS"]

2. "منصة التطبيب عن بعد" - Telemedicine Platform
   - Remote consultations with Arabic language support
   - Technologies: ["React", "WebRTC", "Firebase", "Google Cloud"]

Each idea includes:
- Complete Arabic/English descriptions (500+ words)
- Problem statement addressing Saudi challenges
- Proposed solution with technical implementation
- Market research specific to Saudi Arabia
- 2-4 uploaded files (PDFs, presentations, mockups)
- Status: mix of "draft", "submitted", "under_review", "approved"
- Realistic scoring from supervisors (60-95 points)
```

#### **7. NewsSeeder.php**
```php
// Creates 15 news articles covering hackathon lifecycle:

Announcements (5 articles):
1. "فتح باب التسجيل في هاكاثون رومان 2025"
2. "الإعلان عن المسارات المتاحة والجوائز" 
3. "تمديد فترة التسجيل حتى نهاية فبراير"
4. "بدء فترة تسليم الأفكار والمشاريع"
5. "الإعلان عن لجان التحكيم والمقيمين"

Workshop Announcements (5 articles):
1. "ورشة عمل: مقدمة في التقنية المالية"
2. "ورشة عمل: تطوير تطبيقات الذكاء الاصطناعي"
3. "ورشة عمل: أمن المعلومات والحماية السيبرانية"
4. "ورشة عمل: ريادة الأعمال والاستثمار"
5. "ورشة عمل: العرض والتقديم الفعال"

Partnerships (3 articles):
1. "شراكة استراتيجية مع شركة أرامكو السعودية"
2. "انضمام جامعة الملك سعود كشريك أكاديمي"
3. "دعم من صندوق الاستثمارات العامة"

Results (2 articles):
1. "الإعلان عن المتأهلين للمرحلة النهائية"
2. "تكريم الفائزين والإعلان عن الجوائز"

Each article includes:
- Arabic/English bilingual content
- Featured images from sample image pool
- Proper publication dates across 4 months
- Auto-tweet enabled for published articles
- View counts (500-2500 views)
- Realistic engagement metrics
```

#### **8. OrganizationSeeder.php & SpeakerSeeder.php**
```php
// Organizations: 12 major Saudi companies and institutions
[
    'Saudi Aramco - أرامكو السعودية',
    'Saudi Telecom Company - شركة الاتصالات السعودية', 
    'Al Rajhi Bank - مصرف الراجحي',
    'SABIC - سابك',
    'King Saud University - جامعة الملك سعود',
    'KFUPM - جامعة الملك فهد للبترول والمعادن',
    'KAUST - جامعة الملك عبدالله للعلوم والتقنية',
    'Saudi Investment Fund - صندوق الاستثمارات العامة',
    'Elm Company - شركة علم',
    'STC Pay - إس تي سي باي',
    'Careem Saudi Arabia - كريم السعودية',
    'Noon.com - نون'
]

// Speakers: 20 industry experts with Saudi/regional representation
- Mix of Saudi and international speakers
- Expertise covers all 8 tracks
- Realistic bio information and social links  
- Professional photos and credentials
- Association with 2-3 partner organizations
- Arabic/English speaker names and bios
```

#### **9. WorkshopSeeder.php**
```php
// Creates 12 workshops spread across hackathon timeline:

Pre-Event Workshops (4 workshops):
1. "مقدمة في ريادة الأعمال والابتكار" 
   - Date: 2025-02-01, Duration: 3 hours
   - Speaker: د. أحمد الراشد (Saudi Aramco)
   - Max Attendees: 100

2. "أساسيات تطوير التطبيقات"
   - Date: 2025-02-08, Duration: 4 hours  
   - Speaker: م. نورا المنصوري (STC)
   - Max Attendees: 80

During Event Workshops (6 workshops):
3-8. Technical workshops during hackathon weekend
   - Mentorship sessions
   - Technical support
   - Pitch preparation
   - Industry insights

Post-Event Workshops (2 workshops):
9. "عرض النتائج والتقييم"
10. "الاستثمار والتمويل للمشاريع الناشئة"

Each workshop includes:
- Arabic/English titles and descriptions
- Realistic Saudi venue locations
- Proper capacity limits (50-150 attendees)
- Materials URLs and requirements
- Registration tracking setup
- QR codes for attendance
- Pre/post event surveys
```

#### **10. QRCodeSeeder.php (NEW)**
```php
// Creates QR codes for workshop attendance tracking:
- Unique QR code per workshop
- Embedded attendance URLs
- Printable format specifications  
- Security tokens to prevent fraud
- Mobile-optimized scanning interface
- Automatic attendance marking
- Integration with workshop registration system
```

---

## 🚀 **IMPLEMENTATION TIMELINE - PHASE BY PHASE**

### **PHASE 1: Request Classes & Services (45 minutes)**
```bash
# Create all Request classes (35+ classes)
mkdir -p app/Http/Requests/{Auth,Team,Idea,Workshop,News,Settings,Edition,Public}

# Authentication requests
php artisan make:request Auth/LoginRequest
php artisan make:request Auth/RegisterRequest

# Team management requests  
php artisan make:request Team/CreateTeamRequest
php artisan make:request Team/UpdateTeamRequest
php artisan make:request Team/AddMemberRequest
php artisan make:request Team/TeamIndexRequest
php artisan make:request Team/TeamApprovalRequest

# Idea management requests
php artisan make:request Idea/SubmitIdeaRequest
php artisan make:request Idea/ReviewIdeaRequest
php artisan make:request Idea/IdeaIndexRequest
php artisan make:request Idea/UpdateIdeaRequest

# Workshop requests
php artisan make:request Workshop/CreateWorkshopRequest
php artisan make:request Workshop/UpdateWorkshopRequest
php artisan make:request Workshop/RegisterWorkshopRequest
php artisan make:request Workshop/WorkshopIndexRequest
php artisan make:request Workshop/MarkAttendanceRequest

# News management requests
php artisan make:request News/CreateNewsRequest
php artisan make:request News/UpdateNewsRequest
php artisan make:request News/NewsIndexRequest

# Settings requests
php artisan make:request Settings/UpdateSmtpRequest
php artisan make:request Settings/UpdateBrandingRequest
php artisan make:request Settings/UpdateTwitterRequest
php artisan make:request Settings/UpdateSmsRequest

# Edition management requests
php artisan make:request Edition/CreateHackathonEditionRequest
php artisan make:request Edition/UpdateHackathonEditionRequest

# Public requests
php artisan make:request Public/WorkshopRegistrationRequest

# Create all Service classes (10 services)
mkdir -p app/Services
php artisan make:service TeamService
php artisan make:service IdeaService  
php artisan make:service WorkshopService
php artisan make:service NewsService
php artisan make:service HackathonEditionService
php artisan make:service UserService
php artisan make:service SettingsService
php artisan make:service NotificationService
php artisan make:service TwitterService
php artisan make:service QRCodeService
```

### **PHASE 2: Controllers (60 minutes)**
```bash
# Create role-based controller directories
mkdir -p app/Http/Controllers/{SystemAdmin,HackathonAdmin,TrackSupervisor,TeamLeader,TeamMember,Public}

# System Admin controllers (8 controllers)
php artisan make:controller SystemAdmin/DashboardController
php artisan make:controller SystemAdmin/HackathonEditionController --resource
php artisan make:controller SystemAdmin/UserController --resource
php artisan make:controller SystemAdmin/TeamController --resource
php artisan make:controller SystemAdmin/IdeaController --resource
php artisan make:controller SystemAdmin/SettingsController
php artisan make:controller SystemAdmin/TwitterController
php artisan make:controller SystemAdmin/ReportController

# Hackathon Admin controllers (7 controllers)
php artisan make:controller HackathonAdmin/DashboardController
php artisan make:controller HackathonAdmin/TeamController --resource
php artisan make:controller HackathonAdmin/IdeaController --resource
php artisan make:controller HackathonAdmin/WorkshopController --resource
php artisan make:controller HackathonAdmin/NewsController --resource
php artisan make:controller HackathonAdmin/TrackController
php artisan make:controller HackathonAdmin/ReportController

# Track Supervisor controllers (4 controllers)
php artisan make:controller TrackSupervisor/DashboardController
php artisan make:controller TrackSupervisor/IdeaController
php artisan make:controller TrackSupervisor/WorkshopController
php artisan make:controller TrackSupervisor/TeamController

# Team Leader controllers (4 controllers)
php artisan make:controller TeamLeader/DashboardController
php artisan make:controller TeamLeader/TeamController
php artisan make:controller TeamLeader/IdeaController
php artisan make:controller TeamLeader/WorkshopController

# Team Member controllers (4 controllers)
php artisan make:controller TeamMember/DashboardController
php artisan make:controller TeamMember/TeamController
php artisan make:controller TeamMember/WorkshopController
php artisan make:controller TeamMember/IdeaController

# Public controllers (4 controllers)
php artisan make:controller Public/PublicController
php artisan make:controller Public/WorkshopController
php artisan make:controller Public/QRScannerController
php artisan make:controller Public/NewsController
```

### **PHASE 3: Frontend Navigation Enhancement (30 minutes)**
```bash
# Update existing navigation component
# File: resources/js/Components/NavSidebarDesktop.vue

# Add role-based menu system:
# - Update navigationSections to be computed based on user role
# - Add role detection logic
# - Implement dynamic menu rendering
# - Add Arabic translations for menu items
# - Include proper icons for each section
```

### **PHASE 4: Frontend Pages (90 minutes)**
```bash
# Create role-based page directories
mkdir -p resources/js/Pages/{SystemAdmin,HackathonAdmin,TrackSupervisor,TeamLeader,TeamMember,Public}

# System Admin pages (20 pages)
mkdir -p resources/js/Pages/SystemAdmin/{Dashboard,Editions,Users,Teams,Ideas,Settings,Reports}
touch resources/js/Pages/SystemAdmin/Dashboard/Index.vue
touch resources/js/Pages/SystemAdmin/Editions/{Index,Create,Edit,Show}.vue
touch resources/js/Pages/SystemAdmin/Users/{Index,Create,Edit,Show}.vue
touch resources/js/Pages/SystemAdmin/Teams/{Index,Show}.vue
touch resources/js/Pages/SystemAdmin/Ideas/{Index,Show}.vue
touch resources/js/Pages/SystemAdmin/Settings/{Index,Smtp,Branding,Twitter,Sms}.vue
touch resources/js/Pages/SystemAdmin/Reports/Index.vue

# Hackathon Admin pages (15 pages)
mkdir -p resources/js/Pages/HackathonAdmin/{Dashboard,Teams,Ideas,Workshops,News,Reports}
touch resources/js/Pages/HackathonAdmin/Dashboard/Index.vue
touch resources/js/Pages/HackathonAdmin/Teams/{Index,Show,Bulk}.vue
touch resources/js/Pages/HackathonAdmin/Ideas/{Index,Show,Review}.vue
touch resources/js/Pages/HackathonAdmin/Workshops/{Index,Create,Edit,Attendance}.vue
touch resources/js/Pages/HackathonAdmin/News/{Index,Create,Edit}.vue
touch resources/js/Pages/HackathonAdmin/Reports/Index.vue

# Track Supervisor pages (8 pages)
mkdir -p resources/js/Pages/TrackSupervisor/{Dashboard,Ideas,Teams,Workshops}
touch resources/js/Pages/TrackSupervisor/Dashboard/Index.vue
touch resources/js/Pages/TrackSupervisor/Ideas/{Index,Show,Review}.vue
touch resources/js/Pages/TrackSupervisor/Teams/{Index,Show}.vue
touch resources/js/Pages/TrackSupervisor/Workshops/{Index,Show}.vue

# Team Leader pages (8 pages)
mkdir -p resources/js/Pages/TeamLeader/{Dashboard,Team,Idea,Workshops}
touch resources/js/Pages/TeamLeader/Dashboard/Index.vue
touch resources/js/Pages/TeamLeader/Team/{Show,Edit,Members}.vue
touch resources/js/Pages/TeamLeader/Idea/{Show,Create,Edit}.vue
touch resources/js/Pages/TeamLeader/Workshops/{Index,Show}.vue

# Team Member pages (6 pages)  
mkdir -p resources/js/Pages/TeamMember/{Dashboard,Team,Idea,Workshops}
touch resources/js/Pages/TeamMember/Dashboard/Index.vue
touch resources/js/Pages/TeamMember/Team/Show.vue
touch resources/js/Pages/TeamMember/Idea/Show.vue
touch resources/js/Pages/TeamMember/Workshops/{Index,Show}.vue

# Public pages (6 pages)
mkdir -p resources/js/Pages/Public/{Workshops,News,QR}
touch resources/js/Pages/Public/Workshops/{Index,Show,Register}.vue
touch resources/js/Pages/Public/News/{Index,Show}.vue
touch resources/js/Pages/Public/QR/Scanner.vue
```

### **PHASE 5: Enhanced Components (45 minutes)**
```bash
# Create role-specific components
mkdir -p resources/js/Components/{Role,Workshop,QR,Forms,Tables,Status}

# Role-specific components
touch resources/js/Components/Role/RoleBasedNavigation.vue
touch resources/js/Components/Role/DashboardWidget.vue
touch resources/js/Components/Role/PermissionGate.vue
touch resources/js/Components/Role/RoleIndicator.vue

# Workshop components
touch resources/js/Components/Workshop/WorkshopCard.vue
touch resources/js/Components/Workshop/RegistrationModal.vue
touch resources/js/Components/Workshop/AttendanceScanner.vue
touch resources/js/Components/Workshop/WorkshopSchedule.vue

# QR Code components
touch resources/js/Components/QR/QRCodeGenerator.vue
touch resources/js/Components/QR/QRCodeScanner.vue
touch resources/js/Components/QR/AttendanceMarker.vue
touch resources/js/Components/QR/RegistrationQR.vue

# Enhanced form components
touch resources/js/Components/Forms/TeamForm.vue
touch resources/js/Components/Forms/IdeaForm.vue  
touch resources/js/Components/Forms/WorkshopForm.vue
touch resources/js/Components/Forms/NewsForm.vue
touch resources/js/Components/Forms/BulkActionForm.vue

# Enhanced table components
touch resources/js/Components/Tables/TeamsTable.vue
touch resources/js/Components/Tables/IdeasTable.vue
touch resources/js/Components/Tables/WorkshopsTable.vue
touch resources/js/Components/Tables/EnhancedDatatable.vue

# Status components
touch resources/js/Components/Status/StatusBadge.vue
touch resources/js/Components/Status/ProgressIndicator.vue
touch resources/js/Components/Status/DeadlineCounter.vue
touch resources/js/Components/Status/TeamProgress.vue
```

### **PHASE 6: Routes & Middleware (30 minutes)**
```bash
# Update routes/web.php with role-based route groups
# Add middleware for role-based access control
# Create route naming conventions per role
# Add API routes for AJAX functionality
# Implement proper route model binding
```

## 🎯 **SUCCESS CRITERIA CHECKLIST**

### **✅ AUTHENTICATION & ROLE SYSTEM:**
- [ ] All 5 roles can login and see role-appropriate dashboard
- [ ] NavSidebarDesktop.vue shows correct menu items per role
- [ ] HandleInertiaRequests.php shares proper role data
- [ ] Route middleware protects pages correctly
- [ ] Users redirected to role-specific landing pages

### **✅ SYSTEM ADMIN FUNCTIONALITY:**
- [ ] Can create/manage hackathon editions
- [ ] Can view system-wide statistics
- [ ] Can manage users and assign roles
- [ ] Can configure SMTP, Twitter, and branding settings
- [ ] Can access all teams, ideas, and workshops across editions
- [ ] Can generate comprehensive reports

### **✅ HACKATHON ADMIN FUNCTIONALITY:**
- [ ] Can approve/reject teams with bulk actions
- [ ] Can review and assign supervisors to ideas
- [ ] Can create and manage workshops
- [ ] Can publish news with Twitter integration
- [ ] Can view current edition analytics
- [ ] Can export current edition data

### **✅ TRACK SUPERVISOR FUNCTIONALITY:**
- [ ] Can only see teams/ideas from assigned track(s)
- [ ] Can review and score ideas with detailed criteria
- [ ] Can provide feedback to teams
- [ ] Can view track-specific statistics
- [ ] Cannot access other tracks' data

### **✅ TEAM LEADER FUNCTIONALITY:**
- [ ] Can create and manage own team
- [ ] Can invite/remove team members
- [ ] Can submit and edit team ideas
- [ ] Can register for workshops
- [ ] Can view team progress and deadlines
- [ ] Cannot edit other teams' information

### **✅ TEAM MEMBER FUNCTIONALITY:**
- [ ] Can view team information (read-only)
- [ ] Can view team ideas (read-only)
- [ ] Can register for workshops independently
- [ ] Can leave team with confirmation
- [ ] Cannot edit team or idea details

### **✅ PUBLIC FUNCTIONALITY:**
- [ ] Public workshop registration works without login
- [ ] QR code generation for workshop attendance
- [ ] QR code scanning marks attendance correctly
- [ ] Public news display functions properly
- [ ] Workshop capacity limits enforced

### **✅ FRONTEND BEHAVIOR:**
- [ ] Dashboard widgets show role-appropriate data
- [ ] Navigation menus hide/show based on permissions
- [ ] Status badges and progress indicators work correctly
- [ ] Bulk actions available only to authorized roles
- [ ] Forms validate according to Request classes
- [ ] Error handling shows appropriate messages
- [ ] Mobile responsive design maintained

### **✅ DATABASE & SEEDERS:**
- [ ] All 35+ migrations run successfully
- [ ] All 20+ seeders populate sample data correctly
- [ ] 5 roles with proper permissions assigned
- [ ] Sample users for each role can login
- [ ] Current hackathon edition is properly set
- [ ] Workshop attendance tracking tables ready
- [ ] Twitter integration tables ready
- [ ] Multi-edition support functional

### **✅ API INTEGRATION:**
- [ ] All Request classes validate input correctly
- [ ] Services handle business logic properly
- [ ] Twitter API integration posts news automatically
- [ ] Email notifications sent for team status changes
- [ ] File uploads work for ideas (8 files, 15MB each)
- [ ] QR code system generates and scans correctly

This implementation plan is now **COMPLETELY COMPREHENSIVE** with ultra-detailed role-based frontend behavior, exact component specifications, enhanced Request classes, complete seeder documentation, and precise technical implementation details.

**READY FOR ONE-SHOT IMPLEMENTATION - 100% COMPLETE COVERAGE WITH ENHANCED FRONTEND BEHAVIOR**