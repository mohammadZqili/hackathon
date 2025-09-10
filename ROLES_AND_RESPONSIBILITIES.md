# 📚 HACKATHON SYSTEM - ROLES & RESPONSIBILITIES DOCUMENTATION
## Complete Role Definitions, Permissions, and Registration Methods

---

## 📊 SYSTEM OVERVIEW

The Hackathon Management System has **7 distinct user roles**, each with specific responsibilities and access levels. The system uses **role-based access control (RBAC)** to ensure data security and proper authorization.

### Role Categories:
1. **Administrative Roles** (Predefined) - Created by administrators
2. **Participant Roles** (Self-Registration) - Public registration available
3. **Visitor Role** (Self-Registration) - Limited access for workshop attendance

---

## 🔑 ROLE REGISTRATION METHODS

### **Predefined Roles** (Admin-Created)
| Role | Database Value | Created By | Registration Method |
|------|---------------|------------|-------------------|
| System Admin | `system_admin` | System Setup | Database seeding or initial configuration |
| Hackathon Admin | `hackathon_admin` | System Admin | Admin panel user creation |
| Track Supervisor | `track_supervisor` | Hackathon Admin | Assignment through track management |
| Workshop Supervisor | `workshop_supervisor` | Hackathon Admin | Assignment through workshop management |

### **Self-Registration Roles** (Public)
| Role | Database Value | Registration | Approval Required |
|------|---------------|--------------|------------------|
| Team Leader | `team_leader` | Public registration page | No (instant access) |
| Team Member | `team_member` | Public registration page | No (instant access) |
| Visitor | `visitor` | Public registration page | No (instant access) |

---

## 👤 ROLE DEFINITIONS & RESPONSIBILITIES

### 1️⃣ **SYSTEM ADMIN** (مسؤول النظام العام)

#### Overview
- **Purpose**: Overall system management and technical administration
- **Scope**: Entire system across all editions
- **Database Value**: `system_admin`
- **Registration**: Predefined (cannot self-register)

#### Core Responsibilities

##### Edition Management
- Create new hackathon editions (yearly events)
- Set global edition parameters
- Activate/deactivate editions
- Archive completed editions
- Manage edition transitions

##### User & Role Management
- Create Hackathon Admin accounts
- Assign administrators to editions
- Reset any user password
- Deactivate/reactivate user accounts
- View all user activities
- Manage role permissions

##### System Configuration
- Configure email settings (SMTP)
- Set up SMS gateway integration
- Manage system branding (logo, colors, themes)
- Configure third-party integrations (Twitter API, payment gateways)
- Set system-wide parameters and limits

##### Global Oversight
- Access ALL data across ALL editions
- Monitor system health and performance
- Review comprehensive audit logs
- Generate system-wide reports
- Handle critical escalations
- Backup and restore operations

#### Permissions
- ✅ Full CRUD on all resources
- ✅ Access to all editions
- ✅ System settings modification
- ✅ User role assignment
- ✅ Database management

#### Restrictions
- ❌ Cannot participate as team member
- ❌ Cannot submit ideas
- ❌ Cannot review ideas (not their role)

---

### 2️⃣ **HACKATHON ADMIN** (المشرف العام)

#### Overview
- **Purpose**: Manage a specific hackathon edition
- **Scope**: Single edition (assigned by System Admin)
- **Database Value**: `hackathon_admin`
- **Registration**: Created by System Admin

#### Core Responsibilities

##### Edition Configuration
- Set registration opening/closing dates
- Define idea submission deadlines
- Configure edition-specific rules
- Set team size limits
- Define evaluation criteria

##### Track Management
- Create competition tracks/paths (مسارات)
- Define track descriptions and requirements
- Set track-specific evaluation criteria
- Assign Track Supervisors to tracks
- Monitor track progress
- Manage track deadlines

##### Workshop Organization
- Create and schedule workshops
- Define workshop details:
  - Title and description
  - Date, time, and duration
  - Venue/location
  - Maximum capacity
