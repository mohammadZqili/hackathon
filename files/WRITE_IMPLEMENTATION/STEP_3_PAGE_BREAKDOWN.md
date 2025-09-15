# STEP 3: PAGE-BY-PAGE BREAKDOWN
## Complete Specification for Every Page

---

## 📋 INSTRUCTIONS
For EVERY page, provide complete details. Copy this template for each page.

---

## TEMPLATE FOR EACH PAGE:
```
### Page: [Page Name]
**File Path:** resources/js/Pages/[Full/Path].vue
**Route Name:** [route.name]
**URL Pattern:** /[url-pattern]
**Controller:** App\Http\Controllers\[Path]\[Controller]@[method]
**Middleware:** ['auth', 'role:xxx']

**Purpose:**
[One sentence description]

**Data Required from Backend:**
- [Table]: [fields]
- Props passed: { prop1: type, prop2: type }

**UI Components:**
- Forms: [list form elements]
- Tables: [list table columns]
- Modals: [list modals needed]
- Cards: [list card types]
- Buttons: [list actions]

**User Actions:**
1. [Action] → [What happens] → [Next step]
2. ...

**Validation Rules:**
- field1: required|type|max:n
- field2: ...

**Error Handling:**
- [Error scenario]: [How to handle]

**Success Messages:**
- On create: "[Message in Arabic] / [English]"
- On update: ...

**API Calls:**
- GET /api/[endpoint] - Load data
- POST /api/[endpoint] - Submit form
- PUT /api/[endpoint] - Update
- DELETE /api/[endpoint] - Delete
```

---

# TEAM LEADER PAGES

## 1. Team Leader Dashboard

### Page: Team Leader Dashboard
**File Path:** resources/js/Pages/TeamLeader/Dashboard.vue
**Route Name:** team-leader.dashboard
**URL Pattern:** /team-leader/dashboard
**Controller:** App\Http\Controllers\TeamLeader\DashboardController@index
**Middleware:** ['auth', 'role:team_leader']

**Purpose:**
Overview of team status, idea progress, and quick actions

**Data Required from Backend:**
- teams: { id, name, members_count, track_name }
- idea: { id, title, status, last_feedback }
- notifications: { unread_count, recent[] }
- hackathon: { name, registration_ends_at, idea_submission_ends_at }

**UI Components:**
- Cards: 
  - Team Status Card (members count, team code)
  - Idea Status Card (status badge, progress bar)
  - Deadlines Card (countdown timers)
  - Recent Notifications Card
- Buttons:
  - Create Team (if no team)
  - Manage Team (if has team)
  - Submit Idea (if team exists)
  - View Workshops

**User Actions:**
1. Click "Create Team" → Redirect to team.create → Create team
2. Click "Manage Team" → Redirect to team.show → View/edit team
3. Click "Submit Idea" → Redirect to idea.create → Create idea
4. Click notification → Mark as read → Show details

**Validation Rules:**
N/A (Display only)

**Error Handling:**
- No team: Show "Create Team" CTA
- No idea: Show "Submit Idea" CTA
- Past deadline: Show "Closed" message

**Success Messages:**
N/A (Display only)

**API Calls:**
- GET /api/team-leader/dashboard - Load all dashboard data

---

## 2. Create Team Page

### Page: Create Team
**File Path:** resources/js/Pages/TeamLeader/Team/Create.vue
**Route Name:** team-leader.team.create
**URL Pattern:** /team-leader/team/create
**Controller:** App\Http\Controllers\TeamLeader\TeamController@create
**Middleware:** ['auth', 'role:team_leader', 'no_existing_team']

**Purpose:**
Form to create a new team

**Data Required from Backend:**
- tracks: [{ id, name, description }]
- hackathon: { id, name, year }
- registration_open: boolean

**UI Components:**
- Forms:
  - Team name input (text)
  - Team description textarea
  - Track selection (dropdown)
- Buttons:
  - Submit "إنشاء الفريق / Create Team"
  - Cancel "إلغاء / Cancel"

**User Actions:**
1. Fill team name → Validate uniqueness → Show availability
2. Select track → Show track description → Confirm selection
3. Submit form → Create team → Redirect to team.show
4. Cancel → Return to dashboard

**Validation Rules:**
- name: required|string|max:100|unique:teams
- description: nullable|string|max:500
- track_id: required|exists:tracks,id

**Error Handling:**
- Duplicate name: "اسم الفريق مستخدم / Team name taken"
- Registration closed: "انتهت فترة التسجيل / Registration period ended"
- Already has team: Redirect to existing team

