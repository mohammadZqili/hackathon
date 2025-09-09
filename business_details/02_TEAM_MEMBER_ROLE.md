# 👥 TEAM MEMBER ROLE - COMPLETE BUSINESS DETAILS
## Comprehensive Design Specifications and User Flows

---

## 📊 ROLE OVERVIEW

### Role Identity
- **English Name**: Team Member
- **Arabic Name**: عضو فريق
- **Database Value**: `team_member`
- **Primary Color Theme**: Mint Green (Similar to Team Leader but lighter tones)
- **Secondary Color**: Light Gray/White backgrounds
- **Icon Theme**: Outline style icons, consistent with Team Leader

### Core Responsibilities
1. Join ONE team (via invitation or request)
2. Collaborate on team idea
3. Upload supporting files (with leader permission)
4. View idea details and feedback
5. Participate in team discussions
6. Register for workshops
7. Support team leader in execution

### Key Differences from Team Leader
- **Cannot**: Create team, invite members, submit idea, remove members
- **Can**: View/edit idea (if permitted), leave team, request to join teams
- **Limited**: Administrative actions, final decisions

---

## 📄 PAGE 1: DASHBOARD

### Page Information
- **URL**: `/team-member/dashboard`
- **File References**:
  - Figma: `User_Dashboard.png`
  - Vue: `dashboard.vue`
- **Primary Purpose**: Overview of team status and available actions

### Visual Layout Structure

#### Header Section (Identical to Team Leader)
- **Left**: Hackathon logo (168x43px)
- **Center**: Search bar (312px, mint green background)
- **Right**: Dark mode, notifications, language, user profile

#### Sidebar Navigation (320px width)
**Navigation Items** (Different from Team Leader):
1. **Dashboard** (Active)
   - Icon: Home (24x24px)
   - Background: Mint green when active

2. **Our Idea**
   - Icon: Light bulb
   - Status: Disabled if no team
   
3. **My Team**
   - Icon: Users group
   - Badge: Shows member count
   
4. **Join Team** (Unique to Team Member)
   - Icon: User plus
   - Visible: Only when not in team
   
5. **Tracks**
   - Icon: Grid/Elements
   
6. **Workshops**
   - Icon: Academic cap

#### Main Content Area

##### Welcome Card
- **Content Variations**:
  - **No Team**: "Welcome! Start by joining a team"
  - **Has Team**: "Welcome back! You're part of [Team Name]"
  - **Pending Request**: "Your join request is pending"

##### Status Cards Grid (Modified for Team Member)

1. **Team Status Card**
   - **No Team State**:
     - Background: Gray gradient
     - Text: "No Team Yet"
     - CTA: "Browse Teams"
   - **Has Team State**:
     - Shows team name
     - Member count
     - Your join date

2. **Idea Status Card**
   - **View Only**: Cannot change status
   - Shows current status
   - "View Idea" link (no edit)

3. **Role Card** (Unique)
   - Shows: "Team Member"
   - Join date
   - Contribution stats

4. **Workshop Card**
   - Registered workshops count
   - Next workshop reminder

##### Quick Actions Section

**Actions for Team Member WITHOUT Team**:
1. **Browse Available Teams**
   - Background: Blue tint
   - Icon: Search
   - Button: "Find Teams"
   - Route: `/team-member/browse-teams`

2. **View Join Requests**
   - Shows pending requests
   - Status of each request
   - Cancel option

**Actions for Team Member WITH Team**:
1. **View Team Details**
   - Background: Green tint
   - Button: "View Team"
   - Route: `/team-member/team`

2. **View Idea**
   - Background: Purple tint
   - Button: "View Idea"
   - Note: "Read-only" badge if no edit permission

3. **Leave Team** (Caution)
   - Background: Red tint
   - Requires confirmation
   - Warning about losing access

##### Available Teams Section (When No Team)
- **Title**: "Teams Looking for Members"
- **Layout**: List view
- **Each Item Shows**:
  - Team name
  - Leader name
  - Track
  - Members count (X/5)
  - "Request to Join" button

---

## 📄 PAGE 2: JOIN TEAM / BROWSE TEAMS

### Page Information
- **URL**: `/team-member/join` or `/team-member/browse-teams`
- **File References**: Not directly available, inferred from Team Leader designs
- **Primary Purpose**: Find and join a team