- Assign speakers to workshops
- Link sponsor organizations
- Assign Workshop Supervisors
- Monitor registration numbers

##### Team & Idea Oversight
- View all teams in the edition
- Monitor team formation progress
- Track idea submissions
- Review submission statistics
- Handle escalations from supervisors
- Resolve disputes

##### Communication & Content
- Publish news and announcements
- Send mass notifications to participants
- Create email campaigns
- Manage social media integration
- Link news to Twitter for auto-posting
- Update edition content

##### Reporting & Analytics
- Generate edition reports
- View real-time statistics:
  - Team registration numbers
  - Idea submission rates
  - Workshop attendance
  - Review progress
- Export data to Excel
- Create presentation materials
- Monitor KPIs

#### Permissions
- ✅ Full CRUD within assigned edition
- ✅ Assign supervisors
- ✅ Create tracks and workshops
- ✅ Send announcements
- ✅ Generate edition reports

#### Restrictions
- ❌ Cannot access other editions
- ❌ Cannot create new editions
- ❌ Cannot modify system settings
- ❌ Cannot delete user accounts
- ❌ Cannot change core system configuration

---

### 3️⃣ **TRACK SUPERVISOR** (مشرف المسار)

#### Overview
- **Purpose**: Review and evaluate ideas in assigned track(s)
- **Scope**: Specific track(s) within an edition
- **Database Value**: `track_supervisor`
- **Registration**: Assigned by Hackathon Admin

#### Core Responsibilities

##### Idea Review & Evaluation
- Review ideas ONLY in assigned track(s)
- Score ideas based on criteria:
  - **Innovation** (25% weight)
  - **Feasibility** (25% weight)
  - **Impact** (20% weight)
  - **Presentation** (15% weight)
  - **Team Capability** (15% weight)
- Calculate total scores (weighted average)
- Rank ideas within track

##### Feedback Management
- Provide detailed written feedback (minimum 100 characters)
- Make review decisions:
  - ✅ **Approve** - Proceed to next round
  - ⚠️ **Approve with Conditions** - Specify requirements
  - 🔄 **Request Revision** - Ask for improvements
  - ❌ **Reject** - With detailed justification
- Set revision deadlines
- Track revision submissions

##### Team Communication
- Send direct messages to teams
- Request clarifications on submissions
- Schedule meetings (virtual or physical)
- Provide guidance and mentorship
- Answer team questions
- Send progress updates

##### Track Monitoring
- View all teams in assigned track
- Monitor submission timeline
- Track review progress
- Identify outstanding submissions
- Flag issues to Hackathon Admin
- Ensure deadline compliance

##### Reporting
- Generate track-specific reports
- Export review summaries
- Provide winner recommendations
- Document review rationale
- Create track analytics

#### Permissions
- ✅ View ideas in assigned tracks
- ✅ Score and review ideas
- ✅ Communicate with track teams
- ✅ Generate track reports
- ✅ Export track data

#### Restrictions
- ❌ Cannot access other tracks
- ❌ Cannot create/delete teams
- ❌ Cannot modify submissions
- ❌ Cannot access workshops
- ❌ Cannot change track settings
- ❌ Cannot override admin decisions

---

### 4️⃣ **WORKSHOP SUPERVISOR** (مشرف الورشة)

#### Overview
- **Purpose**: Manage workshop attendance and check-ins
- **Scope**: Specific workshop(s) within an edition
- **Database Value**: `workshop_supervisor`
- **Registration**: Assigned by Hackathon Admin

#### Core Responsibilities

##### Attendance Management
- Use QR scanner for participant check-in
- Verify participant registrations
- Handle walk-in registrations
- Track real-time attendance
- Monitor workshop capacity
- Manage waiting lists

##### Check-in Operations
- Operate mobile QR scanner
- Process check-ins via:
  - QR code scanning
  - Manual ID entry
  - Barcode scanning
- Handle technical issues
- Generate temporary badges
- Resolve check-in conflicts