**Success Messages:**
- On create: "تم إنشاء الفريق بنجاح / Team created successfully"

**API Calls:**
- GET /api/team-leader/team/create - Get form data
- POST /api/team-leader/team - Submit team creation

---

## 3. Team Management Page

### Page: Team Management
**File Path:** resources/js/Pages/TeamLeader/Team/Show.vue
**Route Name:** team-leader.team.show
**URL Pattern:** /team-leader/team
**Controller:** App\Http\Controllers\TeamLeader\TeamController@show
**Middleware:** ['auth', 'role:team_leader', 'has_team']

**Purpose:**
Manage team members and view team details

**Data Required from Backend:**
- team: { id, name, code, description, track }
- members: [{ id, name, email, status, joined_at }]
- invitations: [{ id, email, status, sent_at }]
- join_requests: [{ id, user, requested_at }]

**UI Components:**
- Cards:
  - Team Info Card (name, code, track)
  - Members List Table
  - Pending Invitations Table
  - Join Requests Table
- Modals:
  - Invite Member Modal
  - Remove Member Confirmation
- Buttons:
  - Invite Member
  - Remove Member (per row)
  - Approve/Reject (per request)
  - Copy Team Code

**User Actions:**
1. Click "Invite" → Open modal → Enter email/national_id → Send invitation
2. Click "Remove" → Show confirmation → Remove member → Update list
3. Click "Approve" → Add member → Send notification → Update list
4. Click "Reject" → Reject request → Send notification → Update list
5. Copy team code → Show "Copied!" → Share with members

**Validation Rules:**
- invite_email: required_without:national_id|email
- invite_national_id: required_without:email|digits:10

**Error Handling:**
- Member not found: "المستخدم غير موجود / User not found"
- Already in team: "المستخدم في فريق آخر / User already in a team"
- Max members (5): "الحد الأقصى 5 أعضاء / Maximum 5 members"

**Success Messages:**
- Invite sent: "تم إرسال الدعوة / Invitation sent"
- Member removed: "تم حذف العضو / Member removed"
- Request approved: "تمت الموافقة / Request approved"

**API Calls:**
- GET /api/team-leader/team - Get team details
- POST /api/team-leader/team/invite - Send invitation
- DELETE /api/team-leader/team/member/{id} - Remove member
- POST /api/team-leader/team/approve/{id} - Approve request
- POST /api/team-leader/team/reject/{id} - Reject request

---

## 4. Create Idea Page

### Page: Create Idea
**File Path:** resources/js/Pages/TeamLeader/Idea/Create.vue
**Route Name:** team-leader.idea.create
**URL Pattern:** /team-leader/idea/create
**Controller:** App\Http\Controllers\TeamLeader\IdeaController@create
**Middleware:** ['auth', 'role:team_leader', 'has_team', 'no_existing_idea']

**Purpose:**
Form to submit team's idea

**Data Required from Backend:**
- team: { id, name, track }
- submission_deadline: datetime
- file_requirements: { max_files: 8, max_size_mb: 15, allowed_types: [] }

**UI Components:**
- Forms:
  - Title input (text)
  - Description textarea (rich text editor)
  - File upload area (drag & drop)
- Components:
  - File upload progress bars
  - File list with delete buttons
  - Character counter for description
- Buttons:
  - Upload Files
  - Save as Draft
  - Submit for Review

**User Actions:**
1. Enter title → Show character count → Validate length
2. Enter description → Show rich text tools → Preview formatting
3. Upload files → Show progress → Validate type/size → Add to list
4. Delete file → Confirm → Remove from list
5. Save draft → Save without submitting → Can edit later
6. Submit → Validate all → Submit for review → Lock editing

**Validation Rules:**
- title: required|string|min:10|max:200
- description: required|string|min:100|max:5000
- files: array|max:8
- files.*: file|mimes:pdf,ppt,pptx,doc,docx|max:15360

**Error Handling:**
- File too large: "الملف أكبر من 15 ميجا / File exceeds 15MB"
- Invalid type: "نوع الملف غير مسموح / File type not allowed"
- Too many files: "الحد الأقصى 8 ملفات / Maximum 8 files"

**Success Messages:**
- Draft saved: "تم حفظ المسودة / Draft saved"
- Submitted: "تم إرسال الفكرة للمراجعة / Idea submitted for review"