### Visual Layout Structure

#### Page Header
- **Title**: "Find Your Team" (24px, bold)
- **Subtitle**: "Browse teams looking for members"

#### Search and Filters Bar
- **Search Box**: 
  - Width: 400px
  - Placeholder: "Search by team name or leader"
  - Real-time search

- **Filters**:
  - Track dropdown
  - Team size (has space/full)
  - Sort by: Newest/Oldest/Track

#### Teams Grid/List Toggle
- **View Options**: Grid (default) / List
- **Items per page**: 12 (paginated)

#### Team Card Structure (Grid View)
- **Size**: 350px x 200px
- **Content**:

**Header Section**:
- Team name (18px, bold)
- Track badge (colored by track)
- Members: "3/5 members"

**Body Section**:
- Leader: Name with avatar
- Created: "2 days ago"
- Description: 2 lines (truncated)

**Footer Section**:
- **Status Variations**:
  1. **Open**: "Request to Join" button (green)
  2. **Pending**: "Request Pending" (yellow, disabled)
  3. **Full**: "Team Full" (gray, disabled)
  4. **Rejected**: "Request Rejected" (red, can retry after 24h)

#### Join Request Modal
**Triggered by**: "Request to Join" button

**Modal Content**:
- **Title**: "Request to Join [Team Name]"
- **Team Summary**: Brief info
- **Message Field**: 
  - Label: "Why do you want to join?"
  - Textarea: 200 chars max
  - Optional but recommended

- **Skills Section**:
  - "Select your skills"
  - Checkbox list of relevant skills

- **Buttons**:
  - Cancel (gray)
  - Send Request (green)

#### My Requests Tab
- **Tab Location**: Top of page
- **Content**: List of sent requests
- **Each Request Shows**:
  - Team name
  - Sent date
  - Status (Pending/Accepted/Rejected)
  - Cancel button (if pending)

---

## 📄 PAGE 3: MY TEAM

### Page Information
- **URL**: `/team-member/team`
- **File References**:
  - Figma: `User_team_Team_Leader.png` (modified view)
  - Vue: `My_team.vue`
- **Primary Purpose**: View team details (read-only)

### Visual Layout Structure

#### Team Information Card (View-Only Version)
- **Similar to Team Leader** but:
  - No "Edit Team" button
  - No "Invite Member" button
  - Shows "Leave Team" option (with warning)

#### Members Table (Read-Only)
- **Columns**: Same as Team Leader
- **Actions**: 
  - Cannot remove members
  - Can view profiles only
  - Can see join dates

#### Team Activity Timeline (Unique Section)
- **Title**: "Team Activity"
- **Timeline Items**:
  - Member joined/left
  - Idea status changes
  - File uploads
  - Comments added

#### Your Contribution Section
- **Stats Card**:
  - Files uploaded: X
  - Comments made: X
  - Days in team: X
  - Last active: Date

---

## 📄 PAGE 4: OUR IDEA - OVERVIEW TAB

### Page Information
- **URL**: `/team-member/idea`
- **File References**:
  - Figma: `User_Idea.png`
  - Vue: `Our_Idea-overview_tab.vue`
- **Primary Purpose**: View idea details

### Visual Layout Structure

#### Permission-Based View
**Two Modes**:

1. **Read-Only Mode** (Default)
   - All fields disabled
   - No edit buttons
   - "View Only" badge displayed
   - Can download files
   - Cannot delete files

2. **Edit Mode** (If granted by leader)
   - Can edit description
   - Can upload files
   - Cannot change title or track
   - Cannot submit idea
   - Changes require leader approval

#### Tab Navigation
- **Available Tabs**:
  1. Overview (Always)
  2. Instructions (Always)
  3. Comments (Always)
  4. ~~Submit Idea~~ (Hidden - Leader only)

#### Overview Content
- **Idea Title**: Display only
- **Description**: 
  - Editable if permission granted
  - "Request Edit Access" button if read-only
  
- **Files Section**:
  - Download all files
  - Upload (if permitted)
  - Cannot delete others' files
  - Can delete own uploads

---

## 📄 PAGE 5: COMMENTS TAB

### Page Information
- **File References**:
  - Vue: `Our_Idea-comments_tab.vue`
- **Primary Purpose**: Team communication

### Visual Layout Structure

