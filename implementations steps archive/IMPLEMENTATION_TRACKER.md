# 🎯 **HACKATHON IMPLEMENTATION TRACKER - FINAL VERSION**

**Plan Reference:** `ULTRA_DETAILED_IMPLEMENTATION_PLAN.md` (COMPLETELY MERGED & ENHANCED)  
**Updated:** January 7, 2025  
**Status:** IN PROGRESS - Phase 1-6 Completed

## ✅ COMPLETED PHASES

### **PHASE 1: AUTHENTICATION & ROLE SETUP** ✅
- [x] Created 5 role-based middleware (SystemAdmin, HackathonAdmin, TrackSupervisor, TeamLeader, TeamMember)
- [x] Registered middleware in Kernel.php
- [x] Created 27 Request validation classes across all roles
- [x] Updated HandleInertiaRequests to share role data, permissions, and primary_role
- [x] Created HackathonRoleSeeder with 29 permissions across 5 roles

### **PHASE 2: DATABASE SETUP** ✅
- [x] Ran migrations successfully (35 tables created)
- [x] Fixed migration order issues
- [x] All hackathon-specific tables created:
  - hackathon_editions, hackathons, teams, ideas, tracks
  - workshops, workshop_registrations, workshop_attendances
  - news, organizations, speakers
- [x] Added hackathon fields to users table (team_id, track_id, etc.)

### **PHASE 3: BACKEND MODELS** ✅
- [x] Verified existing models (Team, Idea, Workshop, News, Track)
- [x] Models have proper relationships and methods
- [x] TeamService with full CRUD operations
- [x] Repositories already exist for all entities

### **PHASE 4: CONTROLLERS & ROUTES** ✅
- [x] Created SystemAdmin/DashboardController with statistics
- [x] Created SystemAdmin/EditionController
- [x] Created HackathonAdmin/TeamController with service integration
- [x] Created HackathonAdmin/IdeaController with full review functionality
- [x] Created HackathonAdmin/WorkshopController with QR code generation
- [x] Created HackathonAdmin/NewsController with Twitter integration
- [x] Created HackathonAdmin/DashboardController with comprehensive stats
- [x] Added hackathon routes with role-based middleware protection
- [x] Routes organized by role prefix (system-admin/, hackathon-admin/)

### **PHASE 5: FRONTEND COMPONENTS** ✅
- [x] Created SystemAdmin Dashboard Vue component with statistics cards
- [x] Created HackathonAdmin/Dashboard/Index.vue with real-time stats
- [x] Created HackathonAdmin/Teams/Index.vue with filtering and pagination
- [x] Created HackathonAdmin/Teams/Show.vue with detailed team view
- [x] Created HackathonAdmin/Ideas/Index.vue with review pipeline
- [x] Created HackathonAdmin/Ideas/Show.vue with scoring breakdown
- [x] Created HackathonAdmin/Ideas/Review.vue with comprehensive review form
- [x] Created HackathonAdmin/Workshops/Index.vue basic structure
- [x] Created TeamLeader/Dashboard/Index.vue
- [x] Created TeamMember/Dashboard/Index.vue
- [x] Created TrackSupervisor/Dashboard/Index.vue
- [x] Created SystemAdmin/Editions/Index.vue
- [x] Dynamic navigation sidebar based on user's primary_role
- [x] Role-specific menu items with proper routes
- [x] Support for all 5 hackathon roles in navigation  

---

## 🚨 **LATEST ENHANCEMENTS COMPLETED**

✅ **Files Merged:** Combined both implementation plans into single comprehensive guide  
✅ **Role-Based FE Behavior:** Ultra-detailed frontend specifications for all 5 roles  
✅ **Sidebar & Login Process:** Exact navigation and authentication flows defined  
✅ **Request Validation:** All 35+ controller methods use specific Request classes  
✅ **Component Behavior:** Detailed role-based component rendering specifications  
✅ **Implementation Ready:** Zero-ambiguity instructions for immediate development  

### **PHASE 6: REQUEST CLASSES IMPLEMENTATION** ✅
- [x] Implemented ApproveTeamRequest with role validation
- [x] Created RejectTeamRequest with reason requirement
- [x] Implemented ReviewIdeaRequest with scoring validation
- [x] Created CreateWorkshopRequest with date/time validation
- [x] Created UpdateWorkshopRequest with participant limits
- [x] Implemented CreateNewsRequest with category validation
- [x] Created UpdateNewsRequest with image upload support
- [x] Created PublishNewsRequest with Twitter integration

### **PHASE 7: VUE COMPONENTS STRUCTURE** ✅
- [x] Created complete directory structure for all role-based Vue pages
- [x] Implemented 43+ Vue page files across all roles
- [x] HackathonAdmin: Complete Ideas management pages (Index, Show, Review)
- [x] HackathonAdmin: Dashboard with comprehensive statistics
- [x] HackathonAdmin: Teams management pages (Index, Show)
- [x] Basic implementation for Workshop, News, TrackSupervisor, TeamLeader, TeamMember pages
- [x] SystemAdmin: Basic Editions management page
- [x] All pages integrated with Default layout and proper routing  

---

## 🎯 **KEY FRONTEND ENHANCEMENTS ADDED**

### **🔐 LOGIN PROCESS FLOW**
```
📱 ROLE-BASED AUTHENTICATION & REDIRECTS:

1. system_admin → /system-admin/dashboard
   - Full system access dashboard
   - Edition management widgets
   - User analytics overview
   - System health monitoring

2. hackathon_admin → /hackathon-admin/dashboard  
   - Current edition stats
   - Team approval queue
   - Idea review pipeline
   - Workshop management

3. track_supervisor → /track-supervisor/dashboard
   - My track statistics only
   - Ideas pending my review
   - Track-specific workshops
   - Performance metrics

4. team_leader → /team-leader/dashboard
   - My team status overview
   - Idea submission progress
   - Team member management
   - Workshop registrations

5. team_member → /team-member/dashboard
   - Read-only team information
   - Workshop browse/register
   - Team timeline view
   - Basic participation info
```

### **🗂️ SIDEBAR NAVIGATION BY ROLE**
```
🎛️ ROLE-BASED MENU VISIBILITY:

SYSTEM ADMIN MENU:
├── 🏠 Dashboard
├── 🏆 Hackathon Editions (CRUD)
├── 👥 User Management (All roles)
├── ⚙️ System Settings (SMTP, Twitter, Branding)
├── 📊 Reports & Analytics (Cross-edition)
└── 🔧 System Tools (QR Scanner, Logs)

HACKATHON ADMIN MENU:
├── 🏠 Dashboard (Current edition)
├── 👥 Teams (Approve/Reject)
├── 💡 Ideas (Review/Assign)
├── 🏢 Workshops (CRUD + Attendance)
├── 📰 News (CRUD + Twitter)
└── 📊 Reports (Current edition only)

TRACK SUPERVISOR MENU:
├── 🏠 Dashboard (My track only)
├── 💡 Ideas Review (My track)
├── 🏢 My Workshops
└── 📊 Track Performance

TEAM LEADER MENU:
├── 🏠 Dashboard
├── 👥 My Team (CRUD members)
├── 💡 Our Idea (CRUD + Submit)
└── 🏢 Workshops (Browse + Register)

TEAM MEMBER MENU:
├── 🏠 Dashboard
├── 👥 My Team (Read-only)
└── 🏢 Workshops (Browse + Register)
```

### **🧩 COMPONENT BEHAVIOR SPECIFICATIONS**
```
🎨 ROLE-BASED COMPONENT RENDERING:

📋 DATA TABLES:
- system_admin: All data, full CRUD operations
- hackathon_admin: Current edition data, approve/reject
- track_supervisor: Track-filtered data, review only
- team_leader: Own team data, edit own content
- team_member: View-only access, no actions

📊 DASHBOARD WIDGETS:
- system_admin: Multi-edition aggregated stats
- hackathon_admin: Current edition detailed metrics
- track_supervisor: Single track focused data
- team_leader: Team-specific progress tracking
- team_member: Basic team information display

📝 FORMS:
- system_admin: All form fields, no restrictions
- hackathon_admin: Edition-scoped forms only
- track_supervisor: Review forms for assigned track
- team_leader: Team & idea forms only
- team_member: No create/edit forms, registration only
```

