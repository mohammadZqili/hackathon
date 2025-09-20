# Fixes Log

## Issue Types & Resolutions

### Missing View Component Files
**Issue:** Cannot read properties of undefined (reading 'default') in Inertia navigation
**Roles Affected:** TrackSupervisor, potentially others
**When:** Clicking view/edit/show buttons that navigate to missing pages
**Console Error:** `TypeError: Cannot read properties of undefined (reading 'default')`
**Fix:** Copy corresponding file from similar role directory (e.g., TrackSupervisor1) and adjust import paths

#### Specific Occurrences:
- **TrackSupervisor/Workshops/Show.vue** - Fixed by copying from TrackSupervisor1

#### Should Check in Other Roles:
- [ ] SystemAdmin - Check all Show/Edit views for Ideas, Teams, Workshops
- [ ] HackathonAdmin - Check all Show/Edit views for Teams, Workshops, Tracks
- [ ] WorkshopSupervisor - Check all Show/Edit views for Workshops, Attendees

---

### Import Path Issues
**Issue:** Incorrect relative import paths after copying files between roles
**Roles Affected:** All roles when files are copied
**When:** After copying Vue files between role directories
**Fix:** Adjust import paths based on directory depth (e.g., `../../../Layouts/Default.vue`)

---

### Database Table Name Mismatches
**Issue:** Table name inconsistencies (e.g., `editions` vs `hackathon_editions`)
**Roles Affected:** All roles with database operations
**When:** Running queries or validations
**Fix:** Always verify actual table names in migrations before use

---

### File Upload Handling
**Issue:** Temp files not properly moved to permanent storage
**Roles Affected:** All roles with file upload features
**When:** Updating records with FilePond uploads
**Fix:** Check if file starts with 'temp/' and move to permanent location on save

---

### Non-existent Route Errors
**Issue:** Ziggy error: route is not in the route list
**Roles Affected:** Visitor (workshops.show), TrackSupervisor (checkins.workshop-detail), potentially others
**When:** Clicking links that reference undefined routes
**Console Error Examples:**
- `Ziggy error: route 'visitor.workshops.show' is not in the route list`
- `Ziggy error: route 'track-supervisor.checkins.workshop-detail' is not in the route list`

#### Specific Fix for TrackSupervisor Check-ins:
**Problem:** Route `track-supervisor.checkins.workshop.detail` not registered properly
**Root Cause:** Some routes placed after certain patterns fail to register in Laravel route collection
**Symptoms:**
- Route defined in routes file but not appearing in `php artisan route:list`
- Ziggy not including route in generated JavaScript file
- `Route::getRoutes()` not showing the route

**Solutions Applied:**
1. Moved specific routes before resource routes to avoid conflicts
2. Clear route cache: `php artisan route:clear && php artisan ziggy:generate`
3. Used direct URL navigation as workaround: `router.visit('/track-supervisor/checkins/workshop/${workshopId}')`
4. Alternative: Use existing similar routes that ARE registered

**Permanent Fix:**
- Define problematic routes at the beginning of the route group
- Avoid route names with dots after 'workshop' pattern
- Use direct URL navigation when route helpers fail
**Fix:** Remove or replace links to non-existent routes with valid ones

#### Specific Occurrences:
- **visitor.workshops.show** - Route doesn't exist, removed "View Details" links
- **visitor.workshops.index** - Valid route for browsing workshops
- **visitor.workshops.my** - Valid route for user's registered workshops

#### Should Check in Other Roles:
- [ ] Check all workshop view links in TeamMember pages
- [ ] Check all workshop view links in TeamLead pages
- [ ] Verify all Inertia route() calls match actual routes

---

### Database Column Name Mismatches - Workshop Edition ID
**Issue:** Column not found: Unknown column 'edition_id' in workshops table
**Roles Affected:** SystemAdmin, HackathonAdmin (Reports feature)
**When:** Accessing reports page with edition statistics
**Console Error:** `SQLSTATE[42S22]: Column not found: 1054 Unknown column 'edition_id' in 'where clause'`
**Fix:** Use correct column name `hackathon_edition_id` instead of `edition_id` for workshops table

#### Specific Occurrences:
- **ReportRepository.php** - All workshop queries updated to use `hackathon_edition_id`
- **Workshop.php model** - Added `hackathon_edition_id` to fillable array