#### Comment Permissions
- **Can**: 
  - Read all comments
  - Add new comments
  - Edit own comments
  - Delete own comments
  
- **Cannot**:
  - Delete others' comments
  - Pin comments
  - Mark as resolved

#### Comment Input
- Same as Team Leader
- Character limit: 500
- Can attach files (if permitted)

---

## 📄 PAGE 6: INSTRUCTIONS TAB

### Page Information
- **File References**:
  - Vue: `Our_Idea-instructions_tab.vue`
- **Primary Purpose**: View submission guidelines

### Content Structure
- **Identical to Team Leader view**
- Read-only content
- Includes team member specific tips:
  - "Support your team leader"
  - "Contribute your expertise"
  - "Communicate actively"

---

## 📄 PAGE 7: TRACKS

### Page Information
- **URL**: `/team-member/tracks`
- **File References**:
  - Vue: `tracks.vue`
- **Primary Purpose**: View available tracks

### Visual Layout Structure

#### Display Differences
- **No Team**: Shows all tracks with "Join a team in this track" CTA
- **Has Team**: Highlights current team's track
- Cannot change track (leader only)
- Shows teams count per track

---

## 📄 PAGE 8: WORKSHOPS

### Page Information
- **URL**: `/team-member/workshops`
- **File References**:
  - Vue: `workshop.vue`
- **Primary Purpose**: Browse and register for workshops

### Visual Layout Structure
- **Identical to Team Leader**
- Full registration capabilities
- Personal QR codes for attendance
- Can register independently of team

---

## 📄 PAGE 9: PROFILE

### Page Information
- **URL**: `/team-member/profile`
- **File References**:
  - Vue: `profile.vue`
- **Primary Purpose**: Manage personal profile

### Visual Layout Structure

#### Additional Sections for Team Member

##### Skills & Expertise Tab
- **Purpose**: Help team leaders find members
- **Fields**:
  - Technical skills (tags)
  - Programming languages
  - Frameworks/Tools
  - Soft skills
  - Experience level

##### Availability Status
- **Options**:
  - "Open to invitations"
  - "In a team"
  - "Not available"
  
##### Team History
- **Shows**: Previous teams (if any)
- Cannot be edited
- Shows contribution level

---

## 🔄 USER JOURNEY FLOWS - TEAM MEMBER

### Flow A: Join Team via Invitation

1. **Receive Invitation**
   - Email notification
   - In-app notification
   - Dashboard alert

2. **Review Invitation**
   - Click notification
   - View team details
   - See team members
   - Check track

3. **Accept/Decline**
   - Accept: Immediately added to team
   - Decline: Invitation removed
   - Ignore: Expires after 48 hours

4. **Post-Acceptance**
   - Redirected to team page
   - Welcome message shown
   - Access to idea granted

### Flow B: Join Team via Request

1. **Browse Teams**
   - Navigate to browse page
   - Filter by track/availability
   - Find suitable teams

2. **Send Request**
   - Click "Request to Join"
   - Add introduction message
   - Select skills
   - Submit request

3. **Wait for Response**
   - See "Pending" status
   - Can cancel request
   - Notification on decision

4. **If Accepted**
   - Email notification
   - Added to team
   - Full access granted

5. **If Rejected**
   - Notification with reason (optional)
   - Can try other teams
   - 24-hour cooldown for same team

### Flow C: Contribute to Idea

1. **Check Permissions**
   - View idea page
   - See permission level
   - Request edit if needed

2. **If Edit Permission**
   - Make changes
   - Upload files
   - Add comments
   - Save changes

3. **If Read-Only**
   - View content
   - Download files
   - Add comments only
   - Request permission from leader

### Flow D: Leave Team

1. **Initiate Leave**
   - Go to team page
   - Click "Leave Team"
   - See warning message

2. **Confirmation**
   - "Are you sure?" dialog
   - Consequences listed:
     - Lose idea access
     - Remove from members
     - Cannot rejoin without invitation

3. **Post-Leave**
   - Removed from team
   - Redirected to dashboard
   - Can browse new teams

---

## 🎨 VISUAL DIFFERENCES FROM TEAM LEADER

### Color Variations
- Lighter mint green tones
- More gray/disabled states
- Less prominent CTAs

### Icon Differences
- User plus icon for joining
- View icon instead of edit
- No admin/management icons