**API Calls:**
- GET /api/team-leader/idea/create - Get form data
- POST /api/team-leader/idea/upload - Upload file
- DELETE /api/team-leader/idea/file/{id} - Delete file
- POST /api/team-leader/idea/draft - Save as draft
- POST /api/team-leader/idea/submit - Submit for review

---

# TEAM MEMBER PAGES

## 5. Team Member Dashboard

### Page: Team Member Dashboard
**File Path:** resources/js/Pages/TeamMember/Dashboard.vue
**Route Name:** team-member.dashboard
**URL Pattern:** /team-member/dashboard
**Controller:** App\Http\Controllers\TeamMember\DashboardController@index
**Middleware:** ['auth', 'role:team_member']

**Purpose:**
Overview for team members with team status and actions

**Data Required from Backend:**
- team: { id, name, leader, members_count } (nullable)
- idea: { id, title, status, can_edit } (nullable)
- available_teams: [{ id, name, leader_name }] (if no team)

**UI Components:**
- Cards:
  - Team Status Card (or Join Team CTA)
  - Idea Status Card (if in team)
  - Available Teams List (if no team)
- Buttons:
  - Join Team / Request to Join
  - View Team Details
  - View/Edit Idea

**User Actions:**
1. No team → View available teams → Request to join → Wait for approval
2. Has team → View team details → See members and idea
3. Can edit idea → Click edit → Make changes → Save

**Validation Rules:**
N/A (Display only)

**Error Handling:**
- No team: Show join options
- Request pending: Show "Waiting for approval"
- Request rejected: Show "Try another team"

**Success Messages:**
- Request sent: "تم إرسال طلب الانضمام / Join request sent"

**API Calls:**
- GET /api/team-member/dashboard - Get dashboard data

---

# TRACK SUPERVISOR PAGES

## 6. Track Supervisor Dashboard

### Page: Track Supervisor Dashboard
**File Path:** resources/js/Pages/TrackSupervisor/Dashboard.vue
**Route Name:** track-supervisor.dashboard
**URL Pattern:** /track-supervisor/dashboard
**Controller:** App\Http\Controllers\TrackSupervisor\DashboardController@index
**Middleware:** ['auth', 'role:track_supervisor']

**Purpose:**
Overview of track ideas and review status

**Data Required from Backend:**
- track: { id, name, description }
- statistics: { 
    pending_review: number,
    under_review: number,
    approved: number,
    rejected: number,
    needs_revision: number
  }
- recent_ideas: [{ id, title, team_name, submitted_at }]

**UI Components:**
- Cards:
  - Track Info Card
  - Statistics Cards (colored by status)
  - Recent Submissions Table
- Charts:
  - Ideas by Status (pie chart)
  - Submission Timeline (line chart)
- Buttons:
  - View All Ideas
  - Start Reviewing

**User Actions:**
1. View statistics → Click status card → Filter ideas by status
2. Click recent idea → Open review page → Start review
3. View charts → Analyze trends → Export data

**Validation Rules:**
N/A (Display only)

**Error Handling:**
- No ideas: Show "No submissions yet"

**Success Messages:**
N/A (Display only)

**API Calls:**
- GET /api/track-supervisor/dashboard - Get dashboard data

---

## 7. Ideas Review Page

### Page: Ideas Review
**File Path:** resources/js/Pages/TrackSupervisor/Ideas/Review.vue
**Route Name:** track-supervisor.ideas.review
**URL Pattern:** /track-supervisor/ideas/{id}/review
**Controller:** App\Http\Controllers\TrackSupervisor\IdeaController@review
**Middleware:** ['auth', 'role:track_supervisor', 'owns_track']

**Purpose:**
Review and evaluate submitted idea

**Data Required from Backend:**
- idea: { id, title, description, files[], team, submission_date }
- review_history: [{ status, feedback, reviewer, date }]
- scoring_criteria: [{ name, max_score }]

**UI Components:**
- Sections:
  - Idea Details Section
  - Files Section (with viewers/download)
  - Team Info Section
  - Scoring Section
  - Feedback Section
  - Decision Section
- Forms:
  - Score inputs (per criteria)
  - Feedback textarea
  - Decision radio buttons
- Buttons:
  - Download Files
  - Save Progress
  - Submit Review
  - Request Revision
  - Schedule Meeting

**User Actions:**
1. Read idea → Download files → Review content
2. Score each criteria → Calculate total → Show grade
3. Write feedback → Be specific → Help team improve
4. Select decision → Approve/Reject/Revise → Confirm
5. Submit review → Send notification → Update status