#### Database Structure Reference:
- Teams table: uses `edition_id`
- Ideas table: uses `edition_id`
- Workshops table: uses `hackathon_edition_id` (NOT edition_id)

---

## Quick Check Commands

```bash
# Find missing View files across roles
find resources/js/Pages -name "Index.vue" -path "*/Workshops/*" | while read f; do
  dir=$(dirname "$f")
  [ ! -f "$dir/Show.vue" ] && echo "Missing: $dir/Show.vue"
done

# Check for import path issues
grep -r "import Default from" resources/js/Pages --include="*.vue" | grep -v "@/Layouts"
```

---

### Integrated Team Creation in Idea Forms
**Issue:** Team creation logic mixed with idea submission
**Roles Affected:** TeamLead
**When:** Creating ideas without existing team
**Fix:** Separated concerns - removed team creation from idea form, require existing team

#### Changes Applied:
- Removed team_name field from idea creation form
- Made track selection a dropdown (was read-only)
- Backend requires existing team before idea submission
- Redirects to team creation page if no team exists

---

### Track ID Null Error in Idea Submission
**Issue:** SQLSTATE[23000] Integrity constraint violation - Column 'track_id' cannot be null
**Roles Affected:** TeamLead
**When:** Submitting idea without selecting track
**Console Error:** `Column 'track_id' cannot be null`
**Fix:** Add frontend validation for track selection and ensure backend requires track_id

#### Changes Applied:
- Added track_id validation in submitForm() before submission
- Made track_id required in backend validation
- Show alert if no track selected
- Ensure track_id is always sent with valid value

---

### Jetstream TeamInvitation Class Not Found
**Issue:** Class "Laravel\Jetstream\TeamInvitation" not found
**Roles Affected:** TeamLead (when creating teams with invitations)
**When:** Creating team and sending invitations
**Console Error:** `Class "Laravel\Jetstream\TeamInvitation" not found`
**Fix:** Remove Jetstream dependency, make TeamInvitation standalone Model

#### Changes Applied:
- Changed TeamInvitation to extend Model instead of JetstreamTeamInvitation
- Updated team() relationship to use Team::class
- Added team_id to fillable array
- Migration run to add token fields

---

### Team Members Relationship Error
**Issue:** BadMethodCallException - Call to undefined method App\Models\Team::users()
**Roles Affected:** All users registering via invitation
**When:** User completes registration with invitation token
**Console Error:** `Call to undefined method App\Models\Team::users()`
**Fix:** Use correct relationship method `members()` with required pivot fields

#### Changes Applied:
- Changed from `users()` to `members()` relationship
- Added pivot fields: status, role, joined_at, invited_at, invited_by
- Set member status to 'accepted' upon registration
- Properly track invitation and joining timestamps

---

### Team Members Role Enum Mismatch
**Issue:** SQL insertion error - role value 'team_member' not in enum('leader','member')
**Roles Affected:** All users registering via invitation
**When:** User completes registration with invitation token
**Console Error:** SQL insertion error with unquoted values
**Fix:** Use correct role enum value 'member' instead of 'team_member'

#### Changes Applied:
- Changed invitation role from 'team_member' to 'member'
- Added conversion logic in CreateNewUser for backward compatibility
- Database enum only accepts: 'leader' or 'member'
- Fixed in both TeamController and CreateNewUser

---

### Team Member Not Seeing Team Data
**Issue:** TeamMember Team Index page shows "No Team Assigned" even when user is in a team
**Roles Affected:** TeamMember
**When:** User logs in and navigates to Team Info page
**Console Error:** None - data loading issue
**Fix:** User was not properly added to team_members table during registration

#### Root Cause:
- Invitation acceptance process was creating the record but not syncing properly
- User needs to be in team_members table with proper role and status

#### Applied Fix:
- Manually added user to team using syncWithoutDetaching
- Verified TeamRepository->findMemberTeam() returns correct data
- Removed duplicate "No Team State" section in Index.vue
- Data now loads correctly when user is properly in team_members table

---

### Team Leader Status Pending Instead of Accepted
**Issue:** Team leader shows as 'pending' status after creating team instead of 'accepted'
**Roles Affected:** TeamLead
**When:** Team leader creates a new team
**Console Error:** None - logic issue
**Fix:** Add status='accepted' when attaching leader to team_members table