---

## 🗂️ **ULTRA-DETAILED IMPLEMENTATION PHASES**

### **PHASE 1: AUTHENTICATION & ROLE SETUP (45 minutes)**

#### **🔐 Role-Based Middleware (15 minutes)**
```bash
# Create role-based middleware
php artisan make:middleware SystemAdminMiddleware
php artisan make:middleware HackathonAdminMiddleware
php artisan make:middleware TrackSupervisorMiddleware
php artisan make:middleware TeamLeaderMiddleware
php artisan make:middleware TeamMemberMiddleware

# Register in app/Http/Kernel.php
'role' => \Spatie\Permission\Middlewares\RoleMiddleware::class,
'system_admin' => \App\Http\Middleware\SystemAdminMiddleware::class,
'hackathon_admin' => \App\Http\Middleware\HackathonAdminMiddleware::class,
# ... etc
```

#### **📝 Request Classes Creation (30 minutes)**
```bash
# Create directory structure
mkdir -p app/Http/Requests/{Auth,Team,Idea,Workshop,News,Settings,Edition,SystemAdmin,HackathonAdmin,TrackSupervisor,TeamLeader}

# System Admin requests (8 classes) - 8 minutes
php artisan make:request SystemAdmin/CreateUserRequest
php artisan make:request SystemAdmin/UpdateUserRequest  
php artisan make:request SystemAdmin/CreateHackathonEditionRequest
php artisan make:request SystemAdmin/UpdateHackathonEditionRequest
php artisan make:request SystemAdmin/UpdateSmtpSettingsRequest
php artisan make:request SystemAdmin/UpdateBrandingSettingsRequest
php artisan make:request SystemAdmin/UpdateTwitterSettingsRequest
php artisan make:request SystemAdmin/SystemReportRequest

# Hackathon Admin requests (10 classes) - 10 minutes
php artisan make:request HackathonAdmin/TeamIndexRequest
php artisan make:request HackathonAdmin/ApproveTeamRequest
php artisan make:request HackathonAdmin/RejectTeamRequest
php artisan make:request HackathonAdmin/IdeaIndexRequest
php artisan make:request HackathonAdmin/ReviewIdeaRequest
php artisan make:request HackathonAdmin/CreateWorkshopRequest
php artisan make:request HackathonAdmin/UpdateWorkshopRequest
php artisan make:request HackathonAdmin/CreateNewsRequest
php artisan make:request HackathonAdmin/UpdateNewsRequest
php artisan make:request HackathonAdmin/PublishNewsRequest

# Track Supervisor requests (4 classes) - 4 minutes
php artisan make:request TrackSupervisor/IdeaIndexRequest
php artisan make:request TrackSupervisor/ReviewIdeaRequest
php artisan make:request TrackSupervisor/AddIdeaCommentRequest
php artisan make:request TrackSupervisor/WorkshopAttendanceRequest

# Team Leader requests (6 classes) - 6 minutes
php artisan make:request TeamLeader/CreateTeamRequest
php artisan make:request TeamLeader/UpdateTeamRequest
php artisan make:request TeamLeader/InviteMemberRequest
php artisan make:request TeamLeader/CreateIdeaRequest
php artisan make:request TeamLeader/UpdateIdeaRequest
php artisan make:request TeamLeader/SubmitIdeaRequest

# Team Member requests (2 classes) - 2 minutes
php artisan make:request TeamMember/RegisterWorkshopRequest
php artisan make:request TeamMember/LeaveTeamRequest
```

**✅ PHASE 1 COMPLETION CRITERIA:**
- [ ] All 30 Request classes created with role-based validation
- [ ] Role-based middleware configured and registered
- [ ] HandleInertiaRequests.php enhanced with role-specific data sharing
- [ ] Authentication redirects working per role specifications

---

### **PHASE 2: BACKEND CONTROLLERS (60 minutes)**

#### **🔴 System Admin Controllers (6 controllers) - 18 minutes**
```bash
# Create SystemAdmin directory
mkdir -p app/Http/Controllers/SystemAdmin

php artisan make:controller SystemAdmin/DashboardController
  # Methods: index() - Multi-edition dashboard with system health
  # Uses: No specific request (just auth check)
  # Returns: Inertia page with aggregated statistics

php artisan make:controller SystemAdmin/HackathonEditionController --resource
  # Methods: index(), create(), store(), show(), edit(), update(), destroy()
  # Additional: archive(), setCurrent(), statistics()
  # Uses: CreateHackathonEditionRequest, UpdateHackathonEditionRequest
  # Returns: Inertia pages for edition management

php artisan make:controller SystemAdmin/UserController --resource  
  # Methods: index(), create(), store(), show(), edit(), update()
  # Additional: changeRole(), toggleStatus(), resetPassword(), loginHistory()
  # Uses: CreateUserRequest, UpdateUserRequest
  # Returns: User management interface

php artisan make:controller SystemAdmin/SettingsController
  # Methods: index(), updateSmtp(), updateBranding(), updateTwitter(), testConnection()
  # Uses: UpdateSmtpSettingsRequest, UpdateBrandingSettingsRequest, UpdateTwitterSettingsRequest
  # Returns: Multi-tab settings interface

php artisan make:controller SystemAdmin/ReportController
  # Methods: index(), exportUsers(), exportTeams(), exportIdeas(), systemHealth()
  # Uses: SystemReportRequest
  # Returns: Analytics dashboard and export functionality

php artisan make:controller SystemAdmin/QRScannerController
  # Methods: scanWorkshop(), markAttendance()
  # Uses: No specific request (mobile-optimized)
  # Returns: QR scanner interface for workshop attendance
```

#### **🟡 Hackathon Admin Controllers (5 controllers) - 15 minutes**
```bash
# Create HackathonAdmin directory  
mkdir -p app/Http/Controllers/HackathonAdmin

php artisan make:controller HackathonAdmin/DashboardController
  # Methods: index() - Current edition statistics only
  # Data Scope: Current hackathon edition only
  # Widgets: Teams (pending/approved), Ideas (review pipeline), Workshops (upcoming)

php artisan make:controller HackathonAdmin/TeamController
  # Methods: index(), show(), approve(), reject(), exportTeams()
  # Uses: TeamIndexRequest, ApproveTeamRequest, RejectTeamRequest
  # Data Scope: Teams in current edition only
  # Actions: Bulk approve/reject, team details, export to Excel

php artisan make:controller HackathonAdmin/IdeaController  
  # Methods: index(), show(), review(), assignSupervisor(), exportIdeas()
  # Uses: IdeaIndexRequest, ReviewIdeaRequest
  # Data Scope: Ideas in current edition only
  # Actions: Review pipeline, supervisor assignment, scoring

php artisan make:controller HackathonAdmin/WorkshopController --resource
  # Methods: index(), create(), store(), show(), edit(), update(), destroy()
  # Additional: attendance(), exportAttendance(), generateQR()
  # Uses: CreateWorkshopRequest, UpdateWorkshopRequest
  # Features: Workshop CRUD, QR code generation, attendance tracking

php artisan make:controller HackathonAdmin/NewsController --resource
  # Methods: index(), create(), store(), show(), edit(), update(), destroy()
  # Additional: publish(), tweet(), schedule()
  # Uses: CreateNewsRequest, UpdateNewsRequest, PublishNewsRequest
  # Features: News CRUD, Twitter auto-posting, scheduled publishing
```

#### **🟢 Track Supervisor Controllers (3 controllers) - 9 minutes**
```bash
# Create TrackSupervisor directory
mkdir -p app/Http/Controllers/TrackSupervisor

php artisan make:controller TrackSupervisor/DashboardController
  # Methods: index() - Track-specific statistics only
  # Data Scope: User's assigned track(s) only
  # Widgets: My track teams, Ideas pending review, Track performance

php artisan make:controller TrackSupervisor/IdeaController
  # Methods: index(), show(), review(), addComment(), requestRevision()
  # Uses: IdeaIndexRequest, ReviewIdeaRequest, AddIdeaCommentRequest
  # Data Scope: Ideas from assigned track only
  # Actions: Detailed review with scoring criteria, feedback comments

php artisan make:controller TrackSupervisor/WorkshopController  
  # Methods: index(), show(), markAttendance()
  # Uses: WorkshopAttendanceRequest
  # Data Scope: Workshops related to assigned track
  # Actions: View workshop details, scan QR for attendance
```