**Validation Rules:**
- scores: required|array
- scores.*: required|integer|min:0|max:[max]
- feedback: required|string|min:50|max:2000
- decision: required|in:approved,rejected,needs_revision

**Error Handling:**
- Incomplete scoring: "يجب تقييم جميع المعايير / Must score all criteria"
- No feedback: "الملاحظات مطلوبة / Feedback is required"

**Success Messages:**
- Review saved: "تم حفظ المراجعة / Review saved"
- Review submitted: "تم إرسال التقييم / Review submitted"

**API Calls:**
- GET /api/track-supervisor/ideas/{id}/review - Get idea details
- POST /api/track-supervisor/ideas/{id}/save - Save progress
- POST /api/track-supervisor/ideas/{id}/submit - Submit review

---

# WORKSHOP SUPERVISOR PAGES

## 8. Workshop Check-in Page

### Page: Workshop Check-in
**File Path:** resources/js/Pages/WorkshopSupervisor/CheckIn.vue
**Route Name:** workshop-supervisor.checkin
**URL Pattern:** /workshop-supervisor/checkin
**Controller:** App\Http\Controllers\WorkshopSupervisor\CheckInController@index
**Middleware:** ['auth', 'role:workshop_supervisor']

**Purpose:**
Scan QR codes for workshop attendance

**Data Required from Backend:**
- active_workshop: { id, title, start_time, registered_count }
- checked_in_count: number

**UI Components:**
- Components:
  - QR Scanner (camera view)
  - Manual Entry Form
  - Attendance Counter
  - Recent Check-ins List
- Buttons:
  - Start Scanning
  - Manual Entry
  - Export Attendance

**User Actions:**
1. Select workshop → Start scanner → Scan QR code
2. QR scanned → Validate → Mark attendance → Show success
3. Manual entry → Enter ID → Search → Confirm → Mark attendance
4. View count → Real-time update → Track progress

**Validation Rules:**
- qr_code: required|exists:workshop_registrations,barcode
- manual_id: required|digits:10

**Error Handling:**
- Invalid QR: "رمز غير صالح / Invalid QR code"
- Already checked: "تم تسجيل الحضور مسبقاً / Already checked in"
- Not registered: "غير مسجل في الورشة / Not registered"

**Success Messages:**
- Checked in: "تم تسجيل الحضور / Attendance recorded"

**API Calls:**
- POST /api/workshop-supervisor/checkin - Process check-in
- GET /api/workshop-supervisor/attendance/{workshop} - Get attendance list

---

# HACKATHON ADMIN PAGES

## 9. Hackathon Admin Overview

### Page: Hackathon Overview
**File Path:** resources/js/Pages/HackathonAdmin/Overview.vue
**Route Name:** hackathon-admin.overview
**URL Pattern:** /hackathon-admin/overview
**Controller:** App\Http\Controllers\HackathonAdmin\OverviewController@index
**Middleware:** ['auth', 'role:hackathon_admin']

**Purpose:**
Complete hackathon statistics and management dashboard

**Data Required from Backend:**
- hackathon: { name, year, dates, status }
- statistics: {
    total_teams: number,
    total_participants: number,
    total_ideas: number,
    ideas_by_status: {},
    ideas_by_track: {},
    workshop_attendance: {},
    daily_registrations: []
  }

**UI Components:**
- Cards:
  - Key Metrics Cards (animated counters)
  - Status Overview Cards
- Charts:
  - Registration Timeline (area chart)
  - Ideas by Track (bar chart)
  - Ideas by Status (donut chart)
  - Workshop Attendance (bar chart)
- Tables:
  - Recent Activities
  - Pending Actions
- Buttons:
  - Export Reports
  - Send Announcement
  - View Details (per section)

**User Actions:**
1. View metrics → Click for details → Drill down data
2. Export data → Select format → Download file
3. Send announcement → Write message → Select recipients → Send

**Validation Rules:**
N/A (Display only)

**Error Handling:**
- No data: Show placeholder charts

**Success Messages:**
- Export complete: "تم تصدير البيانات / Data exported"

**API Calls:**
- GET /api/hackathon-admin/overview - Get all statistics

---

## 10. Create Workshop Page

### Page: Create Workshop
**File Path:** resources/js/Pages/HackathonAdmin/Workshops/Create.vue
**Route Name:** hackathon-admin.workshops.create
**URL Pattern:** /hackathon-admin/workshops/create
**Controller:** App\Http\Controllers\HackathonAdmin\WorkshopController@create
**Middleware:** ['auth', 'role:hackathon_admin']

