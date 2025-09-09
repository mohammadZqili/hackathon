# 🎫 WORKSHOP SUPERVISOR ROLE - COMPLETE BUSINESS DETAILS
## Comprehensive Design Specifications & User Flows

---

## 📊 ROLE OVERVIEW

### Role Identity
- **Arabic Name**: مشرف ورشة
- **English Name**: Workshop Supervisor
- **Database Value**: `workshop_supervisor`
- **Color Theme**: Teal Gradient (#14B8A6, #0D9488)
- **Icon**: Users/Workshop icon
- **Permission Level**: Limited to assigned workshops

### Core Responsibilities
1. Manage workshop attendance via QR scanning
2. Track participant check-ins
3. Generate attendance reports
4. Monitor workshop capacity
5. Coordinate with speakers
6. Handle on-site workshop logistics

### Access Restrictions
- Can only access assigned workshops
- Cannot modify workshop details
- Cannot access other supervisors' workshops
- Cannot view team or idea information
- Reports to Hackathon Admin

---

## 🏠 PAGE 1: DASHBOARD
**File**: `supervisor/User_Dashboard.png`
**Route**: `/workshop-supervisor/dashboard`
**Purpose**: Central hub showing workshop assignments and key metrics

### Layout Structure
```
┌─────────────────────────────────────────────────┐
│ Header (64px)                                   │
├──────────┬──────────────────────────────────────┤
│          │                                      │
│ Sidebar  │     Main Content Area               │
│ (280px)  │                                      │
│          │     ┌─────────────────────────┐     │
│ ▪ Dashboard │     │   Summary Section      │     │
│ ▪ Ideas   │     └─────────────────────────┘     │
│ ▪ Tracks  │                                      │
│ ▪ Workshops│     ┌─────────────────────────┐     │
│ ▪ Check-ins│     │   Ideas Section         │     │
│          │     └─────────────────────────┘     │
│          │                                      │
│          │     ┌─────────────────────────┐     │
│          │     │   Workshops Section      │     │
│          │     └─────────────────────────┘     │
│          │                                      │
└──────────┴──────────────────────────────────────┘
```

### Components

#### Header Section
- **Logo**: Ruman logo (left)
- **Search Bar**: 400px width, placeholder "Search"
- **Icons**: Dark mode toggle, language switcher, notifications
- **User Profile**: Avatar + name + role badge

#### Sidebar Navigation
```css
.sidebar-item {
  padding: 12px 20px;
  border-radius: 8px;
  font-size: 14px;
  color: #4B5563;
}
.sidebar-item.active {
  background: linear-gradient(135deg, #14B8A6, #0D9488);
  color: white;
}
```

Navigation Items:
1. **Dashboard** (active) - Overview page
2. **Ideas** - Review ideas with your track
3. **Tracks** - View track information
4. **Workshops** - Manage assigned workshops
5. **Check-ins** - QR scanning interface

#### Summary Section
**Heading**: "Summary"
**Layout**: Single card with illustration

Content Card:
```css
.summary-card {
  background: white;
  border-radius: 16px;
  padding: 32px;
  min-height: 200px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
```

- **Left Content**:
  - Title: "Ideas"
  - Subtitle: "Review ideas with your track."
  - Action Button: "Review Ideas" (teal, 140px × 44px)
  
- **Right Content**:
  - Illustration: Calendar/planning image (200px × 150px)

#### Ideas Section
**Note**: This section appears but Workshop Supervisors don't typically review ideas

#### Upcoming Workshops Section
**Heading**: "Upcoming Workshops"
**Subtitle**: "See the schedule for upcoming workshops and training sessions."

Action Elements:
- **View Workshops** button (teal, 160px × 44px)
- Workshop illustration (260px × 180px)

### Data Display Requirements
- Show only assigned workshops count
- Display next workshop timing
- Show total attendees checked in today
- List upcoming workshops in next 7 days

### User Flows
1. **Login → Dashboard**: Default landing after authentication
2. **Dashboard → Workshops**: Click workshops card or sidebar
3. **Dashboard → Check-in**: Quick access to QR scanner
4. **Dashboard → Notifications**: View workshop reminders

---

## 📋 PAGE 2: WORKSHOPS LIST
**File**: `supervisor/Supervisor_Workshops.png`
**Route**: `/workshop-supervisor/workshops`
**Purpose**: Browse and manage assigned workshops

### Page Header
```css
.page-header {
  margin-bottom: 24px;
}
.page-title {
  font-size: 32px;
  font-weight: 700;
  color: #111827;
  margin-bottom: 8px;
}
.page-subtitle {
  font-size: 16px;
  color: #6B7280;
}
```

**Title**: "Upcoming Workshops"
**Subtitle**: "Explore our upcoming workshops designed to enhance your skills and knowledge in various fields. Register now to secure your spot!"

### Workshop Cards Grid
**Layout**: 1 column, vertical stack
**Gap**: 24px between cards

#### Workshop Card Structure
```css
.workshop-card {
  background: white;
  border-radius: 16px;
  padding: 24px;
  display: flex;
  gap: 24px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
  min-height: 180px;
}
```

**Left Section** (70% width):
- **Meta Information** (top line):
  ```css
  .meta-info {
    font-size: 14px;
    color: #6B7280;
    margin-bottom: 12px;
  }
  ```
  Format: "Speaker: [Name] | Sponsor: [Company] | Date: [Date] | Time: [Time]"

- **Workshop Title**:
  ```css
  .workshop-title {
    font-size: 20px;
    font-weight: 600;
    color: #111827;
    margin-bottom: 8px;
  }
  ```

- **Description**:
  ```css
  .workshop-description {
    font-size: 14px;
    color: #4B5563;
    line-height: 1.6;
    margin-bottom: 16px;
  }
  ```

- **Action Button**:
  ```css
  .register-btn {
    background: white;
    border: 2px solid #10B981;
    color: #10B981;
    padding: 8px 24px;
    border-radius: 8px;
    font-weight: 600;
  }
  ```
  Text: "Register" or "Check Attendance"

**Right Section** (30% width):
- Workshop thumbnail image
- Size: 280px × 160px
- Border radius: 12px

### Sample Workshop Data

#### Workshop 1: Mastering Data Analysis with Python
- Speaker: Dr. Evelyn Reed
- Sponsor: Data Insights Inc.
- Date: July 15, 2024
- Time: 10:00 AM - 12:00 PM
- Description: "Learn to analyze complex datasets using Python, covering data manipulation, visualization, and statistical analysis."
- Status: Open for check-in

#### Workshop 2: Advanced Web Development with React
- Speaker: Mr. Ethan Carter
- Sponsor: WebDev Solutions
- Date: July 22, 2024
- Time: 2:00 PM - 4:00 PM
- Description: "Dive deep into React, building dynamic and responsive web applications with state management and component architecture."
- Status: Upcoming

#### Workshop 3: Introduction to Machine Learning
- Speaker: Ms. Olivia Bennett
- Sponsor: AI Innovations
- Date: July 29, 2024
- Time: 11:00 AM - 1:00 PM
- Description: "Get started with machine learning, covering fundamental concepts, algorithms, and practical applications using popular libraries."
- Status: Upcoming

### Supervisor-Specific Features
1. **Check Attendance** button replaces "Register" for active workshops
2. **View Report** option for completed workshops
3. **QR Scanner** quick access icon
4. **Capacity indicator**: "120/150 checked in"

### User Flows
1. **View Workshop Details**: Click card → Workshop detail modal
2. **Start Check-in**: Click "Check Attendance" → QR scanner page
3. **View Past Attendance**: Click completed workshop → Attendance report
4. **Export Report**: Workshop → Report → Export as PDF/Excel

---

## 📸 PAGE 3: QR CHECK-IN SCANNER
**File**: `supervisor/Supervisor_Workshops-1.png`
**Route**: `/workshop-supervisor/check-in`
**Purpose**: Scan participant QR codes for workshop attendance

### Layout Structure
```
┌─────────────────────────────────────────────────┐
│                Header (64px)                    │
├──────────┬──────────────────────────────────────┤
│          │                                      │
│ Sidebar  │      Check-In Section               │
│ (280px)  │   ┌──────────────────────┐          │
│          │   │   Camera View/Scanner │          │
│   Check- │   │                      │          │
│   ins     │   │   [QR Scanner Area]  │          │
│ (active) │   │                      │          │
│          │   └──────────────────────┘          │
│          │                                      │
│          │      Attendance Section              │
│          │   ┌──────────────────────┐          │
│          │   │   Statistics Cards   │          │
│          │   └──────────────────────┘          │
│          │                                      │
│          │   ┌──────────────────────┐          │
│          │   │   Attendee List      │          │
│          │   └──────────────────────┘          │
└──────────┴──────────────────────────────────────┘
```

### Check-In Section
**Container**: White card with padding 24px

#### Camera Controls
```css
.camera-container {
  background: #F3F4F6;
  border-radius: 12px;
  padding: 32px;
  text-align: center;
  min-height: 300px;
}
```

**Elements**:
1. **Camera Toggle Button**:
   ```css
   .camera-btn {
     background: linear-gradient(135deg, #14B8A6, #0D9488);
     color: white;
     padding: 12px 32px;
     border-radius: 8px;
     font-size: 16px;
     font-weight: 600;
     display: inline-flex;
     align-items: center;
     gap: 8px;
   }
   ```
   - Icon: Camera icon (24px)
   - Text: "Open Camera"
   - Click action: Activates device camera

2. **Alternative Input**:
   ```css
   .barcode-btn {
     background: white;
     border: 2px solid #D1D5DB;
     color: #4B5563;
     padding: 10px 24px;
     border-radius: 8px;
     margin-top: 16px;
   }
   ```
   - Icon: Barcode icon
   - Text: "Scan Barcode (Unregistered)"
   - Purpose: Manual entry for walk-ins

#### Active Scanner View
When camera is active:
```css
.scanner-view {
  position: relative;
  width: 100%;
  max-width: 400px;
  margin: 0 auto;
}
.scanner-frame {
  border: 3px solid #10B981;
  border-radius: 12px;
  aspect-ratio: 1;
  position: relative;
}
.scan-line {
  position: absolute;
  width: 100%;
  height: 2px;
  background: #10B981;
  animation: scan 2s linear infinite;
}
```

### Workshop Attendance Section
**Heading**: "Workshop Attendance"
**Subtitle**: "Confirm attendance for the workshop"

#### Attendance Statistics
**Layout**: 3 cards horizontal
**Gap**: 16px

```css
.stat-card {
  background: white;
  border: 1px solid #E5E7EB;
  border-radius: 12px;
  padding: 20px;
  text-align: center;
  flex: 1;
}
.stat-label {
  font-size: 14px;
  color: #6B7280;
  margin-bottom: 8px;
}
.stat-value {
  font-size: 36px;
  font-weight: 700;
  color: #111827;
}
```

**Metrics**:
1. **Registered**: Total registered participants
2. **Attendees**: Currently checked in
3. **Unregistered**: Walk-in participants

#### Attendance List Table
```css
.attendance-table {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  margin-top: 24px;
}
.table-header {
  background: #F9FAFB;
  padding: 12px 24px;
  border-bottom: 1px solid #E5E7EB;
}
```

**Columns**:
1. **Visitor Name** (40% width)
2. **Attendance Time/Date** (60% width)

**Row Structure**:
```css
.table-row {
  padding: 16px 24px;
  border-bottom: 1px solid #F3F4F6;
  display: flex;
  align-items: center;
}
.visitor-name {
  font-size: 14px;
  font-weight: 500;
  color: #111827;
}
.check-in-time {
  font-size: 14px;
  color: #6B7280;
}
```

### QR Scanning Process
1. **Pre-scan**: Camera preview active
2. **Scanning**: QR code detected, validation in progress
3. **Success**: Green checkmark animation, attendee added
4. **Error**: Red X animation with error message
5. **Duplicate**: Yellow warning "Already checked in"

### Success Feedback
```css
.success-modal {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background: white;
  border-radius: 16px;
  padding: 32px;
  box-shadow: 0 20px 25px rgba(0,0,0,0.15);
  animation: slideUp 0.3s ease;
}
.success-icon {
  width: 64px;
  height: 64px;
  background: #10B981;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 16px;
}
```

### User Flows
1. **Quick Scan**: Open scanner → Point at QR → Auto-confirm → Continue scanning
2. **Manual Entry**: Unregistered → Enter details → Confirm → Add to list
3. **Bulk Check-in**: Keep scanner active → Continuous scanning mode
4. **Review Attendance**: Close scanner → View list → Export if needed

---

## 👤 PAGE 4: MY PROFILE
**File**: `supervisor/Supervisor_Workshops-2.png`
**Route**: `/workshop-supervisor/profile`
**Purpose**: Manage personal information and account settings

### Page Header
```css
.profile-header {
  text-align: center;
  margin-bottom: 40px;
}
```

**Title**: "My Profile"

### Profile Avatar Section
```css
.avatar-section {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-bottom: 32px;
}
.avatar-container {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  background: linear-gradient(135deg, #FEE2E2, #FECACA);
  padding: 4px;
  margin-bottom: 16px;
}
.avatar-image {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  object-fit: cover;
}
.user-name {
  font-size: 24px;
  font-weight: 600;
  color: #111827;
  margin-bottom: 4px;
}
.user-email {
  font-size: 14px;
  color: #6B7280;
}
```

### Form Fields Grid
**Layout**: 2 columns
**Gap**: 20px horizontal, 24px vertical

#### Field Structure
```css
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
.field-input:focus {
  border-color: #14B8A6;
  outline: none;
  box-shadow: 0 0 0 3px rgba(20,184,166,0.1);
}
```

### Profile Fields

#### Row 1
- **Name**: Full name (required)
- **Email**: Email address (required, validated)

#### Row 2
- **Date of Birth**: Date picker
- **Phone Number**: With country code

#### Row 3
- **National ID**: 10 digits (validated)

#### Row 4
- **Password**: Password field with strength indicator
- **Confirm Password**: Must match password

#### Row 5
- **Occupation**: Radio selection (Student/Employee)
- **Job Title**: Text input

### Occupation Selection
```css
.radio-group {
  display: flex;
  gap: 24px;
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
  accent-color: #14B8A6;
}
.radio-label {
  font-size: 14px;
  color: #374151;
}
```

### Save Button
```css
.save-btn {
  background: linear-gradient(135deg, #10B981, #059669);
  color: white;
  padding: 12px 32px;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 600;
  float: right;
  margin-top: 32px;
}
.save-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(16,185,129,0.3);
}
```

### Validation Rules
1. **Email**: Valid email format
2. **Phone**: Valid phone with country code
3. **National ID**: Exactly 10 digits
4. **Password**: Min 8 chars, 1 uppercase, 1 number
5. **Job Title**: Required if Occupation is "Employee"

### User Flows
1. **View Profile**: Click profile icon → Profile page
2. **Edit Information**: Modify fields → Save Changes
3. **Change Password**: Enter new password → Confirm → Save
4. **Update Avatar**: Click avatar → Upload new image

---

## 🔄 CRITICAL USER FLOWS

### FLOW 1: Workshop Check-in Process
1. **Select Workshop**:
   - Dashboard → Workshops
   - Find assigned workshop
   - Click "Check Attendance"

2. **Open Scanner**:
   - Check-in page loads
   - Click "Open Camera"
   - Grant camera permission

3. **Scan QR Codes**:
   - Point camera at participant QR
   - Auto-detection and validation
   - Success confirmation appears
   - Name added to attendance list

4. **Handle Edge Cases**:
   - Duplicate scan: Show warning
   - Invalid QR: Show error
   - Unregistered: Use manual entry

5. **Complete Session**:
   - Close scanner
   - Review attendance list
   - Export report

### FLOW 2: Manual Registration (Walk-ins)
1. **Access Manual Entry**:
   - Click "Scan Barcode (Unregistered)"
   - Manual form opens

2. **Enter Details**:
   - Full name
   - Email
   - Phone number
   - National ID (optional)

3. **Confirm Registration**:
   - Validate information
   - Add to attendance
   - Generate temporary badge

### FLOW 3: Attendance Reporting
1. **Access Reports**:
   - Workshops → Select completed workshop
   - Click "View Report"

2. **Review Statistics**:
   - Total registered vs attended
   - Attendance percentage
   - Time distribution

3. **Export Data**:
   - Choose format (PDF/Excel)
   - Include timestamps
   - Download file

### FLOW 4: Real-time Monitoring
1. **During Workshop**:
   - Keep check-in page open
   - Monitor live statistics
   - See real-time updates

2. **Capacity Alerts**:
   - 80% capacity: Yellow warning
   - 100% capacity: Red alert
   - Notify admin if needed

---

## 📱 RESPONSIVE BEHAVIOR

### Mobile (< 768px)
- **Scanner**: Full screen mode
- **Statistics**: Vertical stack
- **Attendance List**: Simplified view
- **Profile Form**: Single column

### Tablet (768px - 1024px)
- **Scanner**: 70% width centered
- **Statistics**: 2 cards per row
- **Sidebar**: Collapsible

### Desktop (> 1024px)
- **Full Layout**: As designed
- **Scanner**: Max 500px width
- **Keyboard Shortcuts**: 
  - Space: Toggle scanner
  - Esc: Close scanner
  - Ctrl+E: Export report

---

## 🎨 COMPONENT SPECIFICATIONS

### QR Scanner Component
```javascript
{
  camera: {
    active: false,
    device: 'rear',
    resolution: '1920x1080'
  },
  scanning: {
    continuous: true,
    delay: 1000, // ms between scans
    format: 'QR_CODE'
  },
  feedback: {
    sound: true,
    vibration: true,
    visual: 'checkmark'
  }
}
```

### Attendance Table Component
```javascript
{
  columns: [
    { key: 'name', label: 'Visitor Name', width: '40%' },
    { key: 'checkIn', label: 'Attendance Time/Date', width: '60%' }
  ],
  features: {
    search: true,
    sort: true,
    export: ['pdf', 'excel'],
    pagination: {
      perPage: 10,
      showInfo: true
    }
  }
}
```

### Statistics Card Component
```javascript
{
  variants: {
    registered: { color: '#3B82F6', icon: 'users' },
    attended: { color: '#10B981', icon: 'check-circle' },
    unregistered: { color: '#F59E0B', icon: 'user-plus' }
  },
  animation: {
    countUp: true,
    duration: 1000
  }
}
```

---

## 🔒 PERMISSIONS & SECURITY

### Page Access Control
```javascript
const permissions = {
  'workshop-supervisor': {
    canView: ['dashboard', 'workshops', 'check-in', 'profile'],
    canEdit: ['profile'],
    canCreate: ['attendance_record'],
    canDelete: [],
    restrictions: {
      workshops: 'assigned_only',
      reports: 'own_workshops_only'
    }
  }
}
```

### Data Visibility
- **Workshops**: Only assigned workshops
- **Attendees**: Only for managed workshops
- **Reports**: Only for completed workshops
- **Profile**: Own profile only

### Security Features
1. **QR Validation**: Verify workshop registration
2. **Duplicate Prevention**: One check-in per participant
3. **Time Validation**: Only during workshop hours
4. **Audit Trail**: Log all check-ins with timestamp

---

## 📊 METRICS & ANALYTICS

### Key Performance Indicators
1. **Check-in Speed**: Avg time per scan
2. **Attendance Rate**: Attended vs registered
3. **Peak Times**: Busiest check-in periods
4. **Walk-in Rate**: Unregistered participants

### Dashboard Widgets
1. **Today's Workshops**: Count and status
2. **Total Checked In**: Current day total
3. **Upcoming Workshops**: Next 7 days
4. **Recent Activity**: Last 5 check-ins

---

## 🌍 LOCALIZATION

### Arabic Translations
```javascript
const ar_translations = {
  'Workshop Supervisor': 'مشرف ورشة',
  'Check-in': 'تسجيل الحضور',
  'Open Camera': 'فتح الكاميرا',
  'Scan QR Code': 'مسح رمز QR',
  'Attendance': 'الحضور',
  'Registered': 'مسجل',
  'Save Changes': 'حفظ التغييرات'
}
```

### RTL Adjustments
- Scanner interface remains LTR
- Statistics cards flip alignment
- Table columns maintain order
- Form labels right-aligned

---

## ⚡ PERFORMANCE REQUIREMENTS

### Scanner Performance
- **Camera Init**: < 2 seconds
- **QR Detection**: < 500ms
- **Validation**: < 1 second
- **List Update**: Real-time

### Page Load Times
- **Dashboard**: < 1.5 seconds
- **Workshops List**: < 1 second
- **Scanner Page**: < 2 seconds
- **Profile**: < 1 second

### Optimization
1. Lazy load attendance lists
2. Cache workshop data
3. Compress camera stream
4. Debounce scanner input
5. Virtual scroll for long lists

---

This completes the Workshop Supervisor role documentation with all pages, components, and workflows detailed for implementation.