# STEP 2: USER ROLES MAPPING
## Complete Definition of All User Roles and Permissions

---

## 📋 INSTRUCTIONS
Define EVERY role with complete details. Be specific about permissions and access.

---

## 1. VISITOR (زائر)

### Role Definition:
```
Database Value: 'visitor'
Arabic Name: زائر
English Name: Visitor
Description: Can browse and register for workshops only
```

### Entry Method:
```
☐ Self-Registration: Yes
☐ Admin Assignment: No
☐ Invitation: No
First Page After Login: /visitor/dashboard
```

### Permissions:
```
CAN DO:
- View public workshop list
- View workshop details
- Register for workshops
- View own workshop registrations
- View own QR codes
- Cancel workshop registration (before event)

CANNOT DO:
- Create/join teams
- Submit ideas
- Access team features
- Access admin features
```

### Navigation Menu Items:
```
1. Dashboard
   - Route: visitor.dashboard
   - Icon: HomeIcon
   
2. Browse Workshops
   - Route: visitor.workshops.index
   - Icon: AcademicCapIcon
   
3. My Registrations
   - Route: visitor.my-workshops
   - Icon: TicketIcon
   
4. Profile
   - Route: profile.show
   - Icon: UserIcon
```

### Required Pages:
```
1. resources/js/Pages/Visitor/Dashboard.vue
2. resources/js/Pages/Visitor/Workshops/Index.vue
3. resources/js/Pages/Visitor/Workshops/Show.vue
4. resources/js/Pages/Visitor/MyWorkshops.vue
```

---

## 2. TEAM LEADER (قائد فريق)

### Role Definition:
```
Database Value: 'team_leader'
Arabic Name: قائد فريق
English Name: Team Leader
Description: Can create one team, invite members, and submit idea
```

### Entry Method:
```
☐ Self-Registration: Yes
☐ Admin Assignment: No
☐ Invitation: No
First Page After Login: /team-leader/dashboard
```

### Permissions:
```
CAN DO:
- Create ONE team only
- Set team name and description
- Select track for team
- Invite members (max 4) via email or national ID
- Approve/reject join requests
- Remove team members
- Create/edit team idea
- Upload idea files (max 8, each max 15MB)
- Submit idea for review
- Respond to supervisor feedback
- View idea status and feedback
- Register for workshops
- View team statistics

CANNOT DO:
- Create multiple teams
- Join other teams
- Review other teams' ideas
- Access admin features
```

### Navigation Menu Items:
```
1. Dashboard
   - Route: team-leader.dashboard
   - Icon: HomeIcon
   
2. My Team
   - Route: team-leader.team.show
   - Icon: UsersIcon
   - Sub-items:
     - Team Details
     - Manage Members
     - Invitations
   
3. Our Idea
   - Route: team-leader.idea.show
   - Icon: LightBulbIcon
   - Sub-items:
     - View/Edit Idea
     - Upload Files
     - Supervisor Feedback
   
4. Workshops
   - Route: workshops.index
   - Icon: AcademicCapIcon
   
5. Notifications
   - Route: notifications.index
   - Icon: BellIcon
   - Badge: unread_count
   
6. Profile
   - Route: profile.show
   - Icon: UserIcon
```

### Required Pages:
```
1. resources/js/Pages/TeamLeader/Dashboard.vue
2. resources/js/Pages/TeamLeader/Team/Create.vue
3. resources/js/Pages/TeamLeader/Team/Show.vue
4. resources/js/Pages/TeamLeader/Team/Members.vue
5. resources/js/Pages/TeamLeader/Team/Invitations.vue
6. resources/js/Pages/TeamLeader/Idea/Create.vue
7. resources/js/Pages/TeamLeader/Idea/Show.vue
8. resources/js/Pages/TeamLeader/Idea/Edit.vue
9. resources/js/Pages/TeamLeader/Idea/Files.vue
```

---

## 3. TEAM MEMBER (عضو فريق)

### Role Definition:
```
Database Value: 'team_member'
Arabic Name: عضو فريق
English Name: Team Member
Description: Can join team and help edit idea
```

### Entry Method:
```
☐ Self-Registration: Yes
☐ Admin Assignment: No
☐ Invitation: Yes (by team leader)
First Page After Login: /team-member/dashboard
```

### Permissions:
```
CAN DO:
- Request to join teams
- Accept team invitations
- View team details
- View/edit team idea (if permitted by leader)
- Upload files to idea
- View idea status
- Register for workshops
- Leave team

CANNOT DO:
- Create teams
- Invite/remove members
- Submit idea (only leader can)
- Delete idea or files
- Access admin features
```