#### **🔵 Team Leader Controllers (3 controllers) - 9 minutes**
```bash
# Create TeamLeader directory
mkdir -p app/Http/Controllers/TeamLeader

php artisan make:controller TeamLeader/DashboardController
  # Methods: index() - Own team dashboard
  # Data Scope: User's team only
  # Widgets: Team status, Idea progress, Member activity, Upcoming deadlines

php artisan make:controller TeamLeader/TeamController
  # Methods: show(), edit(), update(), inviteMember(), removeMember(), disbandTeam()
  # Uses: UpdateTeamRequest, InviteMemberRequest
  # Data Scope: Own team only
  # Actions: Team management, member invitations, team dissolution

php artisan make:controller TeamLeader/IdeaController
  # Methods: show(), create(), store(), edit(), update(), submit(), withdraw()
  # Uses: CreateIdeaRequest, UpdateIdeaRequest, SubmitIdeaRequest
  # Data Scope: Own team's idea only
  # Actions: Idea CRUD, submission workflow, file uploads (8 files, 15MB each)
```

#### **🟣 Team Member Controllers (3 controllers) - 9 minutes**
```bash
# Create TeamMember directory
mkdir -p app/Http/Controllers/TeamMember

php artisan make:controller TeamMember/DashboardController
  # Methods: index() - Read-only team information
  # Data Scope: Own team info only
  # Widgets: Team details, Idea status, Workshop schedule, Timeline

php artisan make:controller TeamMember/TeamController
  # Methods: show(), leaveTeam(), contactLeader()
  # Uses: LeaveTeamRequest
  # Data Scope: Own team only (read-only)
  # Actions: View team, leave team, message leader

php artisan make:controller TeamMember/WorkshopController
  # Methods: index(), show(), register(), unregister(), downloadCertificate()
  # Uses: RegisterWorkshopRequest
  # Data Scope: Available workshops only
  # Actions: Browse workshops, register/cancel, download attendance certificate
```

**✅ PHASE 2 COMPLETION CRITERIA:**
- [ ] All 20 controllers created in role-based directories
- [ ] Each controller method uses appropriate Request class validation
- [ ] Data scoping implemented correctly per role restrictions
- [ ] All controllers return proper Inertia responses
- [ ] Role-based authorization implemented in each method

---

### **PHASE 3: SERVICES & REPOSITORIES (45 minutes)**

#### **⚙️ Core Services (8 services) - 30 minutes**
```bash
# Create services directory
mkdir -p app/Services/{SystemAdmin,HackathonAdmin,TrackSupervisor,TeamLeader,TeamMember}

# System-wide services (15 minutes)
php artisan make:service HackathonEditionService
  # Methods: create(), update(), setCurrent(), archive(), getStatistics()
  # Handles: Multi-edition management, edition switching, archival

php artisan make:service UserService
  # Methods: create(), update(), changeRole(), resetPassword(), getLoginHistory()
  # Handles: User management, role assignments, password operations

php artisan make:service SettingsService
  # Methods: updateSmtp(), updateBranding(), updateTwitter(), testConnections()
  # Handles: System configuration, API integrations

php artisan make:service NotificationService
  # Methods: sendTeamApproval(), sendIdeaFeedback(), sendWorkshopReminder()
  # Handles: Email/SMS notifications, Twitter posting

# Business logic services (15 minutes)
php artisan make:service TeamService
  # Methods: create(), approve(), reject(), addMember(), removeMember()
  # Handles: Team lifecycle, member management, approval workflow

php artisan make:service IdeaService
  # Methods: create(), submit(), review(), score(), assignSupervisor()
  # Handles: Idea submission, review pipeline, file management

php artisan make:service WorkshopService
  # Methods: create(), generateQR(), markAttendance(), sendReminders()
  # Handles: Workshop CRUD, QR generation, attendance tracking

php artisan make:service NewsService
  # Methods: create(), publish(), tweet(), schedule()
  # Handles: News management, Twitter integration, scheduling
```

#### **🗄️ Repository Pattern (15 minutes)**
```bash
# Create repositories for complex queries
mkdir -p app/Repositories

php artisan make:repository TeamRepository
  # Methods: getByRole(), getWithFilters(), getPendingApproval()
  # Handles: Complex team queries, filtering, role-based data access

php artisan make:repository IdeaRepository  
  # Methods: getForReview(), getByTrack(), getWithScores()
  # Handles: Idea filtering, review pipeline queries, track-based access

php artisan make:repository WorkshopRepository
  # Methods: getAvailable(), getWithAttendance(), getByDate()
  # Handles: Workshop availability, attendance reports, scheduling

php artisan make:repository UserRepository
  # Methods: getByRole(), getWithPermissions(), getLoginStats()
  # Handles: User queries, role-based filtering, activity tracking
```

**✅ PHASE 3 COMPLETION CRITERIA:**
- [ ] All 8 services created with business logic methods
- [ ] 4 repositories created for complex data operations
- [ ] Service-Repository pattern properly implemented
- [ ] Controllers properly inject and use services
- [ ] No direct model access in controllers

---

### **PHASE 4: FRONTEND PAGES & COMPONENTS (75 minutes)**

#### **🔴 System Admin Pages (15 pages) - 25 minutes**
```bash
# Create SystemAdmin page directories
mkdir -p resources/js/Pages/SystemAdmin/{Dashboard,Editions,Users,Settings,Reports,QR}

# Dashboard (1 page)
touch resources/js/Pages/SystemAdmin/Dashboard/Index.vue
  # Components: MultiEditionStats, SystemHealth, UserAnalytics, ActivityLog
  # Data: Aggregated stats across all editions, system metrics
  # Actions: Quick navigation to critical functions

# Edition Management (4 pages)
touch resources/js/Pages/SystemAdmin/Editions/Index.vue      # Editions table with filters
  # Components: EditionsTable, CreateButton, ArchiveAction
  # Features: Year filter, status filter, pagination, search
  
touch resources/js/Pages/SystemAdmin/Editions/Create.vue     # Edition creation form  
  # Components: EditionForm, DatePickers, ValidationErrors
  # Validation: Year uniqueness, date logic, required fields
  
touch resources/js/Pages/SystemAdmin/Editions/Edit.vue       # Edition editing
  # Components: EditionForm (prefilled), StatusSelector
  # Features: Status management, date adjustments, settings
  
touch resources/js/Pages/SystemAdmin/Editions/Show.vue       # Edition details
  # Components: EditionStats, TeamsList, IdeasSummary
  # Data: Complete edition overview, performance metrics

# User Management (4 pages)  
touch resources/js/Pages/SystemAdmin/Users/Index.vue         # Users table
  # Components: UsersTable, RoleFilter, StatusFilter, BulkActions
  # Features: Multi-role filtering, status management, export
  
touch resources/js/Pages/SystemAdmin/Users/Create.vue        # User creation
  # Components: UserForm, RoleSelector, PasswordGenerator
  # Validation: Email uniqueness, role assignment, password strength
  
touch resources/js/Pages/SystemAdmin/Users/Edit.vue          # User editing
  # Components: UserForm, RoleManager, PasswordReset
  # Features: Role changes, status toggle, login history
  
touch resources/js/Pages/SystemAdmin/Users/Show.vue          # User profile
  # Components: UserProfile, ActivityHistory, LoginStats
  # Data: Complete user overview, activity tracking

# Settings (4 pages)
touch resources/js/Pages/SystemAdmin/Settings/Index.vue      # Settings dashboard
  # Components: SettingsTabs, ConfigurationStatus, TestResults
  # Layout: Tabbed interface for different setting categories
  
touch resources/js/Pages/SystemAdmin/Settings/Smtp.vue       # Email configuration
  # Components: SmtpForm, TestEmailButton, ConnectionStatus
  # Features: SMTP configuration, connection testing, email templates
  
touch resources/js/Pages/SystemAdmin/Settings/Branding.vue   # Branding settings
  # Components: LogoUpload, ColorPicker, PreviewPanel
  # Features: Logo management, color schemes, UI customization
  
touch resources/js/Pages/SystemAdmin/Settings/Twitter.vue    # Twitter API
  # Components: TwitterForm, TestTweetButton, PostHistory
  # Features: API configuration, connection testing, posting history

# Reports & QR (2 pages)
touch resources/js/Pages/SystemAdmin/Reports/Index.vue       # Analytics dashboard
  # Components: ReportsGrid, ExportButtons, DateRangeFilter
  # Features: Cross-edition analytics, export functionality
  
touch resources/js/Pages/SystemAdmin/QRScanner.vue           # Workshop attendance
  # Components: QRScanner, AttendanceList, WorkshopSelector
  # Features: Mobile-optimized QR scanning, attendance marking
```

