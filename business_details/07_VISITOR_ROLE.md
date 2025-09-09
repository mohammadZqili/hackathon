# 🌐 VISITOR ROLE - COMPLETE BUSINESS DETAILS
## Comprehensive Design Specifications & User Flows

---

## 📊 ROLE OVERVIEW

### Role Identity
- **Arabic Name**: زائر
- **English Name**: Visitor
- **Database Value**: `visitor`
- **Color Theme**: Gray Gradient (#9CA3AF, #6B7280)
- **Icon**: Globe/User icon
- **Permission Level**: Public access only

### Core Capabilities
1. Browse public hackathon information
2. View available workshops
3. Register for workshops
4. View personal QR codes for attendance
5. Access public news and announcements
6. Basic profile management

### Restrictions
- **Cannot** create or join teams
- **Cannot** submit ideas
- **Cannot** access administrative features
- **Cannot** view team details or ideas
- **Cannot** participate in hackathon competitions
- **Cannot** access supervisor features

### Purpose
The Visitor role is designed for:
- General public interested in workshops
- Non-competitive participants
- Workshop-only attendees
- Observers and learners
- People exploring the hackathon

---

## 🏠 PAGE 1: DASHBOARD
**Route**: `/visitor/dashboard`
**Purpose**: Simple landing page with workshop focus

### Layout Structure
```
┌─────────────────────────────────────────────────┐
│ Header (64px)                                   │
├──────────┬──────────────────────────────────────┤
│          │                                      │
│ Sidebar  │     Main Content Area               │
│ (280px)  │                                      │
│          │   ┌────────────────────────────┐    │
│ Dashboard│   │  Welcome Message           │    │
│ All      │   └────────────────────────────┘    │
│ Workshops│                                      │
│ My       │   ┌────────────────────────────┐    │
│ Workshops│   │  Available Workshops       │    │
│          │   └────────────────────────────┘    │
│          │                                      │
│          │   ┌────────────────────────────┐    │
│          │   │  Upcoming Events           │    │
│          │   └────────────────────────────┘    │
└──────────┴──────────────────────────────────────┘
```

### Sidebar Navigation (Simplified)
```css
.visitor-sidebar {
  background: white;
  width: 280px;
  padding: 24px 16px;
}
.sidebar-item {
  padding: 12px 20px;
  border-radius: 8px;
  font-size: 14px;
  color: #4B5563;
  margin-bottom: 4px;
}
.sidebar-item.active {
  background: linear-gradient(135deg, #9CA3AF, #6B7280);
  color: white;
}
```

**Navigation Items** (Limited):
1. **Dashboard** - Overview page
2. **All Workshops** - Browse available workshops
3. **My Workshops** - Registered workshops with QR codes

### Welcome Section
```css
.welcome-card {
  background: linear-gradient(135deg, #F3F4F6, #E5E7EB);
  border-radius: 16px;
  padding: 32px;
  text-align: center;
  margin-bottom: 32px;
}
.welcome-title {
  font-size: 28px;
  font-weight: 700;
  color: #111827;
  margin-bottom: 8px;
}
.welcome-subtitle {
  font-size: 16px;
  color: #6B7280;
}
```

**Content**:
- Title: "Welcome to RumanHack"
- Subtitle: "Explore workshops to enhance your skills and knowledge"
- No team-related CTAs

### Quick Stats (Visitor-Specific)
```css
.stats-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 20px;
  margin-bottom: 32px;
}
.stat-card {
  background: white;
  border-radius: 12px;
  padding: 20px;
  text-align: center;
}
```

**Metrics**:
1. **Available Workshops**: Count of open workshops
2. **My Registrations**: Number registered for
3. **Upcoming Events**: Next workshop date

### Featured Workshops Preview
- Show 3 upcoming workshops
- Registration button for each
- Link to full workshops page

---

## 📚 PAGE 2: ALL WORKSHOPS
**File**: `visitor/Team/workshop.png`
**Route**: `/visitor/workshops`
**Purpose**: Browse and register for available workshops

### Page Header
```css
.page-header {
  margin-bottom: 32px;
}
.page-title {
  font-size: 32px;
  font-weight: 700;
  color: #111827;
  margin-bottom: 8px;
}
.page-description {
  font-size: 16px;
  color: #6B7280;
  line-height: 1.6;
}
```

**Title**: "Upcoming Workshops"
**Description**: "Explore our upcoming workshops designed to enhance your skills and knowledge in various fields. Register now to secure your spot!"

### Workshop Cards Layout
```css
.workshops-container {
  display: flex;
  flex-direction: column;
  gap: 24px;
}
.workshop-card {
  background: white;
  border-radius: 16px;
  padding: 24px;
  display: flex;
  gap: 24px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
  transition: transform 0.2s;
}
.workshop-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
```

### Workshop Card Structure

#### Left Content Section (70%)
```css
.workshop-content {
  flex: 1;
}
.workshop-meta {
  font-size: 14px;
  color: #6B7280;
  margin-bottom: 12px;
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}
.meta-item {
  display: flex;
  align-items: center;
  gap: 4px;
}
.meta-separator {
  color: #D1D5DB;
}
```

**Meta Information Format**:
"Speaker: [Name] | Sponsor: [Company] | Date: [Date] | Time: [Time Range]"

**Title**:
```css
.workshop-title {
  font-size: 20px;
  font-weight: 600;
  color: #111827;
  margin-bottom: 12px;
}
```

**Description**:
```css
.workshop-description {
  font-size: 14px;
  color: #4B5563;
  line-height: 1.6;
  margin-bottom: 20px;
  max-width: 600px;
}
```

**Register Button**:
```css
.register-btn {
  background: white;
  border: 2px solid #10B981;
  color: #10B981;
  padding: 10px 28px;
  border-radius: 8px;
  font-weight: 600;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.2s;
}
.register-btn:hover {
  background: #10B981;
  color: white;
}
.register-btn.registered {
  background: #10B981;
  color: white;
  pointer-events: none;
}
```

#### Right Image Section (30%)
```css
.workshop-image {
  width: 280px;
  height: 160px;
  border-radius: 12px;
  object-fit: cover;
  background: #F3F4F6;
}
```

### Sample Workshop Data

#### Workshop 1
- **Title**: "Mastering Data Analysis with Python"
- **Speaker**: Dr. Evelyn Reed
- **Sponsor**: Data Insights Inc.
- **Date**: July 15, 2024
- **Time**: 10:00 AM - 12:00 PM
- **Description**: "Learn to analyze complex datasets using Python, covering data manipulation, visualization, and statistical analysis."
- **Image**: Data visualization graphic
- **Status**: Open for registration

#### Workshop 2
- **Title**: "Advanced Web Development with React"
- **Speaker**: Mr. Ethan Carter
- **Sponsor**: WebDev Solutions
- **Date**: July 22, 2024
- **Time**: 2:00 PM - 4:00 PM
- **Description**: "Dive deep into React, building dynamic and responsive web applications with state management and component architecture."
- **Image**: React code illustration
- **Status**: Open for registration

#### Workshop 3
- **Title**: "Introduction to Machine Learning"
- **Speaker**: Ms. Olivia Bennett
- **Sponsor**: AI Innovations
- **Date**: July 29, 2024
- **Time**: 11:00 AM - 1:00 PM
- **Description**: "Get started with machine learning, covering fundamental concepts, algorithms, and practical applications using popular libraries."
- **Image**: ML/AI themed graphic
- **Status**: Open for registration

### Filtering and Search
```css
.filter-section {
  display: flex;
  gap: 16px;
  margin-bottom: 24px;
}
.search-input {
  flex: 1;
  max-width: 400px;
  height: 44px;
  padding: 0 16px;
  border: 1px solid #D1D5DB;
  border-radius: 8px;
}
.filter-dropdown {
  min-width: 150px;
  height: 44px;
  padding: 0 16px;
  border: 1px solid #D1D5DB;
  border-radius: 8px;
}
```

**Filter Options**:
- All Workshops
- This Week
- This Month
- By Track
- By Speaker

---

## 📋 PAGE 3: MY WORKSHOPS
**File**: `visitor/Team/workshop-1.png`
**Route**: `/visitor/my-workshops`
**Purpose**: View registered workshops and access QR codes

### Page Header
```css
.my-workshops-header {
  margin-bottom: 32px;
}
```

**Title**: "My Registered Workshops"
**Subtitle**: "View your registered workshops and access your personal barcode for check-in at each event."

### Registered Workshop Cards
**Similar layout to All Workshops page with modifications**

#### Key Differences
1. **Button Change**:
   ```css
   .barcode-btn {
     background: linear-gradient(135deg, #3B82F6, #2563EB);
     color: white;
     padding: 10px 28px;
     border-radius: 8px;
     font-weight: 600;
     cursor: pointer;
   }
   .barcode-btn:hover {
     transform: translateY(-1px);
     box-shadow: 0 4px 8px rgba(59,130,246,0.3);
   }
   ```
   Text: "View Barcode"

2. **Status Indicators**:
   ```css
   .workshop-status {
     display: inline-block;
     padding: 4px 12px;
     border-radius: 20px;
     font-size: 12px;
     font-weight: 600;
     margin-left: 12px;
   }
   .status-upcoming {
     background: #DBEAFE;
     color: #1E40AF;
   }
   .status-ongoing {
     background: #D1FAE5;
     color: #065F46;
   }
   .status-completed {
     background: #F3F4F6;
     color: #6B7280;
   }
   ```

### QR Code Modal
**Trigger**: Click "View Barcode"

```css
.qr-modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0,0,0,0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}
.qr-content {
  background: white;
  border-radius: 16px;
  padding: 32px;
  max-width: 400px;
  text-align: center;
}
.qr-code {
  width: 200px;
  height: 200px;
  margin: 24px auto;
  border: 2px solid #E5E7EB;
  border-radius: 12px;
  padding: 16px;
}
```

**Modal Content**:
1. Workshop title
2. Date and time
3. QR code image
4. Attendee name
5. Registration ID
6. "Save as Image" button
7. "Close" button

### Empty State
```css
.empty-state {
  text-align: center;
  padding: 60px 20px;
}
.empty-icon {
  width: 120px;
  height: 120px;
  margin: 0 auto 24px;
  opacity: 0.5;
}
.empty-title {
  font-size: 20px;
  font-weight: 600;
  color: #374151;
  margin-bottom: 8px;
}
.empty-description {
  font-size: 14px;
  color: #6B7280;
  margin-bottom: 24px;
}
.browse-btn {
  background: #10B981;
  color: white;
  padding: 10px 24px;
  border-radius: 8px;
  font-weight: 600;
}
```

---

## 👤 PAGE 4: MY PROFILE
**File**: `visitor/Team/workshop-2.png`
**Route**: `/visitor/profile`
**Purpose**: Manage personal information

### Profile Layout
```css
.profile-container {
  max-width: 800px;
  margin: 0 auto;
  background: white;
  border-radius: 16px;
  padding: 40px;
}
```

### Avatar Section
```css
.avatar-section {
  text-align: center;
  margin-bottom: 40px;
}
.avatar-wrapper {
  width: 120px;
  height: 120px;
  margin: 0 auto 16px;
  border-radius: 50%;
  background: linear-gradient(135deg, #FEE2E2, #FECACA);
  padding: 4px;
}
.avatar-image {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  object-fit: cover;
}
.profile-name {
  font-size: 24px;
  font-weight: 600;
  color: #111827;
  margin-bottom: 4px;
}
.profile-email {
  font-size: 14px;
  color: #6B7280;
}
```

### Form Fields
**Layout**: 2-column grid
```css
.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
  margin-bottom: 32px;
}
.form-field {
  display: flex;
  flex-direction: column;
}
.field-label {
  font-size: 14px;
  font-weight: 500;
  color: #374151;
  margin-bottom: 8px;
}
.field-input {
  height: 48px;
  padding: 12px 16px;
  background: #F9FAFB;
  border: 1px solid #E5E7EB;
  border-radius: 8px;
  font-size: 14px;
}
```

### Profile Fields

#### Personal Information
1. **Name** (Full width)
2. **Email** (Full width)
3. **Date of Birth** (Left column)
4. **Phone Number** (Right column)
5. **National ID** (Full width)

#### Security
1. **Password** (Left column)
2. **Confirm Password** (Right column)

#### Professional Information
1. **Occupation** (Radio: Student/Employee)
2. **Job Title** (Text input, enabled if Employee selected)

### Occupation Selection
```css
.radio-group {
  display: flex;
  gap: 32px;
  padding: 12px 0;
}
.radio-option {
  display: flex;
  align-items: center;
  gap: 8px;
}
.radio-input {
  width: 20px;
  height: 20px;
  accent-color: #10B981;
}
.radio-label {
  font-size: 14px;
  color: #374151;
}
```

### Save Button
```css
.save-changes-btn {
  background: linear-gradient(135deg, #10B981, #059669);
  color: white;
  padding: 12px 32px;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 600;
  float: right;
  cursor: pointer;
}
```

---

## 🔑 PAGE 5: LOGIN
**File**: `visitor/Login.png`
**Route**: `/login`
**Purpose**: User authentication (shared across all roles)

### Layout
- Split screen design
- Left: Branding/Image
- Right: Login form

### Login Form
```css
.login-form {
  max-width: 400px;
  margin: 0 auto;
}
```

**Fields**:
1. Email or National ID (toggle)
2. Password
3. Remember me checkbox
4. Forgot password link
5. Login button
6. Register link

**Post-Login**: Redirect to visitor dashboard

---

## 📝 PAGE 6: REGISTER
**File**: `visitor/Register.png`
**Route**: `/register`
**Purpose**: New user registration

### Registration Form
**Key Difference**: Role selection includes "Visitor" as default option

**Fields**:
1. Full name (Arabic & English)
2. National ID
3. Email
4. Phone
5. Password
6. Confirm password
7. **Role**: Dropdown with "Visitor" selected by default
8. Terms acceptance

**Visitor Registration Benefits**:
- Instant approval (no admin review needed)
- Immediate access to workshops
- Simplified onboarding

---

## 🔄 CRITICAL USER FLOWS

### FLOW 1: Workshop Registration
1. **Browse Workshops**:
   - Visit All Workshops page
   - Filter/search for interests
   - Read workshop details

2. **Register**:
   - Click "Register" button
   - Confirm registration
   - Receive confirmation email

3. **Access QR Code**:
   - Go to My Workshops
   - Find registered workshop
   - Click "View Barcode"
   - Save QR for event

4. **Attend Workshop**:
   - Show QR at venue
   - Get scanned by supervisor
   - Attend workshop

### FLOW 2: First-Time Visitor
1. **Landing**:
   - Arrive at public site
   - See workshop offerings

2. **Registration**:
   - Click Register
   - Select "Visitor" role
   - Complete registration

3. **Exploration**:
   - Browse available workshops
   - Read descriptions
   - Check schedules

4. **Engagement**:
   - Register for workshops
   - Update profile
   - Access QR codes

### FLOW 3: Returning Visitor
1. **Login**:
   - Enter credentials
   - Land on dashboard

2. **Check Workshops**:
   - View My Workshops
   - See upcoming events
   - Access QR codes

3. **Discover New**:
   - Browse new workshops
   - Register for additional

### FLOW 4: Profile Management
1. **Access Profile**:
   - Click profile icon
   - View current info

2. **Update Details**:
   - Edit fields
   - Update occupation
   - Change password

3. **Save Changes**:
   - Click save
   - Receive confirmation

---

## 📱 RESPONSIVE BEHAVIOR

### Mobile (< 768px)
```css
@media (max-width: 768px) {
  .sidebar { display: none; }
  .workshop-card { flex-direction: column; }
  .workshop-image { width: 100%; }
  .form-grid { grid-template-columns: 1fr; }
  .stat-cards { grid-template-columns: 1fr; }
}
```

### Tablet (768px - 1024px)
```css
@media (max-width: 1024px) {
  .sidebar { width: 240px; }
  .workshop-cards { gap: 16px; }
  .form-grid { gap: 16px; }
}
```

### Desktop (> 1024px)
- Full layout as designed
- Hover effects enabled
- Side-by-side layouts

---

## 🎨 COMPONENT SPECIFICATIONS

### Workshop Card Component
```javascript
{
  props: {
    id: 'string',
    title: 'string',
    speaker: 'string',
    sponsor: 'string',
    date: 'date',
    time: 'string',
    description: 'string',
    image: 'url',
    capacity: 'number',
    registered: 'number',
    isRegistered: 'boolean'
  },
  actions: {
    register: () => void,
    viewDetails: () => void,
    viewBarcode: () => void
  },
  states: {
    loading: false,
    registered: false,
    full: false
  }
}
```

### QR Code Modal Component
```javascript
{
  data: {
    workshopTitle: 'string',
    workshopDate: 'date',
    attendeeName: 'string',
    registrationId: 'string',
    qrCodeData: 'string'
  },
  actions: {
    saveAsImage: () => void,
    share: () => void,
    close: () => void
  }
}
```

### Profile Form Component
```javascript
{
  fields: {
    name: { required: true },
    email: { required: true, type: 'email' },
    phone: { required: true, pattern: 'phone' },
    nationalId: { required: true, length: 10 },
    password: { min: 8, pattern: 'strong' },
    occupation: { type: 'radio', options: ['student', 'employee'] }
  },
  validation: {
    onBlur: true,
    onSubmit: true
  }
}
```

---

## 🔒 PERMISSIONS & SECURITY

### Visitor Permissions
```javascript
const visitorPermissions = {
  workshops: {
    browse: true,
    register: true,
    view_own: true,
    view_qr: true
  },
  teams: {
    create: false,
    join: false,
    view: false
  },
  ideas: {
    submit: false,
    view: false,
    edit: false
  },
  profile: {
    view_own: true,
    edit_own: true
  },
  admin: {
    access: false
  }
}
```

### Data Access
- Can only see public workshop information
- Cannot access team or idea data
- Cannot view other users' profiles
- Limited to own registration data

### Security Features
1. Rate limiting on registrations
2. QR code expiry after event
3. Email verification required
4. Session timeout: 2 hours

---

## 📊 METRICS & ANALYTICS

### Visitor Metrics
1. **Registration Rate**: Workshops registered / viewed
2. **Attendance Rate**: Attended / registered
3. **Profile Completion**: Filled fields percentage
4. **Return Rate**: Multiple workshop registrations

### Dashboard Stats
- Total available workshops
- My registrations count
- Upcoming workshop dates
- Popular workshop categories

---

## 🌍 LOCALIZATION

### Arabic Translations
```javascript
const ar_translations = {
  'Visitor': 'زائر',
  'All Workshops': 'جميع الورش',
  'My Workshops': 'ورشي',
  'Register': 'تسجيل',
  'View Barcode': 'عرض الباركود',
  'My Profile': 'ملفي الشخصي',
  'Save Changes': 'حفظ التغييرات',
  'Upcoming Workshops': 'الورش القادمة'
}
```

### RTL Support
- Full RTL layout
- Mirrored components
- Right-aligned text
- Flipped navigation

---

## ⚡ PERFORMANCE REQUIREMENTS

### Load Times
- Dashboard: < 1 second
- Workshops list: < 1.5 seconds
- QR generation: < 500ms
- Profile save: < 1 second

### Optimization
1. Lazy load workshop images
2. Cache workshop data
3. Compress QR codes
4. Paginate workshop lists
5. CDN for static assets

---

## 🎯 SUCCESS METRICS

### KPIs for Visitor Role
1. **Conversion Rate**: Visitors who register for workshops
2. **Engagement**: Average workshops per visitor
3. **Retention**: Returning visitors percentage
4. **Satisfaction**: Workshop ratings
5. **Growth**: New visitor registrations

---

This completes the Visitor role documentation with all pages, components, and workflows detailed for implementation.