### Layout Differences
- Fewer action buttons
- More info/view components
- Read-only indicators prominent

### Permission Indicators
- "View Only" badges
- Locked icons on disabled features
- Tooltip explanations
- Request permission buttons

---

## 🔒 PERMISSION MATRIX

### Team Member Permissions

#### ✅ CAN DO:
- View all team information
- View idea details
- Download all files
- Add comments
- Upload files (if permitted)
- Edit idea (if permitted)
- Register for workshops
- Update own profile
- Leave team
- Request to join teams

#### ❌ CANNOT DO:
- Create team
- Invite members
- Remove members
- Submit idea
- Change team details
- Delete others' files
- Assign permissions
- Delete team
- Change track
- Review other teams

---

## 📊 STATE MANAGEMENT

### Team Member States

1. **No Team State**
   - Browse teams enabled
   - Join requests enabled
   - Team features disabled
   - Idea features hidden

2. **Pending Request State**
   - Request status shown
   - Cancel option available
   - Team features disabled
   - Continue browsing enabled

3. **In Team State**
   - Full team access
   - Idea access (based on permission)
   - Leave team option
   - Cannot join other teams

4. **Post-Leave State**
   - Return to "No Team" state
   - Can immediately browse
   - Previous team in history
   - 24-hour rejoin cooldown

---

## 🔄 RESPONSIVE BEHAVIOR

### Mobile Specific (Team Member)
- Join team cards: Single column
- Request form: Full screen modal
- Team view: Simplified cards
- Comments: Chat-like interface

### Tablet Adjustments
- Browse teams: 2 column grid
- Dashboard: Stacked cards
- Sidebars: Collapsible

---

## 🌍 LOCALIZATION (ARABIC)

### Team Member Specific Terms
- "Join Team" → "انضم للفريق"
- "Request to Join" → "طلب الانضمام"
- "Leave Team" → "مغادرة الفريق"
- "Team Member" → "عضو الفريق"
- "View Only" → "عرض فقط"
- "Request Pending" → "الطلب قيد الانتظار"
- "No Team Yet" → "لا يوجد فريق بعد"

---

## 📈 INTERACTION METRICS

### Key Metrics to Track
1. **Time to join team**: Target < 5 minutes
2. **Request acceptance rate**: Target > 60%
3. **Member retention rate**: Target > 90%
4. **Contribution rate**: Files/comments per member
5. **Workshop attendance**: From team members
6. **Profile completion**: Target > 80%

---

## 🐛 EDGE CASES

### Team Member Specific
1. **Last member leaving**: Warning about team dissolution
2. **Invitation expires while viewing**: Show expired state
3. **Team fills while requesting**: Show "Team Full" message
4. **Permission revoked while editing**: Save draft, show read-only
5. **Multiple pending requests**: Limit to 5 active requests
6. **Leader leaves team**: Notification to all members

---

## ⚡ QUICK ACTIONS

### Keyboard Shortcuts (Team Member)
- **Ctrl/Cmd + J**: Join team modal
- **Ctrl/Cmd + T**: Go to team page
- **Ctrl/Cmd + I**: Go to idea page
- **Escape**: Cancel request/close modal

---

## 📝 IMPLEMENTATION PRIORITIES

### Critical Features (Must Have)
1. Join team via invitation
2. View team details
3. View idea
4. Leave team
5. Browse available teams

### Important Features (Should Have)
1. Request to join
2. Edit idea (with permission)
3. Upload files
4. Comment on idea
5. Workshop registration

### Nice to Have
1. Skill matching
2. Team recommendations
3. Contribution tracking
4. Achievement badges

---

## 🚀 DEPLOYMENT CHECKLIST

### Team Member Features
- [ ] Registration with team member role
- [ ] Browse teams functionality
- [ ] Join request system
- [ ] Invitation acceptance
- [ ] Team view (read-only)
- [ ] Idea view with permissions
- [ ] Comment system
- [ ] Leave team functionality
- [ ] Profile management
- [ ] Workshop registration
- [ ] Responsive design
- [ ] Arabic translations
- [ ] Permission enforcement
- [ ] Error handling
- [ ] Loading states

---

This completes the comprehensive business details for the Team Member role. The documentation emphasizes the collaborative nature while respecting the hierarchical permissions structure.