### Navigation Menu Items:
```
1. Dashboard
   - Route: team-member.dashboard
   - Icon: HomeIcon
   
2. My Team
   - Route: team-member.team.show
   - Icon: UsersIcon
   
3. Our Idea
   - Route: team-member.idea.show
   - Icon: LightBulbIcon
   
4. Workshops
   - Route: workshops.index
   - Icon: AcademicCapIcon
   
5. Join Team
   - Route: team-member.join
   - Icon: UserAddIcon
   - Condition: if no team
   
6. Profile
   - Route: profile.show
   - Icon: UserIcon
```

### Required Pages:
```
1. resources/js/Pages/TeamMember/Dashboard.vue
2. resources/js/Pages/TeamMember/Team/Show.vue
3. resources/js/Pages/TeamMember/Team/Join.vue
4. resources/js/Pages/TeamMember/Idea/Show.vue
5. resources/js/Pages/TeamMember/Idea/Edit.vue
```

---

## 4. TRACK SUPERVISOR (مشرف مسار)

### Role Definition:
```
Database Value: 'track_supervisor'
Arabic Name: مشرف مسار
English Name: Track Supervisor
Description: Reviews and manages ideas in assigned track
```

### Entry Method:
```
☐ Self-Registration: No
☐ Admin Assignment: Yes (by hackathon admin)
☐ Invitation: No
First Page After Login: /track-supervisor/dashboard
```

### Permissions:
```
CAN DO:
- View all ideas in assigned track
- Review ideas (approve/reject/request revision)
- Add feedback and comments
- Score ideas
- Contact team leaders
- Schedule meetings with teams
- View team details in track
- Export track reports
- Register for workshops

CANNOT DO:
- View ideas from other tracks
- Create/edit teams
- Submit ideas
- Manage workshops
- Access system settings
```

### Navigation Menu Items:
```
1. Dashboard
   - Route: track-supervisor.dashboard
   - Icon: HomeIcon
   
2. Ideas to Review
   - Route: track-supervisor.ideas.index
   - Icon: ClipboardListIcon
   - Badge: pending_count
   - Sub-items:
     - Pending Review
     - Under Review
     - Reviewed
   
3. Teams in Track
   - Route: track-supervisor.teams.index
   - Icon: UsersIcon
   
4. My Track
   - Route: track-supervisor.track.show
   - Icon: FlagIcon
   
5. Communications
   - Route: track-supervisor.communications
   - Icon: ChatIcon
   
6. Reports
   - Route: track-supervisor.reports
   - Icon: ChartBarIcon
```

### Required Pages:
```
1. resources/js/Pages/TrackSupervisor/Dashboard.vue
2. resources/js/Pages/TrackSupervisor/Ideas/Index.vue
3. resources/js/Pages/TrackSupervisor/Ideas/Review.vue
4. resources/js/Pages/TrackSupervisor/Teams/Index.vue
5. resources/js/Pages/TrackSupervisor/Track/Show.vue
6. resources/js/Pages/TrackSupervisor/Communications/Index.vue
7. resources/js/Pages/TrackSupervisor/Reports/Index.vue
```

---

## 5. WORKSHOP SUPERVISOR (مشرف ورشة)

### Role Definition:
```
Database Value: 'workshop_supervisor'
Arabic Name: مشرف ورشة
English Name: Workshop Supervisor
Description: Manages workshop attendance and check-ins
```

### Entry Method:
```
☐ Self-Registration: No
☐ Admin Assignment: Yes (by hackathon admin)
☐ Invitation: No
First Page After Login: /workshop-supervisor/dashboard
```

### Permissions:
```
CAN DO:
- View assigned workshops
- Scan QR codes for check-in
- Mark manual attendance
- View registration list
- Export attendance reports
- Send reminders to registered attendees
- View workshop statistics

CANNOT DO:
- Create/edit workshops
- Review ideas
- Manage teams
- Access system settings
```

### Navigation Menu Items:
```
1. Dashboard
   - Route: workshop-supervisor.dashboard
   - Icon: HomeIcon
   
2. My Workshops
   - Route: workshop-supervisor.workshops.index
   - Icon: AcademicCapIcon
   
3. QR Check-in
   - Route: workshop-supervisor.checkin
   - Icon: QrCodeIcon
   
4. Attendance
   - Route: workshop-supervisor.attendance
   - Icon: ClipboardCheckIcon
   
5. Reports
   - Route: workshop-supervisor.reports
   - Icon: ChartBarIcon
```

### Required Pages:
```
1. resources/js/Pages/WorkshopSupervisor/Dashboard.vue
2. resources/js/Pages/WorkshopSupervisor/Workshops/Index.vue
3. resources/js/Pages/WorkshopSupervisor/Workshops/Show.vue
4. resources/js/Pages/WorkshopSupervisor/CheckIn.vue
5. resources/js/Pages/WorkshopSupervisor/Attendance/Index.vue
6. resources/js/Pages/WorkshopSupervisor/Reports/Index.vue
```

---

## 6. HACKATHON ADMIN (مشرف عام)

### Role Definition:
```
Database Value: 'hackathon_admin'
Arabic Name: مشرف عام
English Name: Hackathon Admin
Description: Manages entire hackathon edition
```