#### **🟡 Hackathon Admin Pages (12 pages) - 20 minutes**
```bash
# Create HackathonAdmin directories
mkdir -p resources/js/Pages/HackathonAdmin/{Dashboard,Teams,Ideas,Workshops,News}

# Dashboard (1 page)
touch resources/js/Pages/HackathonAdmin/Dashboard/Index.vue
  # Components: CurrentEditionStats, TeamApprovalQueue, IdeaReviewPipeline
  # Data: Current edition only, real-time approval stats
  # Actions: Quick approve/reject, batch operations

# Team Management (2 pages)
touch resources/js/Pages/HackathonAdmin/Teams/Index.vue      # Teams with approval
  # Components: TeamsTable, ApprovalActions, StatusFilters, ExportButton
  # Features: Bulk approve/reject, team details modal, export to Excel
  # Data: Current edition teams only, with approval workflow
  
touch resources/js/Pages/HackathonAdmin/Teams/Show.vue       # Team details
  # Components: TeamProfile, MembersList, IdeaStatus, ApprovalHistory
  # Features: Detailed team view, member management, idea tracking
  # Actions: Approve/reject with comments, view related idea

# Idea Management (3 pages)  
touch resources/js/Pages/HackathonAdmin/Ideas/Index.vue      # Ideas pipeline
  # Components: IdeasTable, ReviewFilters, SupervisorAssignment
  # Features: Review status tracking, supervisor assignment, scoring overview
  # Data: Current edition ideas with review pipeline status
  
touch resources/js/Pages/HackathonAdmin/Ideas/Show.vue       # Idea details
  # Components: IdeaDisplay, FileViewer, ReviewHistory, ScoreBreakdown
  # Features: Complete idea view, file downloads, review tracking
  # Data: Detailed idea with all files and review history
  
touch resources/js/Pages/HackathonAdmin/Ideas/Review.vue     # Review interface
  # Components: ReviewForm, ScoringCriteria, FileViewer, CommentSection
  # Features: Comprehensive review with scoring, feedback, status change
  # Actions: Approve/reject/revision with detailed feedback

# Workshop Management (3 pages)
touch resources/js/Pages/HackathonAdmin/Workshops/Index.vue  # Workshops table
  # Components: WorkshopsTable, CreateButton, AttendanceView
  # Features: Workshop CRUD, attendance tracking, QR code generation
  # Data: All workshops with registration and attendance stats
  
touch resources/js/Pages/HackathonAdmin/Workshops/Create.vue # Workshop creation
  # Components: WorkshopForm, SpeakerSelector, DateTimePicker, QRGeneration
  # Features: Complete workshop setup, speaker assignment, QR generation
  # Validation: Date conflicts, speaker availability, capacity limits
  
touch resources/js/Pages/HackathonAdmin/Workshops/Edit.vue   # Workshop editing
  # Components: WorkshopForm (prefilled), AttendanceReport, QRRegenerator
  # Features: Workshop updates, attendance reports, QR regeneration
  # Actions: Update details, view attendance, export attendee list

# News Management (3 pages)
touch resources/js/Pages/HackathonAdmin/News/Index.vue       # News table
  # Components: NewsTable, CreateButton, PublishButton, TwitterStatus
  # Features: News CRUD, publishing workflow, Twitter integration
  # Data: All news articles with publishing and Twitter status
  
touch resources/js/Pages/HackathonAdmin/News/Create.vue      # News creation  
  # Components: NewsForm, RichTextEditor, ImageUpload, TwitterPreview
  # Features: Rich content creation, image uploads, Twitter preview
  # Validation: Content length, image size, Twitter character limits
  
touch resources/js/Pages/HackathonAdmin/News/Edit.vue        # News editing
  # Components: NewsForm (prefilled), PublishingOptions, TwitterManager
  # Features: Content updates, publishing control, Twitter management
  # Actions: Update, publish/unpublish, tweet/retweet
```

#### **🟢 Track Supervisor Pages (6 pages) - 10 minutes**
```bash
# Create TrackSupervisor directories
mkdir -p resources/js/Pages/TrackSupervisor/{Dashboard,Ideas,Workshops}

# Dashboard (1 page)
touch resources/js/Pages/TrackSupervisor/Dashboard/Index.vue
  # Components: TrackStats, PendingReviews, MyWorkshops, TrackPerformance
  # Data: Track-specific data only, review queue for assigned track
  # Restrictions: Cannot see other tracks' data

# Ideas Review (2 pages)
touch resources/js/Pages/TrackSupervisor/Ideas/Index.vue     # Track ideas only
  # Components: IdeasTable (track-filtered), ReviewStatus, QuickReview
  # Data: Ideas from assigned track only, with review status
  # Features: Track-specific filtering, review queue management
  # Restrictions: Only assigned track ideas visible
  
touch resources/js/Pages/TrackSupervisor/Ideas/Review.vue    # Review interface
  # Components: DetailedReviewForm, ScoringMatrix, CommentSystem, FileViewer
  # Features: Comprehensive idea review with detailed scoring
  # Actions: Score by criteria, add feedback, request revisions
  # Data: Single idea with complete review workflow

# Workshops (3 pages)  
touch resources/js/Pages/TrackSupervisor/Workshops/Index.vue # Track workshops
  # Components: WorkshopsTable (track-filtered), AttendanceOverview
  # Data: Workshops related to assigned track
  # Features: Workshop scheduling, attendance monitoring
  # Restrictions: Track-specific workshop access only
  
touch resources/js/Pages/TrackSupervisor/Workshops/Show.vue  # Workshop details
  # Components: WorkshopDetails, AttendanceList, QRScanner
  # Features: Workshop information, attendee management
  # Actions: View details, scan QR for attendance
  
touch resources/js/Pages/TrackSupervisor/QRScanner.vue       # Mobile QR scanner
  # Components: MobileOptimizedScanner, AttendanceMarker, WorkshopSelector
  # Features: Mobile-first QR scanning for workshop attendance
  # Actions: Scan QR codes, mark attendance, view attendee list
```

#### **🔵 Team Leader Pages (6 pages) - 10 minutes**
```bash
# Create TeamLeader directories
mkdir -p resources/js/Pages/TeamLeader/{Dashboard,Team,Idea}

# Dashboard (1 page)
touch resources/js/Pages/TeamLeader/Dashboard/Index.vue
  # Components: TeamStatusCard, IdeaProgressTracker, DeadlineReminder, WorkshopSchedule
  # Data: Own team data only, idea submission status, upcoming deadlines
  # Features: Team overview, progress tracking, deadline management

# Team Management (2 pages)
touch resources/js/Pages/TeamLeader/Team/Show.vue            # Team overview
  # Components: TeamProfile, MembersList, InviteButton, TeamStats
  # Data: Own team information, member details, team performance
  # Actions: View team, manage members, invite new members
  # Restrictions: Own team data only
  
touch resources/js/Pages/TeamLeader/Team/Edit.vue            # Team editing
  # Components: TeamEditForm, MemberManager, InvitationSystem
  # Features: Team information updates, member management
  # Actions: Update team details, add/remove members, send invitations
  # Validation: Team name uniqueness, member limits (2-5 members)

# Idea Management (3 pages)
touch resources/js/Pages/TeamLeader/Idea/Show.vue            # Idea overview
  # Components: IdeaDisplay, SubmissionStatus, ReviewFeedback, FileManager
  # Data: Own team's idea, submission status, review feedback
  # Features: Idea overview, status tracking, feedback viewing
  # Restrictions: Own team's idea only
  
touch resources/js/Pages/TeamLeader/Idea/Edit.vue            # Idea editing
  # Components: IdeaEditForm, FileUploader, SaveDraftButton, SubmitButton
  # Features: Comprehensive idea editing with file management
  # Actions: Update idea, manage files (8 max, 15MB each), save draft
  # Validation: Required fields, file limits, content restrictions
  
touch resources/js/Pages/TeamLeader/Idea/Submit.vue          # Submission workflow
  # Components: SubmissionForm, FileValidator, ConfirmationDialog, SubmitButton
  # Features: Final idea submission with validation and confirmation
  # Actions: Final submission (locks editing), confirmation dialog
  # Restrictions: Cannot edit after submission
```