##### Workshop Coordination
- Arrive before workshop starts
- Coordinate with speakers
- Ensure room setup
- Manage participant flow
- Handle on-site issues
- Assist with technical needs

##### Data Management
- Record accurate attendance
- Note late arrivals
- Track no-shows
- Document issues
- Update capacity status
- Maintain attendance logs

##### Reporting
- Generate attendance reports
- Export participant lists
- Calculate attendance rates
- Report to Hackathon Admin
- Document workshop feedback
- Create post-workshop summary

#### Permissions
- ✅ View assigned workshop details
- ✅ Scan QR codes
- ✅ Mark attendance
- ✅ Register walk-ins
- ✅ Generate attendance reports

#### Restrictions
- ❌ Cannot modify workshop details
- ❌ Cannot access team/idea data
- ❌ Cannot change speakers
- ❌ Cannot cancel workshops
- ❌ Cannot access other workshops
- ❌ Cannot review ideas

---

### 5️⃣ **TEAM LEADER** (قائد الفريق)

#### Overview
- **Purpose**: Create and lead ONE competition team
- **Scope**: Own team within current edition
- **Database Value**: `team_leader`
- **Registration**: Self-registration (public)

#### Core Responsibilities

##### Team Creation & Management
- Create exactly ONE team
- Choose unique team name
- Write team description
- Select competition track
- Cannot create multiple teams
- Cannot join other teams

##### Member Management
- Invite up to 4 members (5 total including leader)
- Send invitations via:
  - Email address
  - National ID
- Accept/reject join requests
- Remove members if needed
- Manage member permissions
- Assign tasks to members

##### Idea Submission
- Submit ONE idea to chosen track
- Write comprehensive idea description
- Upload supporting documents:
  - Maximum 8 files
  - 15MB per file limit
  - Accepted formats: PDF, DOCX, PPTX
- Edit submission until deadline
- Track submission status

##### Competition Management
- Monitor idea review status
- View reviewer feedback
- Respond to revision requests
- Submit revised materials
- Communicate with Track Supervisor
- Prepare for presentations

##### Team Leadership
- Coordinate team activities
- Set internal deadlines
- Distribute responsibilities
- Resolve team conflicts
- Ensure timely submissions
- Represent team officially

##### Workshop Participation
- Register for workshops
- Receive attendance QR codes
- Attend training sessions
- Apply workshop learnings

#### Permissions
- ✅ Create one team
- ✅ Invite/remove members
- ✅ Submit one idea
- ✅ Edit idea until deadline
- ✅ Communicate with supervisors
- ✅ Dissolve team (before submission)

#### Restrictions
- ❌ Cannot create multiple teams
- ❌ Cannot join another team
- ❌ Cannot leave team (must dissolve)
- ❌ Cannot submit multiple ideas
- ❌ Cannot review other ideas
- ❌ Cannot edit after deadline

---

### 6️⃣ **TEAM MEMBER** (عضو الفريق)

#### Overview
- **Purpose**: Participate as member in ONE team
- **Scope**: Own team within current edition
- **Database Value**: `team_member`
- **Registration**: Self-registration (public)

#### Core Responsibilities

##### Team Participation
- Join ONE team via:
  - Direct invitation from leader
  - Request to join (requires approval)
- Collaborate on idea development
- Contribute to team discussions
- Support team objectives
- Attend team meetings

##### Idea Contribution
- View team's submitted idea
- Help develop idea content
- Upload supporting files (with permission)
- Edit idea sections (with permission)
- Review draft submissions
- Provide feedback

##### Limited Permissions
- Cannot submit idea directly
- Cannot delete idea
- Cannot invite new members
- Cannot remove other members
- Cannot dissolve team
- Can leave team (before submission)

##### Individual Activities
- Maintain personal profile
- Register for workshops independently
- Receive personal QR codes
- Track own participation
- View team progress

#### Permissions
- ✅ Join one team
- ✅ View team idea
- ✅ Contribute content (with permission)
- ✅ Leave team
- ✅ Register for workshops