#### Root Cause:
- TeamService->createTeam() was not setting status field when adding leader
- Default status in database might be 'pending' or null
- Team leader should always be 'accepted' since they created the team

#### Applied Fix:
- Updated TeamService->createTeam() to include status='accepted' for leader
- Added all required pivot fields: status, role, joined_at, invited_at, invited_by
- Members get status='pending' until they accept invitation
- Leader gets status='accepted' automatically

---

### TeamMember Dashboard Display Issues
**Issue:** TeamMember dashboard showing wrong title and using incorrect routes/data fields
**Roles Affected:** TeamMember
**When:** Viewing dashboard as a team member
**Console Error:** Route errors or undefined data
**Fix:** Update routes and data field references to match backend service

#### Issues Found:
- Page title said "Dashboard - Team Lead" instead of "Dashboard - Team Member"
- Routes were using non-existent endpoints (team.show instead of team.index)
- Stats were looking for wrong field names (team_members instead of team_name)

#### Applied Fix:
- Changed title to "Dashboard - Team Member"
- Updated routes: team.index, idea.index, workshops.index
- Fixed stats to use: stats.team_name, team.track.name
- Dashboard now shows correct team member data

---

### Workshop Registration Route Error
**Issue:** Method App\Http\Controllers\HackathonAdmin\WorkshopController::publicRegister does not exist
**Roles Affected:** TeamLead
**When:** Clicking register button on workshop in TeamLead Workshops page
**Console Error:** Method does not exist error
**Fix:** Use correct role-specific route instead of public route

#### Root Cause:
- TeamLead Workshops/Index.vue was using 'workshops.public.register' route
- Should use 'team-lead.workshops.register' which maps to TeamLead\WorkshopController@register

#### Applied Fix:
- Changed route from 'workshops.public.register' to 'team-lead.workshops.register'
- Changed unregister route similarly
- Each role has its own WorkshopController with appropriate methods

---

### Undefined Relationship Error on Idea Model
**Issue:** Call to undefined relationship [user] on model [App\Models\Idea]
**Roles Affected:** TeamMember
**When:** Loading idea page in TeamMember role
**Console Error:** Call to undefined relationship [user] on model
**Fix:** Change relationship loading from non-existent relationships to correct ones

#### Root Cause:
- Controller was trying to load 'user' relationship which doesn't exist on Idea model
- Ideas belong to teams, not directly to users
- Also trying to load 'attachments' instead of 'files'

#### Applied Fix:
- **Backend:** Changed from `$idea->load(['track', 'user', 'attachments'])` to `$idea->load(['track', 'team', 'files'])`
- **Frontend:** Changed from `idea.attachments` to `idea.files` in TeamMember/Idea/Index.vue
- Ideas have: team(), track(), files() relationships
- No direct user() relationship exists
- Files are stored in `idea_files` table with `original_name` field

---

---

### Hackathon Editions Column Name Mismatch
**Issue:** SQLSTATE[42S22]: Column not found: 1054 Unknown column 'start_date' in 'field list'
**Roles Affected:** SystemAdmin, HackathonAdmin, TrackSupervisor
**When:** Loading Tracks Create/Edit pages that fetch editions
**Console Error:** `SQLSTATE[42S22]: Column not found: 1054 Unknown column 'start_date' in 'field list'`
**Fix:** Use correct column names from hackathon_editions table

#### Root Cause:
- TrackService->getEditionsForUser() was selecting wrong column names
- Table has `event_start_date` and `event_end_date`, not `start_date`/`end_date`
- Table uses `status` column (with values like 'active'), not boolean `is_active`

#### Applied Fix:
- Changed `start_date` → `event_start_date`
- Changed `end_date` → `event_end_date`
- Changed `is_active` checks → `status = 'active'`
- Added `status` to select list for proper data retrieval

#### Database Structure Reference:
- hackathon_editions table columns:
  - `event_start_date`, `event_end_date` (NOT start_date/end_date)
  - `status` enum ('draft', 'active', 'completed', 'archived')
  - `is_current` boolean flag

---

## Prevention Guidelines

1. **Always verify file exists** before navigation routes
2. **Check similar role directories** for reference implementations
3. **Test all CRUD operations** after adding new features
4. **Verify import paths** match directory structure
5. **Keep concerns separated** - one form for one purpose
6. **Verify model relationships exist** before trying to load them
7. **Always check actual database column names** in migrations before writing queries