#### **🟣 Team Member Pages (4 pages) - 10 minutes**
```bash
# Create TeamMember directories
mkdir -p resources/js/Pages/TeamMember/{Dashboard,Team,Workshops}

# Dashboard (1 page)
touch resources/js/Pages/TeamMember/Dashboard/Index.vue
  # Components: TeamInfoCard, IdeaStatusCard, WorkshopScheduleCard, TimelineCard
  # Data: Own team information (read-only), workshop schedule
  # Features: Team overview, idea status viewing, workshop browsing
  # Restrictions: Read-only access to team data

# Team View (1 page)  
touch resources/js/Pages/TeamMember/Team/Show.vue            # Read-only team view
  # Components: TeamProfile (readonly), MembersDisplay, IdeaStatusDisplay, TeamTimeline
  # Data: Own team details, member information, idea progress
  # Features: Team information viewing, progress tracking
  # Actions: Leave team (with confirmation), contact team leader
  # Restrictions: No editing capabilities, view-only access

# Workshop Management (2 pages)
touch resources/js/Pages/TeamMember/Workshops/Index.vue      # Available workshops
  # Components: WorkshopsGrid, FilterPanel, RegistrationButton, MyRegistrations
  # Data: Available workshops, registration status, my registrations
  # Features: Workshop browsing, filtering, registration management
  # Actions: Register for workshops, cancel registrations
  
touch resources/js/Pages/TeamMember/Workshops/Show.vue       # Workshop details
  # Components: WorkshopDetails, RegistrationButton, CalendarExport, CertificateDownload
  # Features: Workshop information, registration management
  # Actions: Register/unregister, add to calendar, download certificates
  # Data: Workshop details, registration status, attendance record
```

**✅ PHASE 4 COMPLETION CRITERIA:**
- [ ] All 43 Vue pages created in role-based directories
- [ ] Each page implements proper role-based data access
- [ ] Components created for reusable functionality
- [ ] Proper Inertia.js data flow implemented
- [ ] Role-based UI rendering working correctly

---

### **PHASE 5: COMPONENTS & NAVIGATION (45 minutes)**

#### **🧩 Role-Based Components (20 components) - 30 minutes**
```bash
# Create component directories
mkdir -p resources/js/Components/{Role,Dashboard,Tables,Forms,Workshop,QR,Navigation}

# Role Management Components (5 components) - 8 minutes
touch resources/js/Components/Role/RoleBasedNavigation.vue    # Dynamic menu by role
  # Props: userRoles (array), currentRole (string)
  # Features: Dynamic menu generation, permission-based visibility
  # Behavior: Shows/hides menu items based on user roles
  
touch resources/js/Components/Role/PermissionGate.vue        # Conditional rendering
  # Props: permission (string), role (string), fallback (component)
  # Features: Wraps content with permission checking
  # Behavior: Renders children only if user has permission
  
touch resources/js/Components/Role/RoleSwitcher.vue          # Multi-role users
  # Props: availableRoles (array), currentRole (string)
  # Features: Role switching for multi-role users
  # Behavior: Allows switching between assigned roles
  
touch resources/js/Components/Role/DashboardWidget.vue       # Reusable dashboard widget
  # Props: title, data, type, roleRestrictions
  # Features: Configurable dashboard widget with role-based data
  # Behavior: Adapts display and actions based on user role
  
touch resources/js/Components/Role/ActionButton.vue          # Role-aware actions
  # Props: action, permission, role, variant
  # Features: Buttons that show/hide based on permissions
  # Behavior: Renders appropriate button styles and permissions

# Dashboard Components (4 components) - 8 minutes  
touch resources/js/Components/Dashboard/StatsCard.vue        # Statistics display
  # Props: title, value, change, icon, color, clickable
  # Features: Reusable stat card with trend indicators
  # Behavior: Shows stats with optional trend arrows
  
touch resources/js/Components/Dashboard/ActivityFeed.vue     # Activity timeline
  # Props: activities (array), maxItems (number), showTime
  # Features: Timeline of recent activities with time stamps
  # Behavior: Shows recent system/team activities
  
touch resources/js/Components/Dashboard/QuickActions.vue     # Context-aware actions
  # Props: userRole, availableActions, context
  # Features: Quick action buttons based on user role
  # Behavior: Shows relevant actions for current user's role
  
touch resources/js/Components/Dashboard/NotificationPanel.vue # Notifications
  # Props: notifications, dismissible, maxHeight
  # Features: Notification display with dismissal
  # Behavior: Shows role-specific notifications

# Table Components (4 components) - 6 minutes
touch resources/js/Components/Tables/DataTable.vue          # Enhanced data table
  # Props: columns, data, actions, filters, pagination, roleActions
  # Features: Sortable, filterable table with role-based actions
  # Behavior: Shows/hides columns and actions based on user role
  
touch resources/js/Components/Tables/TeamTable.vue          # Teams-specific table
  # Props: teams, showActions, approvalMode, roleFilter
  # Features: Team listing with approval workflow
  # Behavior: Different views for different roles (approve/view/manage)
  
touch resources/js/Components/Tables/IdeaTable.vue          # Ideas-specific table
  # Props: ideas, reviewMode, trackFilter, scoreVisible
  # Features: Ideas listing with review status and scoring
  # Behavior: Track filtering for supervisors, review actions
  
touch resources/js/Components/Tables/WorkshopTable.vue      # Workshops table
  # Props: workshops, registrationMode, attendanceMode, qrMode
  # Features: Workshop listing with registration/attendance
  # Behavior: Registration for members, attendance for supervisors

# Form Components (4 components) - 8 minutes
touch resources/js/Components/Forms/DynamicForm.vue         # Configurable form
  # Props: fields, validation, submitAction, roleRestrictions
  # Features: Dynamic form generation with role-based field visibility
  # Behavior: Shows/hides fields based on user permissions
  
touch resources/js/Components/Forms/FileUploader.vue        # Multi-file uploader
  # Props: maxFiles, maxSize, allowedTypes, existingFiles
  # Features: Drag-drop file upload with validation (8 files, 15MB each)
  # Behavior: File validation, progress tracking, thumbnail preview
  
touch resources/js/Components/Forms/RichTextEditor.vue      # WYSIWYG editor
  # Props: content, placeholder, toolbar, readOnly
  # Features: Rich text editing for news/ideas
  # Behavior: Read-only mode for restricted users
  
touch resources/js/Components/Forms/ValidationErrors.vue    # Error display
  # Props: errors, field, type, dismissible
  # Features: Consistent error display across forms
  # Behavior: Shows validation errors with proper styling

# Workshop & QR Components (3 components) - 5 minutes
touch resources/js/Components/Workshop/WorkshopCard.vue      # Workshop display
  # Props: workshop, registrationEnabled, attendanceMode, userRole
  # Features: Workshop information with role-based actions
  # Behavior: Registration for members, attendance for supervisors
  
touch resources/js/Components/QR/QRGenerator.vue            # QR code generation
  # Props: data, size, format, logo
  # Features: QR code generation for workshop attendance
  # Behavior: Generates unique QR codes for workshop registration
  
touch resources/js/Components/QR/QRScanner.vue              # QR code scanning
  # Props: onScan, continuous, beep, torch
  # Features: Camera-based QR scanning with audio feedback
  # Behavior: Mobile-optimized scanning for attendance marking
```