#### Restrictions
- ❌ Cannot create team
- ❌ Cannot be in multiple teams
- ❌ Cannot submit idea
- ❌ Cannot manage members
- ❌ Cannot change team settings
- ❌ Cannot delete team content

---

### 7️⃣ **VISITOR** (زائر)

#### Overview
- **Purpose**: Attend workshops without competition participation
- **Scope**: Public workshops and events
- **Database Value**: `visitor`
- **Registration**: Self-registration (public)

#### Core Responsibilities

##### Workshop Participation
- Browse available workshops
- View workshop details:
  - Topics and descriptions
  - Speakers and organizations
  - Dates and times
  - Available seats
- Register for multiple workshops
- Receive QR codes via email
- Attend workshops
- Provide feedback

##### Public Access
- View hackathon information
- Read news and announcements
- See competition tracks
- View prizes and awards
- Access public schedules
- Download public resources

##### Profile Management
- Create visitor account
- Update personal information
- Manage workshop registrations
- View registration history
- Download attendance certificates
- Update contact preferences

#### Permissions
- ✅ Register for workshops
- ✅ View public information
- ✅ Update own profile
- ✅ Receive certificates

#### Restrictions
- ❌ Cannot create teams
- ❌ Cannot join teams  
- ❌ Cannot submit ideas
- ❌ Cannot view team details
- ❌ Cannot access competition features
- ❌ Cannot review ideas
- ❌ Cannot access private content

---

## 📊 COMPREHENSIVE PERMISSION MATRIX

### System Features Access

| Feature | System Admin | Hackathon Admin | Track Supervisor | Workshop Supervisor | Team Leader | Team Member | Visitor |
|---------|-------------|-----------------|------------------|-------------------|-------------|-------------|---------|
| **System Settings** | ✅ Full | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| **Edition Management** | ✅ Create/Edit | ✅ Configure | ❌ | ❌ | ❌ | ❌ | ❌ |
| **User Management** | ✅ All Users | ✅ Supervisors | ❌ | ❌ | ❌ | ❌ | ❌ |
| **Track Management** | ✅ View | ✅ Full | ✅ Assigned | ❌ | 👁️ View | 👁️ View | 👁️ Public |
| **Team Management** | ✅ All | ✅ Edition | ✅ Track | ❌ | ✅ Own | 👁️ Own | ❌ |
| **Idea Management** | ✅ View All | ✅ Edition | ✅ Review | ❌ | ✅ Submit | 👁️ View | ❌ |
| **Workshop Management** | ✅ All | ✅ Create | 👁️ View | ✅ Check-in | 📝 Register | 📝 Register | 📝 Register |
| **News Management** | ✅ All | ✅ Create | ❌ | ❌ | 👁️ View | 👁️ View | 👁️ View |
| **Reports** | ✅ System | ✅ Edition | ✅ Track | ✅ Workshop | 👁️ Own | 👁️ Own | ❌ |

### Legend:
- ✅ Full access (Create, Read, Update, Delete)
- 📝 Create/Register only
- 👁️ Read only
- ❌ No access

---

## 🔄 ROLE TRANSITION RULES

### Allowed Transitions
1. **Visitor → Team Member**: By accepting team invitation
2. **User → Track Supervisor**: By Hackathon Admin assignment
3. **User → Workshop Supervisor**: By Hackathon Admin assignment

### Prohibited Transitions
1. **Team Member → Team Leader**: Must create new account
2. **Team Leader → Team Member**: Must create new account
3. **Any Role → System Admin**: Only through system configuration
4. **Any Role → Hackathon Admin**: Only by System Admin

---

## 🔐 AUTHENTICATION & SECURITY

### Login Credentials

#### Predefined Users
```
- Receive email with temporary password
- Required to change password on first login
- Two-factor authentication available
- Session timeout: 30 minutes inactive
```

