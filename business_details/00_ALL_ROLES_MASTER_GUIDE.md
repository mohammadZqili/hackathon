# 🎯 HACKATHON SYSTEM - COMPLETE BUSINESS DETAILS MASTER GUIDE
## Comprehensive Design Specifications for All Roles

---

## 📊 SYSTEM OVERVIEW

### Design System Foundation
- **Primary Color**: Mint Green (#10B981, #4ADE80)
- **Secondary Colors**: Role-specific gradients
- **Typography**: Inter font family
- **Icons**: 24px outline style (Heroicons)
- **Border Radius**: 12px (cards), 16px (containers)
- **Shadows**: Subtle (0px 4px 6px rgba(0,0,0,0.1))

### Global Layout Structure
- **Sidebar**: 320px width (collapsible)
- **Header**: 64px height (fixed)
- **Content Area**: Flex-1 with 24px padding
- **Max Content Width**: 1440px (centered)
- **Mobile Breakpoint**: 768px

---

## 👥 ROLE HIERARCHY & PERMISSIONS

### 1. SYSTEM ADMIN (مسؤول النظام)
**Highest Level - Full System Control**
- Database value: `system_admin`
- Color theme: Deep Blue gradient
- Can access ALL features
- Manages hackathon editions
- Controls system settings

### 2. HACKATHON ADMIN (مشرف عام)
**Edition Management**
- Database value: `hackathon_admin`
- Color theme: Purple gradient
- Manages current hackathon
- Creates tracks and workshops
- Assigns supervisors

### 3. TRACK SUPERVISOR (مشرف مسار)
**Track-Specific Management**
- Database value: `track_supervisor`
- Color theme: Orange gradient
- Reviews ideas in assigned track
- Provides feedback and scoring
- Cannot access other tracks

### 4. WORKSHOP SUPERVISOR (مشرف ورشة)
**Workshop Management**
- Database value: `workshop_supervisor`
- Color theme: Teal gradient
- Manages workshop attendance
- QR code scanning capability
- Reports to hackathon admin

### 5. TEAM LEADER (قائد فريق)
**Team Management**
- Database value: `team_leader`
- Color theme: Mint Green gradient
- Creates and manages ONE team
- Submits team idea
- Full team control

### 6. TEAM MEMBER (عضو فريق)
**Team Participation**
- Database value: `team_member`
- Color theme: Light Mint gradient
- Joins ONE team
- Contributes to idea
- Limited permissions

### 7. VISITOR (زائر)
**Public Access**
- Database value: `visitor`
- Color theme: Gray gradient
- Browses public information
- Registers for workshops only
- No team capabilities

---

## 📄 COMMON PAGE PATTERNS

### Authentication Pages (All Roles)

#### LOGIN PAGE
**File**: `Login.png` (All roles have identical login)
**Layout**:
- Split screen: 50% image, 50% form
- Left side: Hackathon branding image
- Right side: Login form

**Form Fields**:
1. Email/National ID toggle
2. Password field
3. Remember me checkbox
4. Forgot password link
5. Login button (role-specific redirect)
6. Register link

#### REGISTER PAGE
**File**: `Register.png`
**Unique Feature**: Role selection dropdown
**Fields**:
1. Full name (Arabic & English)
2. National ID (10 digits)
3. Email
4. Phone (with country code)
5. Password (with strength meter)
6. Confirm password
7. Role selection (critical)
8. Terms acceptance

**Role Selection Logic**:
- Visitor: Default, no approval
- Team Leader/Member: Instant
- Supervisors: Requires admin approval
- Admins: Cannot self-register

---

## 🎨 ROLE-SPECIFIC DASHBOARDS

### Common Dashboard Elements
All dashboards share:
- Welcome message with user name
- Role-specific statistics cards
- Quick actions section
- Recent activity feed
- Notification center

### Role Dashboard Variations

#### SYSTEM ADMIN DASHBOARD
**Primary Metrics**:
- Total users by role
- Active hackathons
- System health status
- Storage usage

**Quick Actions**:
- Create new edition
- System settings
- User management
- Backup system

#### HACKATHON ADMIN DASHBOARD
**File**: `Admin_Dashboard.png`
**Primary Metrics**:
- Teams registered
- Ideas submitted
- Workshop attendance
- Track distribution

**Quick Actions**:
- Create workshop
- Add news
- Generate reports
- Send announcements

#### TRACK SUPERVISOR DASHBOARD
**File**: `Supervisor_Dashboard.png`
**Primary Metrics**:
- Ideas to review
- Pending reviews
- Average scores
- Team count in track

**Quick Actions**:
- Start reviewing
- View teams
- Export track report
- Schedule meetings

#### TEAM LEADER DASHBOARD
**Already documented in detail**

#### TEAM MEMBER DASHBOARD
**Already documented in detail**

#### VISITOR DASHBOARD
**Simplified View**:
- Available workshops
- Registered workshops
- QR codes
- No team features visible

---

## 📋 CRITICAL USER FLOWS BY ROLE

### FLOW 1: HACKATHON CREATION (System Admin)
1. System Admin → Editions page
2. Click "Create Edition"
3. Fill edition details:
   - Name, year, dates
   - Registration periods
   - Submission deadlines
4. Create tracks for edition
5. Assign Hackathon Admin
6. Activate edition

### FLOW 2: WORKSHOP CREATION (Hackathon Admin)
**Files**: `Admin_AddWorkshops.png`
1. Navigate to Workshops
2. Click "Add Workshop"
3. Fill details:
   - Title, description
   - Speaker selection
   - Date, time, venue
   - Max capacity
4. Add organization sponsors
5. Publish workshop

### FLOW 3: IDEA REVIEW (Track Supervisor)
**File**: `Idea.png`
1. Dashboard shows pending ideas
2. Click idea to review
3. View complete submission:
   - Read description
   - Download files
   - Check team info
4. Score based on criteria
5. Add feedback comments
6. Submit review decision

### FLOW 4: QR CHECK-IN (Workshop Supervisor)
**File**: `Supervisor_Workshops.png`
1. Select active workshop
2. Open QR scanner
3. Scan attendee QR code
4. Confirm check-in
5. View attendance list
6. Export attendance report

---

## 🎯 CRITICAL PAGES BY PRIORITY

### PRIORITY 1: Core System Pages
1. **Registration with role selection**
2. **Role-based dashboard routing**
3. **Team creation form**
4. **Idea submission form**
5. **Review interface**

### PRIORITY 2: Management Pages
1. **User management (Admin)**
2. **Workshop management**
3. **Track management**
4. **Team listing**
5. **Idea listing**

### PRIORITY 3: Communication Pages
1. **News/Announcements**
2. **Comments system**
3. **Notifications**
4. **Email templates**

### PRIORITY 4: Reporting Pages
1. **Edition reports**
2. **Track reports**
3. **Workshop attendance**
4. **Team statistics**

---

## 🎨 DESIGN SPECIFICATIONS

### Card Components
```css
.card {
  background: white;
  border-radius: 16px;
  padding: 24px;
  box-shadow: 0px 0px 8px rgba(0,0,0,0.05);
}

.card-header {
  font-size: 18px;
  font-weight: 600;
  margin-bottom: 16px;
}
```

### Button Styles
```css
.btn-primary {
  background: linear-gradient(135deg, #10B981, #059669);
  color: white;
  padding: 10px 20px;
  border-radius: 12px;
  font-weight: 600;
}

.btn-secondary {
  background: #F3F4F6;
  color: #374151;
  padding: 10px 20px;
  border-radius: 12px;
}
```

### Status Badges
```css
.badge-draft { background: #F3F4F6; color: #6B7280; }
.badge-submitted { background: #DBEAFE; color: #1E40AF; }
.badge-approved { background: #D1FAE5; color: #065F46; }
.badge-rejected { background: #FEE2E2; color: #991B1B; }
```

### Form Inputs
```css
.input {
  width: 100%;
  height: 48px;
  padding: 12px 16px;
  border: 1px solid #D1D5DB;
  border-radius: 12px;
  font-size: 14px;
}

.input:focus {
  border-color: #10B981;
  outline: none;
  box-shadow: 0 0 0 3px rgba(16,185,129,0.1);
}
```

---

## 📱 RESPONSIVE BREAKPOINTS

### Mobile (< 768px)
- Single column layouts
- Hamburger menu
- Full-screen modals
- Stacked cards
- Hidden secondary info

### Tablet (768px - 1024px)
- 2 column grids
- Collapsible sidebar
- 90% width modals
- Responsive tables

### Desktop (> 1024px)
- Full layouts
- 3-4 column grids
- Fixed sidebar
- Hover states
- Keyboard shortcuts

---

## 🌍 ARABIC LOCALIZATION

### RTL Layout Changes
- Sidebar moves to right
- Text alignment reversed
- Icons flipped where directional
- Progress bars RTL
- Form labels right-aligned

### Key Translations
```javascript
const translations = {
  'Dashboard': 'لوحة القيادة',
  'Teams': 'الفرق',
  'Ideas': 'الأفكار',
  'Workshops': 'ورش العمل',
  'Submit': 'إرسال',
  'Cancel': 'إلغاء',
  'Save': 'حفظ',
  'Edit': 'تعديل',
  'Delete': 'حذف',
  'Search': 'بحث'
}
```

---

## 🔄 STATE MANAGEMENT

### Global States
```javascript
// User state
{
  user: {
    id: 'xxx',
    name: 'John Doe',
    role: 'team_leader',
    team_id: 'xxx',
    permissions: []
  },
  
  // Navigation state
  navigation: {
    currentPage: 'dashboard',
    breadcrumbs: [],
    sidebarOpen: true
  },
  
  // Notification state
  notifications: {
    unread: 5,
    items: []
  }
}
```

### Page-Specific States
```javascript
// Team page state
{
  team: {
    loading: false,
    data: {},
    members: [],
    invitations: []
  },
  
  // Idea page state
  idea: {
    loading: false,
    data: {},
    files: [],
    comments: [],
    canEdit: false
  }
}
```

---

## ⚡ PERFORMANCE REQUIREMENTS

### Page Load Times
- Initial load: < 3 seconds
- Subsequent navigation: < 1 second
- API responses: < 500ms
- File uploads: Progress indicator required

### Optimization Strategies
1. Lazy load images
2. Code splitting by role
3. Cache API responses
4. Compress assets
5. CDN for static files

---

## 🔒 SECURITY CONSIDERATIONS

### Authentication
- JWT tokens with refresh
- Session timeout: 30 minutes
- Remember me: 7 days
- 2FA for admin roles

### Authorization
- Role-based access control
- Permission checking on routes
- API endpoint protection
- File access restrictions

### Data Protection
- Input sanitization
- XSS prevention
- CSRF tokens
- SQL injection prevention
- File type validation

---

## 📊 ANALYTICS TRACKING

### Key Events to Track
1. User registration by role
2. Team creation
3. Idea submission
4. Review completion
5. Workshop registration
6. File uploads
7. Page views by role
8. Error occurrences

### Metrics Dashboard
- User engagement by role
- Feature usage statistics
- Error rates
- Performance metrics
- Conversion funnels

---

## 🚀 IMPLEMENTATION ROADMAP

### Phase 1: Core Foundation (Week 1)
1. Authentication system
2. Role-based routing
3. Dashboard templates
4. Navigation components

### Phase 2: Team Features (Week 2)
1. Team CRUD operations
2. Member management
3. Invitation system
4. Team dashboards

### Phase 3: Idea Management (Week 3)
1. Idea submission forms
2. File upload system
3. Review interface
4. Feedback system

### Phase 4: Admin Features (Week 4)
1. User management
2. Workshop system
3. Reporting tools
4. Settings pages

### Phase 5: Polish & Deploy (Week 5)
1. Arabic translations
2. Mobile optimization
3. Performance tuning
4. Security audit
5. Deployment

---

## 📝 TESTING CHECKLIST

### Functional Testing
- [ ] All user roles can register
- [ ] Role-based routing works
- [ ] Forms validate correctly
- [ ] File uploads work
- [ ] Permissions enforced

### UI/UX Testing
- [ ] Responsive on all devices
- [ ] Arabic RTL layout
- [ ] Dark mode (if applicable)
- [ ] Loading states
- [ ] Error messages

### Performance Testing
- [ ] Page load times
- [ ] API response times
- [ ] Concurrent users
- [ ] File upload limits

### Security Testing
- [ ] Authentication flow
- [ ] Authorization checks
- [ ] Input validation
- [ ] File security
- [ ] API protection

---

## 🎯 SUCCESS CRITERIA

### System Launch Ready When:
1. All 7 roles functional
2. Core workflows operational
3. Arabic/English support
4. Mobile responsive
5. Security verified
6. Performance acceptable
7. Admin tools ready
8. Documentation complete

---

## 📞 SUPPORT & MAINTENANCE

### Post-Launch Priorities
1. User feedback collection
2. Bug fixes
3. Performance monitoring
4. Security updates
5. Feature enhancements

### Monitoring Dashboard
- System health
- User activity
- Error logs
- Performance metrics
- Security alerts

---

This master guide provides the essential business details for implementing all roles in the hackathon system. For detailed page-by-page specifications for Team Leader and Team Member roles, refer to their individual documentation files (01_TEAM_LEADER_ROLE.md and 02_TEAM_MEMBER_ROLE.md).