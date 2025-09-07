Hackathon Management System - Admin Pages Documentation
System Admin Role Pages
Based on the SRS document and Figma structure analysis, here are the detailed page descriptions for the System Admin role:
1. Admin Dashboard (Main)
   Route: /admin/dashboard
   Purpose: Overview of all hackathon activities and system health
   Layout Components:

Header:

Logo (left)
User profile dropdown (right)
Language switcher (AR/EN)
Dark mode toggle
Notifications bell


Sidebar:

Dashboard
Hackathon Management
User Management
Reports & Analytics
System Settings
Logs & Audit


Main Content:

KPI Cards (4 cards in row):

Total Active Users
Current Hackathon Registrations
Ideas Submitted
Workshop Attendance


Recent Activities Table
System Health Status
Quick Actions Panel



Colors & Styling:

Primary: #1E40AF (Blue)
Secondary: #059669 (Green)
Warning: #D97706 (Orange)
Error: #DC2626 (Red)
Background: #F8FAFC
Card Background: #FFFFFF
Text Primary: #1F2937
Text Secondary: #6B7280


2. Hackathon Management
   Route: /admin/hackathons
   Purpose: Manage hackathon editions and configurations
   Layout Components:

Page Header:

Page title: "Hackathon Management"
"Create New Edition" button (primary blue)
Search and filter controls


Content Tabs:

Active Editions
Past Editions
Draft Editions


Hackathon Cards Grid (3 columns):
Each card contains:

Hackathon title
Edition year
Status badge
Registration period
Ideas submission period
Participants count
Actions dropdown (Edit, View Details, Archive, Delete)



Create/Edit Hackathon Modal:

Basic Information

Title (AR/EN)
Description (AR/EN)
Edition Year
Start/End Dates


Registration Settings

Registration Start/End Dates
Ideas Submission Start/End Dates
Maximum Team Size


Tracks Configuration

Track Name (AR/EN)
Track Description (AR/EN)
Assign Supervisors


Prizes Configuration

Prize Name (AR/EN)
Prize Value
Track Assignment




3. User Management
   Route: /admin/users
   Purpose: Manage all system users and their roles
   Layout Components:

Filter Bar:

User Type Filters (All, Admin, Hackathon Admin, Supervisor, Team Leader, Team Member, Visitor)
Status Filter (Active, Inactive, Pending)
Date Range Picker
Search Input


Users Table:

User Avatar
Name (AR/EN)
Email
Phone
User Type Badge
Registration Date
Status Badge
Last Login
Actions (View, Edit, Suspend, Delete)


Bulk Actions:

Export to Excel
Send Bulk Email
Change Status



User Details Modal:

Personal Information Tab
Role & Permissions Tab
Activity History Tab
Login Sessions Tab


4. Tracks & Supervisors Management
   Route: /admin/tracks
   Purpose: Configure hackathon tracks and assign supervisors
   Layout Components:

Tracks Grid (2 columns):
Each track card contains:

Track Icon
Track Name (AR/EN)
Track Description
Assigned Supervisors (avatars)
Ideas Count
Edit Button


Supervisors Section:

Available Supervisors List
Add New Supervisor Form
Supervisor-Track Assignment Matrix



Track Configuration Modal:

Basic Information

Track Name (AR/EN)
Track Description (AR/EN)
Track Icon Upload
Track Color


Criteria Configuration

Evaluation Criteria List
Scoring Weights


Supervisor Assignment

Primary Supervisor
Secondary Supervisors
Permissions Configuration




5. Workshops Management
   Route: /admin/workshops
   Purpose: Manage workshops, seminars, and lectures
   Layout Components:

Workshop Calendar View:

Monthly/Weekly/Daily views
Workshop blocks with time slots
Color coding by workshop type


Workshop List View:

Workshop Title
Date & Time
Duration
Speaker(s)
Organization
Registered/Capacity
Status Badge
Actions



Workshop Creation Form:

Basic Information

Title (AR/EN)
Description (AR/EN)
Workshop Type (Workshop, Seminar, Lecture)
Date & Time
Duration
Capacity


Speaker Information

Speaker Name
Speaker Bio
Speaker Photo
Contact Information


Organization Details

Organization Name
Organization Logo
Website


Registration Settings

Public/Private
Prerequisites
Materials Required




6. Reports & Analytics
   Route: /admin/reports
   Purpose: View comprehensive system analytics and generate reports
   Layout Components:

Dashboard Charts:

Registration Trends (Line Chart)
Ideas by Track (Pie Chart)
Workshop Attendance (Bar Chart)
User Activity Heatmap