#### Self-Registered Users
```
- Create own password during registration
- Email verification required
- Password requirements:
  - Minimum 8 characters
  - At least 1 uppercase letter
  - At least 1 number
  - At least 1 special character
```

### Security Policies
1. **Password Policy**: Strong passwords enforced
2. **Session Management**: Automatic logout after inactivity
3. **IP Restrictions**: Optional IP whitelisting for admins
4. **Audit Logging**: All actions logged with timestamp
5. **Data Encryption**: Sensitive data encrypted at rest
6. **API Security**: Rate limiting and authentication required

---

## 📱 ROLE-BASED UI DIFFERENCES

### Navigation Menu Items

| Menu Item | System Admin | Hackathon Admin | Track Supervisor | Workshop Supervisor | Team Leader | Team Member | Visitor |
|-----------|-------------|-----------------|------------------|-------------------|-------------|-------------|---------|
| Dashboard | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Editions | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| Users | ✅ | ⚠️ Limited | ❌ | ❌ | ❌ | ❌ | ❌ |
| Teams | ✅ | ✅ | ✅ | ❌ | ✅ My Team | ✅ My Team | ❌ |
| Ideas | ✅ | ✅ | ✅ Review | ❌ | ✅ Our Idea | ✅ Our Idea | ❌ |
| Tracks | ✅ | ✅ | ✅ My Tracks | ❌ | ✅ View | ✅ View | ❌ |
| Workshops | ✅ | ✅ | ✅ | ✅ Assigned | ✅ | ✅ | ✅ |
| Check-ins | ✅ | ✅ | ❌ | ✅ | ❌ | ❌ | ❌ |
| News | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Reports | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ |
| Settings | ✅ | ⚠️ Edition | ❌ | ❌ | ❌ | ❌ | ❌ |
| Profile | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |

---

## 📝 IMPLEMENTATION NOTES

### Database Schema Considerations
```sql
-- Users table must include
users:
  - id (primary key)
  - name (string)
  - email (unique)
  - role (enum)
  - password (hashed)
  - national_id (unique, nullable for predefined)
  - team_id (foreign key, nullable)
  - edition_id (foreign key, nullable)
  - created_by (foreign key, nullable)
  - email_verified_at (timestamp, nullable)

-- Role-specific pivot tables
track_supervisors:
  - user_id
  - track_id
  
workshop_supervisors:
  - user_id
  - workshop_id
```

### Middleware Requirements
```php
// Role-based middleware
'role:system_admin'
'role:hackathon_admin'
'role:track_supervisor'
'role:workshop_supervisor'
'role:team_leader'
'role:team_member'
'role:visitor'

// Feature-based middleware
'can:manage-editions'
'can:review-ideas'
'can:check-in-participants'
'can:submit-ideas'
```

---

## 📞 SUPPORT & ESCALATION

### Escalation Path
1. **Team Member** → Team Leader
2. **Team Leader** → Track Supervisor
3. **Track Supervisor** → Hackathon Admin
4. **Hackathon Admin** → System Admin
5. **System Admin** → Technical Support

### Common Issues by Role

| Role | Common Issues | Resolution |
|------|--------------|------------|
| System Admin | System configuration | Technical documentation |
| Hackathon Admin | Edition setup | System Admin support |
| Track Supervisor | Review process | Hackathon Admin guidance |
| Workshop Supervisor | Check-in problems | Technical support |
| Team Leader | Team management | Track Supervisor help |
| Team Member | Access issues | Team Leader assistance |
| Visitor | Registration problems | Public support |

---

## ✅ QUICK REFERENCE CHECKLIST

### For System Implementation
- [ ] Implement role-based authentication
- [ ] Create user management interfaces
- [ ] Set up permission policies
- [ ] Configure role-based routing
- [ ] Implement data scoping by role
- [ ] Create role assignment workflows
- [ ] Set up audit logging
- [ ] Test all role combinations
- [ ] Document role transitions
- [ ] Create user guides per role

---

*Last Updated: [Current Date]*
*Version: 1.0*
*System: Hackathon Management System - Ruman*