#### **🗂️ Navigation Enhancement (15 minutes)**
```bash
# Update existing navigation component
# File: resources/js/Components/NavSidebarDesktop.vue

# Add role-based menu configuration:
const menuConfig = {
    system_admin: [
        { name: 'Dashboard', route: 'system-admin.dashboard', icon: 'HomeIcon' },
        { name: 'Hackathon Editions', route: 'system-admin.editions.index', icon: 'TrophyIcon' },
        { name: 'User Management', route: 'system-admin.users.index', icon: 'UsersIcon' },
        { name: 'System Settings', route: 'system-admin.settings.index', icon: 'CogIcon' },
        { name: 'Reports & Analytics', route: 'system-admin.reports.index', icon: 'ChartBarIcon' },
        { name: 'QR Scanner', route: 'system-admin.qr-scanner', icon: 'QrcodeIcon' }
    ],
    
    hackathon_admin: [
        { name: 'Dashboard', route: 'hackathon-admin.dashboard', icon: 'HomeIcon' },
        { name: 'Teams', route: 'hackathon-admin.teams.index', icon: 'UsersIcon' },
        { name: 'Ideas', route: 'hackathon-admin.ideas.index', icon: 'LightBulbIcon' },
        { name: 'Workshops', route: 'hackathon-admin.workshops.index', icon: 'AcademicCapIcon' },
        { name: 'News', route: 'hackathon-admin.news.index', icon: 'NewspaperIcon' }
    ],
    
    track_supervisor: [
        { name: 'Dashboard', route: 'track-supervisor.dashboard', icon: 'HomeIcon' },
        { name: 'Ideas Review', route: 'track-supervisor.ideas.index', icon: 'LightBulbIcon' },
        { name: 'My Workshops', route: 'track-supervisor.workshops.index', icon: 'AcademicCapIcon' },
        { name: 'QR Scanner', route: 'track-supervisor.qr-scanner', icon: 'QrcodeIcon' }
    ],
    
    team_leader: [
        { name: 'Dashboard', route: 'team-leader.dashboard', icon: 'HomeIcon' },
        { name: 'My Team', route: 'team-leader.team.show', icon: 'UsersIcon' },
        { name: 'Our Idea', route: 'team-leader.idea.show', icon: 'LightBulbIcon' },
        { name: 'Workshops', route: 'team-member.workshops.index', icon: 'AcademicCapIcon' }
    ],
    
    team_member: [
        { name: 'Dashboard', route: 'team-member.dashboard', icon: 'HomeIcon' },
        { name: 'My Team', route: 'team-member.team.show', icon: 'UsersIcon' },
        { name: 'Workshops', route: 'team-member.workshops.index', icon: 'AcademicCapIcon' }
    ]
};

# Add role detection logic:
const userRole = computed(() => {
    const roles = user.value?.roles || [];
    // Priority order: system_admin > hackathon_admin > track_supervisor > team_leader > team_member
    if (roles.includes('system_admin')) return 'system_admin';
    if (roles.includes('hackathon_admin')) return 'hackathon_admin';
    if (roles.includes('track_supervisor')) return 'track_supervisor';
    if (roles.includes('team_leader')) return 'team_leader';
    if (roles.includes('team_member')) return 'team_member';
    return null;
});

# Add menu generation:
const currentMenu = computed(() => {
    return menuConfig[userRole.value] || [];
});
```

**✅ PHASE 5 COMPLETION CRITERIA:**
- [ ] All 20 role-aware components created and functional
- [ ] Navigation updated with role-based menus  
- [ ] Components properly handle role-based visibility
- [ ] Reusable components work across different roles
- [ ] Navigation accurately reflects user permissions

---

### **PHASE 6: ROUTES & MIDDLEWARE (30 minutes)**

