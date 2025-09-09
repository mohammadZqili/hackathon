# 🎯 TEAM LEADER ROLE - COMPLETE BUSINESS DETAILS
## Comprehensive Design Specifications and User Flows

---

## 📊 ROLE OVERVIEW

### Role Identity
- **English Name**: Team Leader
- **Arabic Name**: قائد الفريق
- **Database Value**: `team_leader`
- **Primary Color Theme**: Mint Green (#4ADE80, #10B981)
- **Secondary Color**: White/Light Gray backgrounds
- **Icon Theme**: Outline style icons with consistent 24px size

### Core Responsibilities
1. Create and manage ONE team
2. Invite up to 4 team members
3. Submit and manage team idea
4. Upload supporting documents (max 8 files, 15MB each)
5. View supervisor feedback
6. Register for workshops
7. Manage team communication

---

## 📄 PAGE 1: DASHBOARD

### Page Information
- **URL**: `/team-leader/dashboard`
- **File References**: 
  - Figma: `User_Dashboard.png`
  - Vue: `dashboard.vue`
- **Primary Purpose**: Central hub showing team status, idea progress, and quick actions

### Visual Layout Structure

#### Header Section (Fixed, 64px height)
- **Left Side**:
  - Hackathon logo (168x43px)
  - Positioned: 16px from edges
  
- **Center**:
  - Search bar (312px width)
  - Mint green background (#E6FFFA)
  - Search icon (18x18px) on left
  - Placeholder text: "Search" in gray (#6B7280)
  
- **Right Side** (171px width):
  - Dark mode toggle (28x28px, mint background)
  - Notification bell (28x28px, mint background)
  - Language toggle (28x28px, mint background)
  - User profile section:
    - Avatar image (40x40px, rounded)
    - User name (bold, 16px)
    - Role text (14px, mint green color)

#### Sidebar Navigation (320px width, full height)
- **Background**: White with 8px shadow
- **Border Radius**: 16px
- **Padding**: 16px
- **Logo Section**: 
  - Centered logo (168x43px)
  - 16px padding
  - Bottom border (1px, mint green)

##### Navigation Items (Each 288px width, 40px height)
1. **Dashboard** (Active state)
   - Background: Mint green (#E6FFFA)
   - Border radius: 12px
   - Icon: Home (24x24px)
   - Text: Bold, 14px
   - Padding: 8px 12px

2. **Our Idea**
   - Background: Light mint (#F0FDF4) on hover
   - Icon: Light bulb
   - Text: Regular, 14px

3. **My Team**
   - Icon: Users group
   - Badge: Team member count (if applicable)

4. **Tracks**
   - Icon: Grid/Elements
   
5. **Workshops**
   - Icon: Academic cap

#### Main Content Area (Flex: 1)

##### Welcome Card (Full width, 120px height)
- **Background**: White with shadow
- **Border Radius**: 16px
- **Padding**: 24px
- **Content**:
  - Welcome message: "Welcome back, [User Name]!" (24px, bold)
  - Subtitle: "Here's your team progress" (16px, gray)
  - Right side: Hackathon countdown timer

##### Statistics Cards Grid (4 columns)
Each card (250px width, 140px height):
- **Border Radius**: 12px
- **Padding**: 20px
- **Shadow**: 0px 4px 6px rgba(0,0,0,0.1)

1. **Team Status Card**
   - Background: Gradient blue (#3B82F6 to #2563EB)
   - Icon: Team (32px, white, 75% opacity)
   - Number: "3/5" (24px, bold, white)
   - Label: "Team Members" (14px, white)
   - Subtext: Team name (12px, 90% opacity)

2. **Idea Status Card**
   - Background: Gradient green (#10B981 to #059669)
   - Icon: Lightbulb
   - Status badge: "Draft/Submitted/Under Review"
   - Label: "Idea Status"
   - Subtext: Idea title or "No idea yet"

3. **Track Card**
   - Background: Gradient purple (#8B5CF6 to #7C3AED)
   - Icon: Flag
   - Text: Track name
   - Status: "Active" badge

4. **Deadline Card**
   - Background: Gradient red (#EF4444 to #DC2626)
   - Icon: Clock
   - Number: Days remaining (large, bold)
   - Label: "Days Until Submission"

##### Quick Actions Section (Full width)
- **Title**: "Quick Actions" (18px, semibold)
- **Grid**: 2 columns
- **Card Height**: 80px each

Action Cards:
1. **Create/Manage Team**
   - Condition: Show "Create" if no team, "Manage" if team exists
   - Background: Blue tint (#EFF6FF)
   - Icon: Users (24px)
   - Title: "Create Your Team" (16px, semibold)
   - Description: "Start by creating your team" (14px, gray)
   - Button: "Create Team" (blue background, white text)

2. **Submit/View Idea**
   - Condition: Show only if team exists
   - Background: Purple tint (#F3E8FF)
   - Button: "Submit Idea" or "View Idea"

##### Activity Feed (Right sidebar, 350px width)
- **Title**: "Recent Activity" (16px, semibold)
- **List Items**: 
  - Green dot indicator (8px)
  - Activity text (14px)
  - Timestamp (12px, gray)
  - Maximum 5 items shown

### User Interactions & Flows

#### Flow 1: First-time User (No Team)
1. **Entry**: User logs in → Redirected to dashboard
2. **Visual State**: 
   - Team card shows "0/5 members"
   - Prominent "Create Team" CTA button
   - Other actions disabled/hidden
3. **Action**: Click "Create Team"
4. **Result**: Navigate to `/team-leader/team/create`

#### Flow 2: Team Leader with Team (No Idea)
1. **Entry**: Dashboard loads with team data
2. **Visual State**:
   - Team card shows actual member count
   - "Submit Idea" CTA prominent
   - Team management enabled
3. **Actions Available**:
   - Click "Manage Team" → `/team-leader/team`
   - Click "Submit Idea" → `/team-leader/idea/create`

#### Flow 3: Complete Team with Submitted Idea
1. **Visual State**:
   - All cards show data
   - "View Idea" replaces "Submit"
   - Activity feed shows reviews
2. **Actions**:
   - View idea details
   - Check feedback
   - Download reports

### Component Specifications

#### Buttons
- **Primary Button** (CTA):
  - Height: 40px
  - Padding: 0 16px
  - Background: #10B981 (mint green)
  - Text: White, 14px, semibold
  - Border radius: 12px
  - Hover: Darken 10%
  - Active: Darken 20%

#### Cards
- **Base Card**:
  - Background: White
  - Border: None
  - Shadow: 0px 0px 8px rgba(0,0,0,0.05)
  - Border radius: 16px
  - Padding: 16px

#### Status Badges
- **Draft**: Gray background (#F3F4F6), gray text
- **Submitted**: Blue background, white text
- **Under Review**: Yellow background, dark text
- **Approved**: Green background, white text
- **Rejected**: Red background, white text

---

## 📄 PAGE 2: CREATE TEAM

### Page Information
- **URL**: `/team-leader/team/create`
- **File References**:
  - Figma: `Create_team.png`
  - Vue: `my_team-create_team.vue`
- **Primary Purpose**: Form to create a new team

### Visual Layout Structure

#### Container
- **Max Width**: 960px
- **Centered**: Yes
- **Background**: Mint cream (#F0FDF4)
- **Padding**: 20px

#### Header
- **Title**: "Create Team" (32px, bold)
- **Position**: Left aligned
- **Margin Bottom**: 24px

#### Form Structure

##### Input Fields (Each 480px width, 56px height)

1. **Team Name Field**
   - Label: "Team Name" (inside field as placeholder)
   - Background: White with mint cream tint
   - Border: 1px solid #D1FAE5
   - Border radius: 12px
   - Padding: 15px
   - Text color: Mint green when typing
   - Required indicator: Red asterisk

2. **Hackathon Edition** (Dropdown)
   - Pre-filled with current hackathon
   - Disabled state (not editable)
   - Dropdown arrow icon (11x21px)

3. **Track Selection** (Dropdown)
   - Placeholder: "Select Track"
   - Dropdown shows available tracks
   - Each option shows track name and description

4. **Invite Members Field**
   - Placeholder: "Invite Members (Email)"
   - Add button on right side
   - Supports multiple entries

##### Invited Members List
- **Title**: "Invited Members" (18px, bold)
- **List Items** (Each 56px height):
  - Background: Mint cream
  - Member email/name (16px)
  - Remove button (X icon, 28x28px)
  - Maximum 4 members

##### Submit Button
- **Width**: 480px (full form width)
- **Height**: 40px
- **Background**: Medium sea green (#3B9B7C)
- **Text**: "Create Team" (14px, bold, white)
- **Border radius**: 12px
- **Position**: Bottom of form

### User Interactions & Flows

#### Flow 1: Successful Team Creation
1. **Entry**: User clicks "Create Team" from dashboard
2. **Form Display**: Empty form loads
3. **User Actions**:
   - Enter team name (validates uniqueness in real-time)
   - Select track from dropdown
   - Optional: Add member emails
4. **Validation**:
   - Team name: Required, 3-100 characters, unique
   - Track: Required selection
   - Members: Valid email format
5. **Submit**: Click "Create Team"
6. **Processing**: 
   - Loading state on button
   - Form disabled
7. **Success**: 
   - Success toast notification
   - Redirect to `/team-leader/team`

#### Flow 2: Validation Errors
1. **Duplicate Team Name**:
   - Red border on field
   - Error message below: "Team name already taken"
   - Field focus retained
2. **Invalid Email**:
   - Red highlight on specific email
   - Error: "Invalid email format"
3. **Maximum Members Exceeded**:
   - Disable add button after 4 members
   - Info message: "Maximum 4 members allowed"

---

## 📄 PAGE 3: TEAM MANAGEMENT

### Page Information
- **URL**: `/team-leader/team`
- **File References**:
  - Figma: `User_team_Team_Leader.png`
  - Vue: `my_team-team.vue`
- **Primary Purpose**: Manage team members and view team details

### Visual Layout Structure

#### Team Information Card (Full width, 180px height)
- **Background**: White with gradient overlay
- **Content Layout** (Grid, 3 columns):
  
  **Column 1 - Team Details**:
  - Team name (24px, bold)
  - Team code (16px, mono font): "TEAM-XXXXX"
  - Copy code button (icon)
  - Track name (14px, gray)
  
  **Column 2 - Statistics**:
  - Members count: "4/5 Members"
  - Status: "Active" badge (green)
  - Created date: "Created on DD/MM/YYYY"
  
  **Column 3 - Actions**:
  - "Invite Member" button (primary)
  - "Edit Team" button (secondary)
  - "Leave Team" (text link, red)

#### Members Table Section

##### Table Header
- **Title**: "Team Members" (18px, semibold)
- **Right Actions**: 
  - Search box (200px width)
  - Filter dropdown
  - "Invite Member" button

##### Table Structure
- **Columns**:
  1. **Member** (40% width)
     - Avatar (40px circle)
     - Name (14px, semibold)
     - Email (12px, gray)
  
  2. **Role** (15% width)
     - "Leader" badge (gold background)
     - "Member" badge (gray background)
  
  3. **Status** (15% width)
     - Active (green dot + text)
     - Invited (yellow dot + text)
     - Pending (gray dot + text)
  
  4. **Joined Date** (20% width)
     - Date format: "DD MMM YYYY"
  
  5. **Actions** (10% width)
     - Three dots menu
     - Options: Remove, Make Leader, View Profile

##### Table Rows
- **Height**: 64px per row
- **Hover**: Light mint background
- **Divider**: 1px gray line between rows

#### Pending Invitations Section (If any)
- **Title**: "Pending Invitations" (16px, semibold)
- **Card Layout**:
  - Email address
  - Sent date
  - "Resend" link
  - "Cancel" link (red)

### User Interactions & Flows

#### Flow 1: Invite New Member
1. **Trigger**: Click "Invite Member" button
2. **Modal Opens** (See Add Member Page details)
3. **Enter Email/National ID**
4. **Send Invitation**
5. **Result**: 
   - New row in pending invitations
   - Email sent to invitee
   - Success notification

#### Flow 2: Remove Team Member
1. **Trigger**: Click three dots → "Remove"
2. **Confirmation Dialog**:
   - Title: "Remove Team Member?"
   - Message: "Are you sure you want to remove [Name]?"
   - Buttons: "Cancel" (gray), "Remove" (red)
3. **Confirm**: Member removed
4. **Update**: Table refreshes, count updates

---

## 📄 PAGE 4: ADD TEAM MEMBER (MODAL)

### Page Information
- **File References**:
  - Figma: `User_AddMember_Team_Leader.png`
  - Vue: `my_team-Add_team_Member.vue`
- **Type**: Modal overlay
- **Trigger**: "Invite Member" button

### Modal Structure

#### Modal Container
- **Width**: 480px
- **Background**: White
- **Border Radius**: 16px
- **Shadow**: Large (0px 20px 25px rgba(0,0,0,0.2))
- **Overlay**: Black 50% opacity

#### Modal Header
- **Title**: "Invite Team Member" (18px, bold)
- **Close Button**: X icon (24px, top-right)
- **Divider**: 1px line below header

#### Modal Body (Padding: 24px)

##### Input Method Toggle
- **Type**: Tab buttons
- **Options**:
  1. "By Email" (default active)
  2. "By National ID"
- **Active State**: Mint green background
- **Inactive**: Gray background

##### Email Input Section (When "By Email" selected)
- **Field Label**: "Email Address"
- **Input Type**: Email
- **Placeholder**: "member@example.com"
- **Validation**: Real-time email format check
- **Helper Text**: "Member will receive an invitation email"

##### National ID Section (When "By National ID" selected)
- **Field Label**: "National ID"
- **Input Type**: Text
- **Placeholder**: "1234567890"
- **Max Length**: 10 digits
- **Helper Text**: "Member must be registered with this ID"

##### Optional Message Field
- **Label**: "Add a message (optional)"
- **Type**: Textarea
- **Height**: 80px
- **Placeholder**: "Join our amazing team!"
- **Character Limit**: 200

#### Modal Footer
- **Layout**: Right aligned
- **Buttons**:
  1. **Cancel** (Secondary)
     - Width: 100px
     - Background: Gray
  2. **Send Invitation** (Primary)
     - Width: 150px
     - Background: Mint green
     - Loading state when processing

### Validation & States

#### Success State
- Green checkmark appears in input
- Success message: "Invitation sent successfully"
- Modal closes after 2 seconds

#### Error States
1. **User Already in Team**:
   - Error: "This user is already in a team"
2. **User Not Found**:
   - Error: "No user found with this email/ID"
3. **Team Full**:
   - Error: "Team has reached maximum members (5)"

---

## 📄 PAGE 5: OUR IDEA - OVERVIEW TAB

### Page Information
- **URL**: `/team-leader/idea`
- **File References**:
  - Figma: `User_Idea.png`
  - Vue: `our_idea-overview_tab.vue`
- **Primary Purpose**: View and manage team's idea

### Visual Layout Structure

#### Page Header
- **Title**: "Our Idea" (24px, bold)
- **Status Badge**: Right side (Draft/Submitted/Under Review)
- **Last Updated**: "Last updated: DD/MM/YYYY HH:MM"

#### Tab Navigation
- **Tabs** (Horizontal, below header):
  1. **Overview** (Active)
  2. **Submit Idea**
  3. **Instructions**
  4. **Comments**
- **Active Tab**: Mint green underline (3px)
- **Inactive**: Gray text
- **Tab Height**: 48px

#### Overview Tab Content

##### Idea Summary Card
- **Layout**: Full width, auto height
- **Sections**:

**1. Idea Title Section**
- Title field (24px, bold)
- Edit icon (if draft status)
- Character count: "XX/200"

**2. Description Section**
- Label: "Description" (16px, semibold)
- Content: Multi-line text (14px)
- Edit button (if allowed)
- Character count: "XXX/5000"

**3. Track Information**
- Label: "Track" (16px, semibold)
- Track name with icon
- Track description (italic, gray)

**4. Files Section**
- Title: "Uploaded Files" (16px, semibold)
- File grid (2 columns):
  - File icon based on type
  - File name (truncated)
  - File size
  - Upload date
  - Download button
  - Delete button (if draft)

##### Scoring Section (If reviewed)
- **Title**: "Evaluation Scores"
- **Score Cards** (Grid):
  - Criteria name
  - Score: X/10
  - Progress bar
  - Total score card (highlighted)

##### Feedback Section (If reviewed)
- **Title**: "Supervisor Feedback"
- **Card Content**:
  - Supervisor name and avatar
  - Feedback date
  - Feedback text (can be long)
  - Status: Approved/Needs Revision/Rejected

---

## 📄 PAGE 6: SUBMIT IDEA TAB

### Page Information
- **File References**:
  - Figma: `Submit_idea.png`
  - Vue: `our_idea-Submit_idea_tab.vue`
- **Primary Purpose**: Form to submit/edit idea

### Visual Layout Structure

#### Form Container
- **Max Width**: 800px
- **Background**: White
- **Padding**: 24px

#### Form Fields

##### 1. Idea Title
- **Label**: "Idea Title *" (14px, semibold)
- **Input Height**: 48px
- **Max Length**: 200 characters
- **Counter**: Shows "XXX/200"
- **Placeholder**: "Enter your innovative idea title"

##### 2. Problem Statement
- **Label**: "Problem Statement *"
- **Type**: Textarea
- **Height**: 120px
- **Max Length**: 1000 characters
- **Placeholder**: "Describe the problem you're solving"

##### 3. Proposed Solution
- **Label**: "Proposed Solution *"
- **Type**: Rich text editor
- **Height**: 200px
- **Toolbar**: Bold, Italic, Bullet list, Number list
- **Max Length**: 5000 characters

##### 4. Target Audience
- **Label**: "Target Audience *"
- **Type**: Textarea
- **Height**: 100px
- **Placeholder**: "Who will benefit from this solution?"

##### 5. File Upload Section
- **Title**: "Supporting Documents"
- **Subtitle**: "Upload up to 8 files (Max 15MB each)"
- **Accepted Types**: PDF, DOC, DOCX, PPT, PPTX, ZIP

**Upload Area**:
- **Type**: Drag and drop zone
- **Height**: 150px
- **Border**: 2px dashed gray
- **Icon**: Cloud upload (48px)
- **Text**: "Drag files here or click to browse"

**Uploaded Files List**:
- File icon
- File name
- Size
- Upload progress bar (during upload)
- Remove button (X)

#### Action Buttons
- **Layout**: Bottom right
- **Buttons**:
  1. **Save as Draft** (Secondary)
     - Saves without submitting
  2. **Submit for Review** (Primary)
     - Final submission
     - Shows confirmation dialog

### Validation Rules

#### Required Fields
- Title: Required, 10-200 characters
- Problem: Required, 50-1000 characters
- Solution: Required, 100-5000 characters
- Target: Required, 20-500 characters

#### File Validation
- Max files: 8
- Max size per file: 15MB
- Total size: 100MB
- Types: PDF, DOC, DOCX, PPT, PPTX, ZIP

---

## 📄 PAGE 7: INSTRUCTIONS TAB

### Page Information
- **File References**:
  - Vue: `our_idea-instructions_tab.vue`
- **Primary Purpose**: Guidelines for idea submission

### Content Structure

#### Instructions Card
- **Background**: Light mint
- **Border**: 1px solid mint green
- **Icon**: Info circle (24px)
- **Content Sections**:

##### 1. Submission Guidelines
- Title: "How to Submit Your Idea"
- Numbered list:
  1. Complete all required fields
  2. Be clear and concise
  3. Upload supporting documents
  4. Review before submitting
  5. Submit before deadline

##### 2. Evaluation Criteria
- Title: "How Your Idea Will Be Evaluated"
- Criteria list with weights:
  - Innovation (25%)
  - Feasibility (25%)
  - Impact (20%)
  - Presentation (15%)
  - Team Collaboration (15%)

##### 3. Important Dates
- Submission deadline
- Review period
- Results announcement
- Presentation date

##### 4. Tips for Success
- Bullet list of helpful tips
- Do's and Don'ts section

---

## 📄 PAGE 8: COMMENTS TAB

### Page Information
- **File References**:
  - Vue: `our_idea-comments_tab.vue`
- **Primary Purpose**: Communication with supervisors

### Visual Layout Structure

#### Comments Section
- **Type**: Chat-like interface
- **Layout**: Vertical timeline

#### Comment Structure
Each comment contains:
- **Avatar**: 40px circle
- **Author Name**: 14px, semibold
- **Role Badge**: (Supervisor/Team Leader)
- **Timestamp**: 12px, gray
- **Message**: 14px, regular
- **Attachments**: If any

#### Comment Input
- **Position**: Bottom fixed
- **Components**:
  - Textarea (auto-expand)
  - Attachment button
  - Send button
  - Character limit: 500

#### Comment Types

##### 1. Supervisor Feedback
- **Background**: Light blue
- **Icon**: Official badge
- **Special**: Cannot be deleted

##### 2. Team Responses
- **Background**: White
- **Can**: Edit/Delete own comments

##### 3. System Messages
- **Background**: Light gray
- **Examples**: "Idea submitted", "Status changed"

---

## 📄 PAGE 9: TRACKS

### Page Information
- **URL**: `/team-leader/tracks`
- **File References**:
  - Vue: `tracks.vue`
- **Primary Purpose**: Browse available tracks

### Visual Layout Structure

#### Page Header
- **Title**: "Competition Tracks" (24px, bold)
- **Subtitle**: "Choose the track that fits your idea"

#### Tracks Grid
- **Layout**: 2 columns on desktop, 1 on mobile
- **Gap**: 24px

#### Track Card Structure
- **Height**: 200px
- **Background**: White
- **Border**: 1px solid gray
- **Hover**: Mint green border

**Card Content**:
1. **Track Icon**: 48px (top-left)
2. **Track Name**: 18px, bold
3. **Teams Count**: "X/20 teams"
4. **Description**: 14px, gray (3 lines max)
5. **Supervisor**: Name and avatar
6. **Action Button**: 
   - "Select Track" (if no team)
   - "View Details" (if has team)

#### Track Details Modal
Opens when clicking "View Details":
- Full track description
- Requirements
- Evaluation criteria
- Important dates
- Registered teams list

---

## 📄 PAGE 10: WORKSHOPS

### Page Information
- **URL**: `/team-leader/workshops`
- **File References**:
  - Vue: `workshops.vue`
- **Primary Purpose**: Browse and register for workshops

### Visual Layout Structure

#### Page Header
- **Title**: "Available Workshops" (24px, bold)
- **Filters**: 
  - Date range picker
  - Track filter
  - Status filter (Upcoming/Past)

#### Workshops List
- **View Toggle**: Grid/List view
- **Default**: Grid view (3 columns)

#### Workshop Card
- **Height**: 280px
- **Sections**:

**1. Header Image** (120px height)
- Workshop banner or default image
- Date badge overlay (top-right)

**2. Content** (Padding: 16px)
- **Title**: 16px, bold (2 lines max)
- **Speaker**: Name with avatar (14px)
- **Details**:
  - Date & Time with icon
  - Duration with icon
  - Venue with icon
  - Available seats: "X/50 seats left"

**3. Footer**
- **Register Button**: 
  - "Register" (if available)
  - "Full" (if no seats)
  - "Registered" (if already registered)
- **QR Code Icon**: If registered

#### Registration Modal
- **Title**: "Register for Workshop"
- **Workshop Details**: Summary
- **Confirmation Message**
- **Terms Checkbox**: "I understand I must attend"
- **Buttons**: Cancel, Confirm Registration

#### My Registrations Section
- **Toggle**: "Show My Registrations"
- **List**: Registered workshops with QR codes
- **Actions**: Cancel registration (if allowed)

---

## 📄 PAGE 11: PROFILE

### Page Information
- **URL**: `/team-leader/profile`
- **File References**:
  - Vue: `profile.vue`
- **Primary Purpose**: User profile management

### Visual Layout Structure

#### Profile Header
- **Background**: Gradient mint green
- **Height**: 200px
- **Content**:
  - Avatar (100px, centered)
  - Change photo button
  - User name (24px, white)
  - Role badge
  - Email

#### Profile Tabs
1. **Personal Information**
2. **Security**
3. **Notifications**
4. **Activity Log**

#### Personal Information Tab

##### Form Fields
1. **Full Name**: Text input
2. **Email**: Disabled (cannot change)
3. **Phone**: With country code
4. **National ID**: Disabled
5. **Bio**: Textarea (optional)
6. **Skills**: Tag input
7. **LinkedIn**: URL input
8. **GitHub**: URL input

##### Save Button
- Position: Bottom right
- Only enabled when changes made

#### Security Tab
- Change password section
- Two-factor authentication toggle
- Login sessions list
- "Logout from all devices" button

#### Notifications Tab
- Email notifications toggles:
  - Team invitations
  - Idea status updates
  - Workshop reminders
  - Announcements
- In-app notifications settings

#### Activity Log Tab
- Timeline of user actions
- Filterable by type
- Pagination

---

## 🔄 COMMON COMPONENTS ACROSS PAGES

### 1. Navigation Sidebar (Persistent)
- **Width**: 320px
- **Items**: 5 main navigation items
- **Active State**: Mint green background
- **Icons**: 24x24px, consistent style
- **Responsive**: Collapses to icons only on mobile

### 2. Header Bar (Persistent)
- **Height**: 64px
- **Components**: Logo, Search, User menu
- **Sticky**: Yes
- **Shadow**: Subtle bottom shadow

### 3. Notification System
- **Types**: Success, Error, Warning, Info
- **Position**: Top-right
- **Duration**: 5 seconds (auto-dismiss)
- **Actions**: Close button, action link

### 4. Modal System
- **Overlay**: Black 50% opacity
- **Animation**: Fade in/out
- **Close Methods**: X button, Escape key, Click outside
- **Sizes**: Small (400px), Medium (600px), Large (800px)

### 5. Loading States
- **Page Load**: Full-screen spinner
- **Component Load**: Inline spinner
- **Button Load**: Spinner replaces text
- **Skeleton**: For content placeholders

### 6. Empty States
- **Icon**: 64px gray icon
- **Message**: Descriptive text
- **Action**: CTA button when applicable

### 7. Error States
- **Field Errors**: Red border, error text below
- **Page Errors**: Error card with retry button
- **Network Errors**: Toast notification

---

## 🎨 DESIGN SYSTEM SPECIFICATIONS

### Color Palette
```css
/* Primary Colors */
--mint-green: #10B981;
--mint-green-light: #E6FFFA;
--mint-cream: #F0FDF4;
--sea-green: #3B9B7C;

/* Status Colors */
--success: #10B981;
--error: #EF4444;
--warning: #F59E0B;
--info: #3B82F6;

/* Neutral Colors */
--gray-900: #111827;
--gray-700: #374151;
--gray-500: #6B7280;
--gray-300: #D1D5DB;
--gray-100: #F3F4F6;
--white: #FFFFFF;
```

### Typography
```css
/* Font Family */
font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;

/* Font Sizes */
--text-xs: 12px;
--text-sm: 14px;
--text-base: 16px;
--text-lg: 18px;
--text-xl: 20px;
--text-2xl: 24px;
--text-3xl: 32px;

/* Font Weights */
--font-regular: 400;
--font-medium: 500;
--font-semibold: 600;
--font-bold: 700;
```

### Spacing System
```css
/* Spacing Scale */
--space-1: 4px;
--space-2: 8px;
--space-3: 12px;
--space-4: 16px;
--space-5: 20px;
--space-6: 24px;
--space-8: 32px;
--space-10: 40px;
--space-12: 48px;
--space-16: 64px;
```

### Border Radius
```css
--radius-sm: 4px;
--radius-md: 8px;
--radius-lg: 12px;
--radius-xl: 16px;
--radius-full: 9999px;
```

### Shadows
```css
--shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
--shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
--shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
--shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
```

---

## 🔀 COMPLETE USER JOURNEY FLOWS

### Flow A: Complete Team Leader Journey (First Time)

1. **Registration**
   - Select "Team Leader" role
   - Complete profile
   - Email verification

2. **First Login**
   - Redirected to dashboard
   - Welcome modal shown
   - Guided tour offered

3. **Team Creation**
   - Click "Create Team" CTA
   - Fill team details
   - Select track
   - Team created successfully

4. **Member Invitation**
   - Navigate to team page
   - Click "Invite Member"
   - Send up to 4 invitations
   - Track invitation status

5. **Idea Development**
   - Navigate to "Our Idea"
   - Read instructions
   - Fill idea form
   - Upload documents
   - Save as draft

6. **Idea Submission**
   - Review idea details
   - Confirm submission
   - Receive confirmation
   - Wait for review

7. **Review Process**
   - Check comments tab
   - Respond to feedback
   - Make revisions if needed
   - Track status changes

8. **Workshop Participation**
   - Browse workshops
   - Register for relevant ones
   - Receive QR codes
   - Attend workshops

### Flow B: Quick Actions Flow

1. **Quick Team Setup** (5 minutes)
   - Dashboard → Create Team → Basic info only → Save

2. **Quick Idea Submit** (10 minutes)
   - Dashboard → Submit Idea → Fill required fields → Submit

3. **Quick Member Add** (2 minutes)
   - Team page → Invite → Enter email → Send

### Flow C: Error Recovery Flows

1. **Failed Team Creation**
   - Show specific error
   - Retain form data
   - Allow correction
   - Retry submission

2. **Failed File Upload**
   - Show file-specific error
   - Allow retry for failed files
   - Keep successful uploads

3. **Session Timeout**
   - Save draft automatically
   - Show session expired modal
   - Redirect to login
   - Restore previous state after login

---

## 📱 RESPONSIVE BEHAVIOR

### Mobile (< 768px)
- Sidebar: Hamburger menu, slide-in drawer
- Cards: Single column, full width
- Tables: Horizontal scroll
- Modals: Full screen
- Navigation: Bottom tab bar for main items

### Tablet (768px - 1024px)
- Sidebar: Collapsible, icons only by default
- Cards: 2 columns
- Tables: Responsive with priority columns
- Modals: 90% width

### Desktop (> 1024px)
- Full layout as designed
- All features visible
- Hover states enabled
- Keyboard shortcuts active

---

## ⌨️ KEYBOARD SHORTCUTS

- **Ctrl/Cmd + S**: Save draft
- **Ctrl/Cmd + Enter**: Submit form
- **Escape**: Close modal/dropdown
- **Tab**: Navigate form fields
- **Arrow keys**: Navigate dropdown options
- **Space**: Toggle checkbox/radio
- **Enter**: Activate button/link

---

## 🌍 LOCALIZATION NOTES

### Arabic Support Required
- All text must have Arabic translations
- RTL layout support for Arabic
- Flip icons where directional
- Mirror layout for RTL
- Number formats: Eastern Arabic numerals option
- Date formats: Hijri calendar option

### Text Direction Changes
- Sidebar: Moves to right in RTL
- Icons: Flip horizontal where applicable
- Progress bars: Right to left in RTL
- Form labels: Right aligned in RTL

---

## 🔒 PERMISSION BOUNDARIES

### Team Leader CAN:
- Create ONE team only
- Invite up to 4 members
- Remove members (except self)
- Edit team details
- Submit ONE idea per team
- Edit idea while in draft
- View idea feedback
- Respond to comments
- Register for workshops
- Update own profile

### Team Leader CANNOT:
- Create multiple teams
- Join other teams
- Delete submitted idea
- Edit idea after submission (without supervisor request)
- Review other teams' ideas
- Access admin areas
- Modify other users' profiles
- Delete team (only leave)

---

## 📊 DATA VALIDATION RULES

### Team Creation
- **Team Name**: 
  - Required
  - 3-100 characters
  - Alphanumeric + spaces
  - Unique per hackathon
  
- **Description**: 
  - Optional
  - Max 500 characters
  
- **Track**: 
  - Required
  - Must be active track

### Idea Submission
- **Title**: 
  - Required
  - 10-200 characters
  - No special characters
  
- **Description**: 
  - Required
  - 100-5000 characters
  - Rich text allowed
  
- **Files**: 
  - Max 8 files
  - Max 15MB per file
  - Allowed: PDF, DOC, DOCX, PPT, PPTX, ZIP

### Member Invitation
- **Email**: 
  - Valid email format
  - Must be registered user
  - Not already in a team


---

## 🎯 CRITICAL USER PATHS

These paths MUST work flawlessly:

1. **Registration → Dashboard** (First impression)
2. **Dashboard → Create Team** (Core action)
3. **Team → Invite Members** (Collaboration)
4. **Idea → Submit** (Main goal)
5. **Feedback → Revise** (Iteration)
6. **Workshop → Register** (Engagement)

---

## 📈 SUCCESS METRICS

Track these to ensure good UX:

1. **Time to create team**: Target < 2 minutes
2. **Time to submit idea**: Target < 10 minutes
3. **Invitation acceptance rate**: Target > 70%
4. **Draft to submission rate**: Target > 80%
5. **Page load time**: Target < 2 seconds
6. **Error rate**: Target < 1%
7. **Mobile usage**: Monitor percentage

---

## 🐛 EDGE CASES TO HANDLE

1. **Last member leaving team**: Prompt for team deletion
2. **Leader leaving team**: Assign new leader first
3. **Deadline passed during submission**: Show grace period message
4. **Multiple simultaneous edits**: Show conflict resolution
5. **Network failure during upload**: Resume capability
6. **Browser back button**: Preserve form state
7. **Duplicate tab sessions**: Sync or warn user

---

## 📝 IMPLEMENTATION NOTES FOR DEVELOPERS

### Component Reusability
- Create base components for cards, buttons, inputs
- Use composition for complex components
- Implement proper prop validation
- Use slots for flexibility

### State Management
- Use Pinia/Vuex for global state
- Local state for component-specific data
- Persist critical data in localStorage
- Implement optimistic updates

### Performance Optimization
- Lazy load heavy components
- Implement virtual scrolling for long lists
- Compress images, use WebP format
- Cache API responses appropriately
- Debounce search inputs

### Accessibility
- ARIA labels on all interactive elements
- Keyboard navigation support
- Screen reader friendly
- Sufficient color contrast
- Focus indicators visible

### Testing Priorities
1. Test all form submissions
2. Test validation rules
3. Test error states
4. Test mobile responsive
5. Test Arabic RTL layout
6. Test keyboard navigation
7. Test session handling

---

## 🚀 DEPLOYMENT CHECKLIST

Before deploying Team Leader features:

- [ ] All pages implemented per specifications
- [ ] Navigation works correctly
- [ ] Forms validate properly
- [ ] File uploads working
- [ ] Email invitations sending
- [ ] Arabic translations complete
- [ ] Mobile responsive tested
- [ ] Error handling in place
- [ ] Loading states implemented
- [ ] Success messages shown
- [ ] Permissions enforced
- [ ] Data persistence working
- [ ] Session management tested
- [ ] Performance acceptable
- [ ] Accessibility verified

---

This completes the comprehensive business details for the Team Leader role. Every page, component, flow, and interaction has been documented in detail to enable exact implementation of the design.