**Purpose:**
Create new workshop with speakers and sponsors

**Data Required from Backend:**
- speakers: [{ id, name, title, photo }]
- organizations: [{ id, name, logo }]
- venues: [{ id, name, capacity }]

**UI Components:**
- Forms:
  - Workshop title (text)
  - Description (rich text)
  - Date & time (datetime picker)
  - Duration (select)
  - Venue (select)
  - Max seats (number)
  - Speakers (multi-select with search)
  - Organizations (multi-select)
  - Registration deadline (datetime)
- Components:
  - Speaker cards (preview)
  - Organization logos (preview)
- Buttons:
  - Add New Speaker
  - Add New Organization
  - Save Draft
  - Publish Workshop

**User Actions:**
1. Fill basic info → Select date/time → Check conflicts
2. Add speakers → Search/select → Show preview
3. Add sponsors → Select orgs → Show logos
4. Set capacity → Validate with venue → Confirm
5. Publish → Make visible → Open registration

**Validation Rules:**
- title: required|string|max:200
- description: required|string|max:2000
- datetime: required|date|after:now
- max_seats: required|integer|min:10|max:500
- speakers: required|array|min:1
- venue_id: required|exists:venues,id

**Error Handling:**
- Time conflict: "يوجد تعارض في الوقت / Time conflict exists"
- Past date: "التاريخ في الماضي / Date is in the past"

**Success Messages:**
- Created: "تم إنشاء الورشة / Workshop created"
- Published: "تم نشر الورشة / Workshop published"

**API Calls:**
- GET /api/hackathon-admin/workshops/create - Get form data
- POST /api/hackathon-admin/workshops - Create workshop
- POST /api/hackathon-admin/speakers - Add speaker
- POST /api/hackathon-admin/organizations - Add organization

---

# SYSTEM ADMIN PAGES

## 11. Hackathon Editions Management

### Page: Editions Management
**File Path:** resources/js/Pages/SystemAdmin/Editions/Index.vue
**Route Name:** system-admin.editions.index
**URL Pattern:** /system-admin/editions
**Controller:** App\Http\Controllers\SystemAdmin\EditionController@index
**Middleware:** ['auth', 'role:system_admin']

**Purpose:**
Manage all hackathon editions

**Data Required from Backend:**
- editions: [{
    id, name, year, status,
    registration_start, registration_end,
    idea_start, idea_end,
    teams_count, ideas_count
  }]

**UI Components:**
- Table:
  - Edition columns (year, name, dates, status, stats)
  - Action buttons per row
- Modals:
  - Create Edition Modal
  - Edit Dates Modal
  - Archive Confirmation
- Buttons:
  - Create New Edition
  - Edit (per edition)
  - Activate/Deactivate
  - Archive
  - Clone Edition

**User Actions:**
1. Create edition → Set year/dates → Create structure
2. Edit dates → Update periods → Save changes
3. Activate → Make current → Deactivate others
4. Clone → Copy structure → New year → Create
5. Archive → Confirm → Lock data → Store

**Validation Rules:**
- year: required|integer|min:2024|unique:hackathons
- name: required|string|max:200
- dates: required|date|after:each_other

**Error Handling:**
- Overlapping dates: "تداخل في التواريخ / Date overlap"
- Already active: "نسخة نشطة بالفعل / Already active edition"

**Success Messages:**
- Created: "تم إنشاء النسخة / Edition created"
- Activated: "تم تفعيل النسخة / Edition activated"

**API Calls:**
- GET /api/system-admin/editions - List editions
- POST /api/system-admin/editions - Create edition
- PUT /api/system-admin/editions/{id} - Update edition
- POST /api/system-admin/editions/{id}/activate - Activate
- POST /api/system-admin/editions/{id}/archive - Archive

---

## PAGE BREAKDOWN COMPLETE CHECKLIST
- ☐ All role dashboards defined
- ☐ All CRUD pages specified
- ☐ All forms detailed with fields
- ☐ All tables with columns listed
- ☐ All API endpoints mapped
- ☐ All validation rules specified
- ☐ All error messages defined
- ☐ All success messages defined

---

## TOTAL PAGES COUNT
- Team Leader: 9 pages
- Team Member: 5 pages  
- Track Supervisor: 7 pages
- Workshop Supervisor: 6 pages
- Hackathon Admin: 12 pages
- System Admin: 8 pages
- Visitor: 4 pages
- Auth: 3 pages
- **TOTAL: 54 pages**

---

## NOTES
[Add any page-specific notes or special requirements]