#### **🛣️ Role-Based Routes Configuration (20 minutes)**
```php
// File: routes/web.php

// System Admin Routes (Full Access)
Route::middleware(['auth', 'role:system_admin'])->prefix('system-admin')->name('system-admin.')->group(function () {
    Route::get('/dashboard', [SystemAdmin\DashboardController::class, 'index'])->name('dashboard');
    
    // Hackathon Editions Management
    Route::resource('editions', SystemAdmin\HackathonEditionController::class);
    Route::post('editions/{edition}/set-current', [SystemAdmin\HackathonEditionController::class, 'setCurrent'])->name('editions.set-current');
    Route::post('editions/{edition}/archive', [SystemAdmin\HackathonEditionController::class, 'archive'])->name('editions.archive');
    
    // User Management
    Route::resource('users', SystemAdmin\UserController::class);
    Route::post('users/{user}/change-role', [SystemAdmin\UserController::class, 'changeRole'])->name('users.change-role');
    Route::post('users/{user}/toggle-status', [SystemAdmin\UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::post('users/{user}/reset-password', [SystemAdmin\UserController::class, 'resetPassword'])->name('users.reset-password');
    Route::get('users/{user}/login-history', [SystemAdmin\UserController::class, 'loginHistory'])->name('users.login-history');
    
    // System Settings
    Route::get('/settings', [SystemAdmin\SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings/smtp', [SystemAdmin\SettingsController::class, 'updateSmtp'])->name('settings.smtp');
    Route::put('/settings/branding', [SystemAdmin\SettingsController::class, 'updateBranding'])->name('settings.branding');
    Route::put('/settings/twitter', [SystemAdmin\SettingsController::class, 'updateTwitter'])->name('settings.twitter');
    Route::post('/settings/test-smtp', [SystemAdmin\SettingsController::class, 'testSmtp'])->name('settings.test-smtp');
    
    // Reports & Analytics
    Route::get('/reports', [SystemAdmin\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export-users', [SystemAdmin\ReportController::class, 'exportUsers'])->name('reports.export-users');
    Route::get('/reports/export-teams', [SystemAdmin\ReportController::class, 'exportTeams'])->name('reports.export-teams');
    Route::get('/reports/export-ideas', [SystemAdmin\ReportController::class, 'exportIdeas'])->name('reports.export-ideas');
    Route::get('/reports/system-health', [SystemAdmin\ReportController::class, 'systemHealth'])->name('reports.system-health');
    
    // QR Scanner
    Route::get('/qr-scanner', [SystemAdmin\QRScannerController::class, 'index'])->name('qr-scanner');
    Route::post('/qr-scanner/scan', [SystemAdmin\QRScannerController::class, 'scan'])->name('qr-scanner.scan');
});

// Hackathon Admin Routes (Current Edition Access)  
Route::middleware(['auth', 'role:hackathon_admin'])->prefix('hackathon-admin')->name('hackathon-admin.')->group(function () {
    Route::get('/dashboard', [HackathonAdmin\DashboardController::class, 'index'])->name('dashboard');
    
    // Team Management
    Route::get('/teams', [HackathonAdmin\TeamController::class, 'index'])->name('teams.index');
    Route::get('/teams/{team}', [HackathonAdmin\TeamController::class, 'show'])->name('teams.show');
    Route::post('/teams/{team}/approve', [HackathonAdmin\TeamController::class, 'approve'])->name('teams.approve');
    Route::post('/teams/{team}/reject', [HackathonAdmin\TeamController::class, 'reject'])->name('teams.reject');
    Route::get('/teams/export', [HackathonAdmin\TeamController::class, 'export'])->name('teams.export');
    
    // Idea Management
    Route::get('/ideas', [HackathonAdmin\IdeaController::class, 'index'])->name('ideas.index');
    Route::get('/ideas/{idea}', [HackathonAdmin\IdeaController::class, 'show'])->name('ideas.show');
    Route::get('/ideas/{idea}/review', [HackathonAdmin\IdeaController::class, 'review'])->name('ideas.review');
    Route::post('/ideas/{idea}/assign-supervisor', [HackathonAdmin\IdeaController::class, 'assignSupervisor'])->name('ideas.assign-supervisor');
    Route::get('/ideas/export', [HackathonAdmin\IdeaController::class, 'export'])->name('ideas.export');
    
    // Workshop Management
    Route::resource('workshops', HackathonAdmin\WorkshopController::class);
    Route::get('/workshops/{workshop}/attendance', [HackathonAdmin\WorkshopController::class, 'attendance'])->name('workshops.attendance');
    Route::get('/workshops/{workshop}/export-attendance', [HackathonAdmin\WorkshopController::class, 'exportAttendance'])->name('workshops.export-attendance');
    Route::post('/workshops/{workshop}/generate-qr', [HackathonAdmin\WorkshopController::class, 'generateQR'])->name('workshops.generate-qr');
    
    // News Management
    Route::resource('news', HackathonAdmin\NewsController::class);
    Route::post('/news/{news}/publish', [HackathonAdmin\NewsController::class, 'publish'])->name('news.publish');
    Route::post('/news/{news}/tweet', [HackathonAdmin\NewsController::class, 'tweet'])->name('news.tweet');
    Route::post('/news/{news}/schedule', [HackathonAdmin\NewsController::class, 'schedule'])->name('news.schedule');
});

// Track Supervisor Routes (Track-Specific Access)
Route::middleware(['auth', 'role:track_supervisor'])->prefix('track-supervisor')->name('track-supervisor.')->group(function () {
    Route::get('/dashboard', [TrackSupervisor\DashboardController::class, 'index'])->name('dashboard');
    
    // Ideas Review (Track-Specific)
    Route::get('/ideas', [TrackSupervisor\IdeaController::class, 'index'])->name('ideas.index');
    Route::get('/ideas/{idea}', [TrackSupervisor\IdeaController::class, 'show'])->name('ideas.show');
    Route::get('/ideas/{idea}/review', [TrackSupervisor\IdeaController::class, 'review'])->name('ideas.review');
    Route::post('/ideas/{idea}/add-comment', [TrackSupervisor\IdeaController::class, 'addComment'])->name('ideas.add-comment');
    Route::post('/ideas/{idea}/request-revision', [TrackSupervisor\IdeaController::class, 'requestRevision'])->name('ideas.request-revision');
    
    // Workshop Management (Track-Related)
    Route::get('/workshops', [TrackSupervisor\WorkshopController::class, 'index'])->name('workshops.index');
    Route::get('/workshops/{workshop}', [TrackSupervisor\WorkshopController::class, 'show'])->name('workshops.show');
    Route::post('/workshops/{workshop}/mark-attendance', [TrackSupervisor\WorkshopController::class, 'markAttendance'])->name('workshops.mark-attendance');
    
    // QR Scanner
    Route::get('/qr-scanner', [TrackSupervisor\QRScannerController::class, 'index'])->name('qr-scanner');
    Route::post('/qr-scanner/scan', [TrackSupervisor\QRScannerController::class, 'scan'])->name('qr-scanner.scan');
});

// Team Leader Routes (Own Team Access)
Route::middleware(['auth', 'role:team_leader'])->prefix('team-leader')->name('team-leader.')->group(function () {
    Route::get('/dashboard', [TeamLeader\DashboardController::class, 'index'])->name('dashboard');
    
    // Team Management (Own Team Only)
    Route::get('/team', [TeamLeader\TeamController::class, 'show'])->name('team.show');
    Route::get('/team/edit', [TeamLeader\TeamController::class, 'edit'])->name('team.edit');
    Route::put('/team', [TeamLeader\TeamController::class, 'update'])->name('team.update');
    Route::post('/team/invite-member', [TeamLeader\TeamController::class, 'inviteMember'])->name('team.invite-member');
    Route::delete('/team/members/{user}', [TeamLeader\TeamController::class, 'removeMember'])->name('team.remove-member');
    Route::delete('/team', [TeamLeader\TeamController::class, 'disbandTeam'])->name('team.disband');
    
    // Idea Management (Own Team's Idea)
    Route::get('/idea', [TeamLeader\IdeaController::class, 'show'])->name('idea.show');
    Route::get('/idea/create', [TeamLeader\IdeaController::class, 'create'])->name('idea.create');
    Route::post('/idea', [TeamLeader\IdeaController::class, 'store'])->name('idea.store');
    Route::get('/idea/edit', [TeamLeader\IdeaController::class, 'edit'])->name('idea.edit');
    Route::put('/idea', [TeamLeader\IdeaController::class, 'update'])->name('idea.update');
    Route::post('/idea/submit', [TeamLeader\IdeaController::class, 'submit'])->name('idea.submit');
    Route::post('/idea/withdraw', [TeamLeader\IdeaController::class, 'withdraw'])->name('idea.withdraw');
});

// Team Member Routes (Read-Only + Workshop Registration)
Route::middleware(['auth', 'role:team_member'])->prefix('team-member')->name('team-member.')->group(function () {
    Route::get('/dashboard', [TeamMember\DashboardController::class, 'index'])->name('dashboard');
    
    // Team View (Read-Only)
    Route::get('/team', [TeamMember\TeamController::class, 'show'])->name('team.show');
    Route::post('/team/leave', [TeamMember\TeamController::class, 'leaveTeam'])->name('team.leave');
    Route::post('/team/contact-leader', [TeamMember\TeamController::class, 'contactLeader'])->name('team.contact-leader');
    
    // Workshop Registration
    Route::get('/workshops', [TeamMember\WorkshopController::class, 'index'])->name('workshops.index');
    Route::get('/workshops/{workshop}', [TeamMember\WorkshopController::class, 'show'])->name('workshops.show');
    Route::post('/workshops/{workshop}/register', [TeamMember\WorkshopController::class, 'register'])->name('workshops.register');
    Route::delete('/workshops/{workshop}/unregister', [TeamMember\WorkshopController::class, 'unregister'])->name('workshops.unregister');
    Route::get('/workshops/{workshop}/certificate', [TeamMember\WorkshopController::class, 'downloadCertificate'])->name('workshops.certificate');
});

// Public Routes (No Authentication Required)
Route::prefix('public')->name('public.')->group(function () {
    Route::get('/workshops', [Public\WorkshopController::class, 'index'])->name('workshops.index');
    Route::get('/workshops/{workshop}', [Public\WorkshopController::class, 'show'])->name('workshops.show');
    Route::post('/workshops/{workshop}/register', [Public\WorkshopController::class, 'register'])->name('workshops.register');
    Route::get('/news', [Public\NewsController::class, 'index'])->name('news.index');
    Route::get('/news/{news}', [Public\NewsController::class, 'show'])->name('news.show');
    Route::get('/hackathon-info', [Public\HackathonController::class, 'info'])->name('hackathon.info');
});

// Role-Based Dashboard Redirects
Route::middleware(['auth'])->get('/dashboard', function () {
    $user = auth()->user();
    
    if ($user->hasRole('system_admin')) {
        return redirect()->route('system-admin.dashboard');
    }
    
    if ($user->hasRole('hackathon_admin')) {
        return redirect()->route('hackathon-admin.dashboard');
    }
    
    if ($user->hasRole('track_supervisor')) {
        return redirect()->route('track-supervisor.dashboard');
    }
    
    if ($user->hasRole('team_leader')) {
        return redirect()->route('team-leader.dashboard');
    }
    
    if ($user->hasRole('team_member')) {
        return redirect()->route('team-member.dashboard');
    }
    
    // Fallback for users without roles
    return redirect()->route('home');
})->name('dashboard');
```

#### **🛡️ Enhanced Middleware Configuration (10 minutes)**
```php
// File: app/Http/Kernel.php
// Add to $middlewareAliases array:

'role' => \Spatie\Permission\Middlewares\RoleMiddleware::class,
'permission' => \Spatie\Permission\Middlewares\PermissionMiddleware::class,
'role_or_permission' => \Spatie\Permission\Middlewares\RoleOrPermissionMiddleware::class,
'system_admin' => \App\Http\Middleware\SystemAdminMiddleware::class,
'hackathon_admin' => \App\Http\Middleware\HackathonAdminMiddleware::class,
'track_supervisor' => \App\Http\Middleware\TrackSupervisorMiddleware::class,
'team_leader' => \App\Http\Middleware\TeamLeaderMiddleware::class,
'team_member' => \App\Http\Middleware\TeamMemberMiddleware::class,

// File: app/Http/Middleware/HandleInertiaRequests.php
// Enhanced user data sharing:

'auth' => [
    'user' => $request->user() ? [
        'id' => $request->user()->id,
        'name' => $request->user()->name,
        'email' => $request->user()->email,
        'roles' => $request->user()->roles->pluck('name'),
        'permissions' => $request->user()->getPermissionsViaRoles()->pluck('name'),
        'primary_role' => $request->user()->getRoleNames()->first(),
        'can_switch_roles' => $request->user()->roles->count() > 1,
        'team_id' => $request->user()->team_id,
        'track_id' => $request->user()->track_id,
        'avatar' => $avatar->create($request->user()->name)->toBase64(),
    ] : null,
],

// Add hackathon-specific context
'hackathon' => [
    'current_edition' => HackathonEdition::current()->first(),
    'registration_open' => HackathonEdition::current()->first()?->isRegistrationOpen(),
    'idea_submission_open' => HackathonEdition::current()->first()?->isIdeaSubmissionOpen(),
    'event_dates' => HackathonEdition::current()->first()?->only(['event_start_date', 'event_end_date']),
],
```