### Entry Method:
```
☐ Self-Registration: No
☐ Admin Assignment: Yes (by system admin)
☐ Invitation: No
First Page After Login: /hackathon-admin/dashboard
```

### Permissions:
```
CAN DO:
- Set registration dates
- Create/edit tracks
- Assign track supervisors
- Create/edit workshops
- Assign workshop supervisors
- View all teams and ideas
- Override idea decisions
- Publish news
- Generate reports
- Send mass notifications
- Export all data
- Manage speakers and organizations

CANNOT DO:
- Create new hackathon editions
- Manage system settings
- Delete user accounts
- Access server configurations
```

### Navigation Menu Items:
```
1. Dashboard
   - Route: hackathon-admin.dashboard
   - Icon: HomeIcon
   
2. Overview
   - Route: hackathon-admin.overview
   - Icon: ChartPieIcon
   
3. Teams
   - Route: hackathon-admin.teams.index
   - Icon: UsersIcon
   
4. Ideas
   - Route: hackathon-admin.ideas.index
   - Icon: LightBulbIcon
   
5. Tracks
   - Route: hackathon-admin.tracks.index
   - Icon: FlagIcon
   
6. Workshops
   - Route: hackathon-admin.workshops.index
   - Icon: AcademicCapIcon
   
7. News
   - Route: hackathon-admin.news.index
   - Icon: NewspaperIcon
   
8. Reports
   - Route: hackathon-admin.reports
   - Icon: DocumentReportIcon
   
9. Settings
   - Route: hackathon-admin.settings
   - Icon: CogIcon
```

### Required Pages:
```
1. resources/js/Pages/HackathonAdmin/Dashboard.vue
2. resources/js/Pages/HackathonAdmin/Overview.vue
3. resources/js/Pages/HackathonAdmin/Teams/Index.vue
4. resources/js/Pages/HackathonAdmin/Ideas/Index.vue
5. resources/js/Pages/HackathonAdmin/Tracks/Index.vue
6. resources/js/Pages/HackathonAdmin/Tracks/Create.vue
7. resources/js/Pages/HackathonAdmin/Workshops/Index.vue
8. resources/js/Pages/HackathonAdmin/Workshops/Create.vue
9. resources/js/Pages/HackathonAdmin/News/Index.vue
10. resources/js/Pages/HackathonAdmin/News/Create.vue
11. resources/js/Pages/HackathonAdmin/Reports/Index.vue
12. resources/js/Pages/HackathonAdmin/Settings.vue
```

---

## 7. SYSTEM ADMIN (مسؤول النظام)

### Role Definition:
```
Database Value: 'system_admin'
Arabic Name: مسؤول النظام
English Name: System Admin
Description: Full system access and configuration
```

### Entry Method:
```
☐ Self-Registration: No
☐ Admin Assignment: Database seeder/manual
☐ Invitation: No
First Page After Login: /system-admin/dashboard
```

### Permissions:
```
CAN DO:
- Everything hackathon admin can do
- Create new hackathon editions
- Manage all users
- Set system configurations
- Configure email/SMS settings
- Manage API integrations
- View system logs
- Database maintenance
- Backup/restore operations

CANNOT DO:
- Nothing (has full access)
```

### Navigation Menu Items:
```
1. Dashboard
   - Route: system-admin.dashboard
   - Icon: HomeIcon
   
2. Editions
   - Route: system-admin.editions.index
   - Icon: CalendarIcon
   
3. Users
   - Route: system-admin.users.index
   - Icon: UsersIcon
   
4. System Settings
   - Route: system-admin.settings
   - Icon: CogIcon
   
5. Integrations
   - Route: system-admin.integrations
   - Icon: LinkIcon
   
6. Logs
   - Route: system-admin.logs
   - Icon: ServerIcon
   
7. Backup
   - Route: system-admin.backup
   - Icon: DatabaseIcon
```

### Required Pages:
```
1. resources/js/Pages/SystemAdmin/Dashboard.vue
2. resources/js/Pages/SystemAdmin/Editions/Index.vue
3. resources/js/Pages/SystemAdmin/Editions/Create.vue
4. resources/js/Pages/SystemAdmin/Users/Index.vue
5. resources/js/Pages/SystemAdmin/Settings/Index.vue
6. resources/js/Pages/SystemAdmin/Integrations/Index.vue
7. resources/js/Pages/SystemAdmin/Logs/Index.vue
8. resources/js/Pages/SystemAdmin/Backup/Index.vue
```

---

## ROLE MAPPING COMPLETE CHECKLIST
- ☐ All 7 roles defined
- ☐ Permissions clearly stated for each role
- ☐ Navigation menus specified
- ☐ Required pages listed
- ☐ Entry methods defined
- ☐ Database values confirmed

---

## NOTES
[Add any role-related observations or special cases]