Report Generation Panel:

Report Type Selection
Date Range Configuration
Filter Options
Export Format (PDF, Excel, CSV)
Generate Report Button


Pre-built Reports Table:

Report Name
Generated Date
File Size
Download Link
Delete Option



Available Reports:

Participation Report

Total registrations by role
Team formation statistics
Ideas submission trends
Drop-out analysis


Workshop Analytics

Workshop popularity ranking
Attendance rates
Speaker effectiveness
Organization participation


Evaluation Report

Ideas evaluation statistics
Supervisor workload
Evaluation timeline analysis
Track performance comparison


System Usage Report

User activity logs
Feature usage statistics
Performance metrics
Error reports




7. News Management
   Route: /admin/news
   Purpose: Manage news articles and social media integration
   Layout Components:

News List:

Featured Image Thumbnail
Title (AR/EN)
Publication Date
Status Badge
Twitter Posted Status
Actions (Edit, Delete, Post to Twitter)


News Editor:

Title Input (AR/EN)
Rich Text Editor (AR/EN)
Featured Image Upload
Publication Date Picker
Auto-post to Twitter Toggle
SEO Settings
Tags Management



Twitter Integration:

Auto-post configuration
Tweet preview
Hashtag management
Posting schedule
Analytics tracking


8. System Settings
   Route: /admin/settings
   Purpose: Configure system-wide settings and integrations
   Settings Sections:
   General Settings

Site Name (AR/EN)
Site Description (AR/EN)
Site Logo Upload
Favicon Upload
Default Language
Timezone Configuration

Email Configuration

SMTP Settings
Email Templates Management
Email Signature Configuration
Delivery Settings

SMS Configuration

SMS Provider Settings
SMS Templates
Delivery Configuration

Social Media Integration

Twitter API Configuration
Auto-posting Settings
Hashtag Configuration

Security Settings

Password Policy Configuration
Two-Factor Authentication Settings
Session Configuration
Rate Limiting Settings
File Upload Restrictions

Localization Settings

Language Management
Translation Management
RTL/LTR Configuration
Date/Time Format Settings


9. Audit Logs
   Route: /admin/logs
   Purpose: View system activity and audit trails
   Layout Components:

Log Filters:

Date Range Picker
User Filter
Action Type Filter
Log Level Filter
Search Input


Logs Table:

Timestamp
User
Action Performed
Resource Affected
IP Address
User Agent
Status
Details Link



Log Details Modal:

Full request/response data
Stack trace (for errors)
User context information
Related logs correlation


10. System Maintenance
    Route: /admin/maintenance
    Purpose: System health monitoring and maintenance tools
    Layout Components:

System Health Dashboard:

Server Status
Database Status
Cache Status
Queue Status
Storage Usage
Memory Usage


Maintenance Tools:

Cache Management
Queue Management
Database Optimization
File Cleanup
Backup Management


Monitoring Charts:

Response Time Trends
Error Rate Tracking
Resource Usage Over Time
User Activity Patterns




Common UI Components
Navigation Structure
├── Dashboard
├── Hackathon Management
│   ├── Active Editions
│   ├── Create New Edition
│   └── Archive
├── User Management
│   ├── All Users
│   ├── Roles & Permissions
│   └── Bulk Actions
├── Content Management
│   ├── Tracks & Supervisors
│   ├── Workshops
│   └── News
├── Reports & Analytics
│   ├── Dashboard
│   ├── Generate Reports
│   └── Export Data
├── System Management
│   ├── Settings
│   ├── Audit Logs
│   └── Maintenance
└── Profile & Account
Responsive Design Breakpoints

Mobile: < 768px
Tablet: 768px - 1024px
Desktop: > 1024px

Color Palette

Primary Colors:

Primary Blue: #1E40AF
Primary Green: #059669
Primary Orange: #D97706
Primary Red: #DC2626


Neutral Colors:

White: #FFFFFF
Light Gray: #F8FAFC
Medium Gray: #6B7280
Dark Gray: #1F2937
Black: #000000


Status Colors:

Success: #10B981
Warning: #F59E0B
Error: #EF4444
Info: #3B82F6



Typography

Headings: Inter Font Family
Body Text: Inter Font Family
Monospace: JetBrains Mono (for code/logs)

Component Library Requirements

Button variants (primary, secondary, ghost, outline)
Form components (input, textarea, select, checkbox, radio)
Data display (table, cards, badges, avatars)
Navigation (sidebar, tabs, breadcrumbs)
Feedback (alerts, toasts, modals, tooltips)
Charts and visualization components