**✅ PHASE 6 COMPLETION CRITERIA:**
- [ ] All role-based routes configured with proper middleware
- [ ] Route protection working for each role level
- [ ] Dashboard redirects functioning correctly
- [ ] Enhanced user data sharing in Inertia requests
- [ ] Middleware properly registered and functional

---

## 🎯 **TESTING & VERIFICATION (45 minutes)**

### **🔍 Role-Based Authentication Testing (15 minutes)**
```bash
# Test login flows for each role
# System Admin Login
POST /login
{
  "email": "admin@ruman.sa",
  "password": "password"
}
# Expected redirect: /system-admin/dashboard
# Expected sidebar: Full system menu (6 sections)

# Hackathon Admin Login  
POST /login
{
  "email": "hackathon@ruman.sa", 
  "password": "password"
}
# Expected redirect: /hackathon-admin/dashboard
# Expected sidebar: Edition management menu (5 sections)

# Track Supervisor Login
POST /login
{
  "email": "supervisor@ruman.sa",
  "password": "password"
}  
# Expected redirect: /track-supervisor/dashboard
# Expected sidebar: Track-specific menu (4 sections)

# Team Leader Login
POST /login
{
  "email": "leader@ruman.sa",
  "password": "password"
}
# Expected redirect: /team-leader/dashboard  
# Expected sidebar: Team management menu (4 sections)

# Team Member Login
POST /login
{
  "email": "member@ruman.sa",
  "password": "password"
}
# Expected redirect: /team-member/dashboard
# Expected sidebar: Limited access menu (3 sections)
```

### **🛡️ Permission Boundary Testing (15 minutes)**
```bash
# Test role boundaries - should return 403 Forbidden
# Team Member trying to access System Admin pages
GET /system-admin/dashboard (as team_member) → 403

# Track Supervisor trying to access other tracks
GET /track-supervisor/ideas?track_id=2 (when assigned to track_id=1) → 403

# Team Leader trying to access other teams' data  
GET /team-leader/team/show?team_id=5 (when belongs to team_id=3) → 403

# Team Member trying to edit team information
PUT /team-leader/team (as team_member) → 403

# Public user trying to access admin functions
GET /hackathon-admin/dashboard (without login) → 401 Redirect to login
```

### **🧩 Component Behavior Testing (15 minutes)**
```bash
# Test role-based component rendering
# Dashboard widgets showing correct data per role
- System Admin: Multi-edition aggregated statistics ✅
- Hackathon Admin: Current edition statistics only ✅
- Track Supervisor: Single track statistics only ✅
- Team Leader: Own team statistics only ✅
- Team Member: Read-only team information ✅

# Navigation menu showing correct items per role  
- System Admin: 6 menu sections visible ✅
- Hackathon Admin: 5 menu sections visible ✅
- Track Supervisor: 4 menu sections visible ✅
- Team Leader: 4 menu sections visible ✅
- Team Member: 3 menu sections visible ✅

# Action buttons showing/hiding correctly
- Create buttons: Only for roles with create permissions ✅
- Edit buttons: Only for roles with edit permissions ✅
- Delete buttons: Only for system/hackathon admins ✅
- Approve/Reject buttons: Only for admin roles ✅
```

---

## 📊 **SUCCESS METRICS & COMPLETION CHECKLIST**

### **✅ CORE FUNCTIONALITY CHECKLIST**
- [ ] **Authentication System**: All 5 roles login and redirect correctly
- [ ] **Role-Based Navigation**: Sidebar menus show appropriate items per role
- [ ] **Dashboard Widgets**: Each role sees appropriate data and statistics
- [ ] **Data Access Control**: Users can only access data within their scope
- [ ] **Form Validation**: All 30+ Request classes validate input correctly
- [ ] **Permission Boundaries**: Unauthorized access attempts return 403 errors
- [ ] **Component Rendering**: UI elements show/hide based on user permissions
- [ ] **API Endpoints**: All role-specific endpoints function correctly
- [ ] **File Management**: Idea file uploads work (8 files, 15MB each)
- [ ] **QR System**: QR generation and scanning operational for workshops

### **✅ ROLE-SPECIFIC FUNCTIONALITY**
- [ ] **System Admin**: Can manage editions, users, settings, and view all data
- [ ] **Hackathon Admin**: Can approve teams, review ideas, manage current edition
- [ ] **Track Supervisor**: Can review ideas in assigned track, manage workshops
- [ ] **Team Leader**: Can manage own team, submit ideas, register for workshops
- [ ] **Team Member**: Can view team info, register for workshops (read-only access)

### **✅ TECHNICAL REQUIREMENTS**  
- [ ] **Database**: All migrations run successfully with sample data
- [ ] **Seeders**: All 20+ seeders populate realistic test data
- [ ] **Routes**: All role-based routes protected with appropriate middleware
- [ ] **Services**: Business logic properly separated into service classes
- [ ] **Components**: All 43 pages and 20 components render correctly
- [ ] **Error Handling**: Proper error messages and fallbacks implemented
- [ ] **Mobile Responsive**: QR scanner and key interfaces work on mobile
- [ ] **Performance**: Page load times under 2 seconds, no N+1 queries

### **✅ INTEGRATION TESTING**
- [ ] **Workshop Registration**: Public registration generates QR codes
- [ ] **QR Attendance**: Scanning QR codes marks attendance correctly  
- [ ] **Email Notifications**: Team approval/rejection emails sent
- [ ] **File Uploads**: Idea file management working (drag-drop, validation)
- [ ] **Twitter Integration**: News auto-posting to Twitter (when configured)
- [ ] **Role Switching**: Multi-role users can switch contexts (if applicable)
- [ ] **Session Management**: Role-based session handling working
- [ ] **Cache Management**: Permission and role caches clear properly

---

## ⏰ **FINAL IMPLEMENTATION TIMELINE**

- **Phase 1**: Authentication & Role Setup (45 minutes)
- **Phase 2**: Backend Controllers (60 minutes)  
- **Phase 3**: Services & Repositories (45 minutes)
- **Phase 4**: Frontend Pages & Components (75 minutes)
- **Phase 5**: Components & Navigation (45 minutes)
- **Phase 6**: Routes & Middleware (30 minutes)
- **Testing**: Role-based functionality verification (45 minutes)
- **TOTAL**: 345 minutes (5 hours 45 minutes)

**🎯 TARGET: Complete fully-functional role-based hackathon management system in under 6 hours**

---

## 🔄 **RECOVERY COMMANDS FOR INTERRUPTIONS**

### **📍 Quick Status Check**
```bash
# Check implementation progress
git status
php artisan route:list | grep -E "system-admin|hackathon-admin|track-supervisor|team-leader|team-member"
ls -la app/Http/Controllers/SystemAdmin/
ls -la app/Http/Requests/
ls -la resources/js/Pages/SystemAdmin/
npm run build 2>&1 | head -20
```

### **🚀 Phase-Specific Recovery**
```bash
# Phase 1 Recovery - Request Classes & Middleware
ls -la app/Http/Requests/
php artisan make:request --help
php artisan middleware:list | grep role

# Phase 2 Recovery - Controllers
ls -la app/Http/Controllers/SystemAdmin/
php artisan route:list | head -10

# Phase 3 Recovery - Services  
ls -la app/Services/
composer dump-autoload

# Phase 4 Recovery - Frontend Pages
ls -la resources/js/Pages/SystemAdmin/
npm run dev

# Phase 5 Recovery - Components
ls -la resources/js/Components/Role/
npm run build

# Phase 6 Recovery - Routes & Testing
php artisan route:clear
php artisan route:cache
php artisan serve
```

---

**🚀 IMPLEMENTATION READY - COMPREHENSIVE ROLE-BASED SYSTEM WITH ZERO AMBIGUITY**

This tracker provides complete phase-by-phase implementation with exact specifications for role-based frontend behavior, authentication flows, and component rendering. Every aspect of the 5-role system is detailed for immediate implementation.