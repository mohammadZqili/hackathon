# Workshop Supervisor Role - Pages Documentation

## Overview
The Workshop Supervisor role is designed for individuals responsible for managing workshop attendance, conducting check-ins, and overseeing workshop operations during events. Workshop Supervisors have specialized tools for attendance tracking, barcode scanning, and participant management. Based on the Figma analysis, Workshop Supervisors have access to 4 main sections: Dashboard, All Workshops, My Workshops, and Check-Ins.

---

## 1. Dashboard/All Workshops Page
**Route:** `/workshop-supervisor/dashboard`  
**Purpose:** Overview of all workshops available in the system

### Layout Components:

**Header:**
- **Logo:** "RumanHack" branding (16px, Ruman One Bold)
- **Search Bar:** Global search functionality
- **User Profile:**
  - "Workshops" role indicator (14px, Ruman One Regular)
  - User avatar and dropdown menu
- **Theme Controls:**
  - Language Switcher: AR/EN toggle
  - Dark Mode Toggle: Theme switcher
  - Notifications: Updates and alerts

**Sidebar Navigation:**
- **Dashboard** (14px, Ruman One Regular)
- **All Workshops** (14px, Ruman One Regular)
- **My Workshops** (14px, Ruman One Regular)
- **Check-Ins** (14px, Ruman One Regular)

**Main Content Area:**
- **Page Title:** "Upcoming Workshops" (32px, Space Grotesk Bold)
- **Description:** "Explore our upcoming workshops designed to enhance your skills and knowledge in various fields. Register now to secure your spot!" (14px, Space Grotesk Regular)

**Workshop Cards with Check-In Controls:**

1. **Workshop Card 1:**
   - **Title:** "Mastering Data Analysis with Python" (16px, Space Grotesk Bold)
   - **Details:** "Speaker: Dr. Evelyn Reed | Sponsor: Data Insights Inc. | Date: July 15, 2024 | Time: 10:00 AM - 12:00 PM" (14px, Space Grotesk Regular)
   - **Description:** "Learn to analyze complex datasets using Python, covering data manipulation, visualization, and statistical analysis." (14px, Space Grotesk Regular)
   - **Action:** "Check-In" button (14px, Ruman One Medium)

2. **Workshop Card 2:**
   - **Title:** "Advanced Web Development with React" (16px, Space Grotesk Bold)
   - **Details:** "Speaker: Mr. Ethan Carter | Sponsor: WebDev Solutions | Date: July 22, 2024 | Time: 2:00 PM - 4:00 PM" (14px, Space Grotesk Regular)
   - **Description:** "Dive deep into React, building dynamic and responsive web applications with state management and component architecture." (14px, Space Grotesk Regular)
   - **Action:** "Check-In" button (14px, Ruman One Medium)

3. **Workshop Card 3:**
   - **Title:** "Introduction to Machine Learning" (16px, Space Grotesk Bold)
   - **Details:** "Speaker: Ms. Olivia Bennett | Sponsor: AI Innovations | Date: July 29, 2024 | Time: 11:00 AM - 1:00 PM" (14px, Space Grotesk Regular)
   - **Description:** "Get started with machine learning, covering fundamental concepts, algorithms, and practical applications using popular libraries." (14px, Space Grotesk Regular)
   - **Action:** "Check-In" button (14px, Ruman One Medium)

---

## 2. Check-Ins Page
**Route:** `/workshop-supervisor/check-ins`  
**Purpose:** Active workshop attendance management with real-time check-in capabilities

### Layout Components:

**Header:** Same as dashboard

**Sidebar Navigation:** "Check-Ins" highlighted as active (bold styling)

**Main Content Layout (Split View):**

### Left Panel - Check-In Controls:
**Check-In Section:** (22px, Space Grotesk Bold)

**Camera Controls:**
- **Open Camera Button:** "Open Camera" (14px, Space Grotesk Bold)
  - Activates device camera for barcode scanning
  - Real-time barcode detection
  - Auto-focus and lighting controls

**Manual Entry Options:**
- **Scan Barcode (Unregistered) Button:** "Scan Barcode (Unregistered)" (14px, Space Grotesk Bold)
  - For attendees without prior registration
  - Manual data entry form
  - Instant registration and check-in

### Right Panel - Workshop Overview:

**Workshop Info:**
- **Title:** "Workshop Attendance" (32px, Space Grotesk Bold)
- **Description:** "Confirm attendance for the workshop" (14px, Space Grotesk Regular)

**Attendance Overview:** (22px, Space Grotesk Bold)

**Statistics Cards:**
1. **Registered Attendees:**
   - **Label:** "Registered" (16px, Space Grotesk Medium)
   - **Count:** "150" (24px, Space Grotesk Bold)

2. **Current Attendees:**
   - **Label:** "Attendees" (16px, Space Grotesk Medium)
   - **Count:** "120" (24px, Space Grotesk Bold)

3. **Unregistered Walk-ins:**
   - **Label:** "Unregistered" (16px, Space Grotesk Medium)
   - **Count:** "30" (24px, Space Grotesk Bold)

**Attendance Log Table:**
- **Table Headers:**
  - **Visitor Name** (14px, Space Grotesk Medium)
  - **Attendance Time/Date** (14px, Space Grotesk Medium)

- **Recent Check-ins:**
  1. Liam Harper | 10:00 AM, July 26, 2024 (14px, Space Grotesk Regular)
  2. Unregistered | 10:05 AM, July 26, 2024 (16px, Space Grotesk Medium)
  3. Noah Carter | 10:10 AM, July 26, 2024 (14px, Space Grotesk Regular)
  4. Ava Mitchell | 10:15 AM, July 26, 2024 (14px, Space Grotesk Regular)
  5. Ethan Foster | 10:20 AM, July 26, 2024 (14px, Space Grotesk Regular)
  6. Isabella Hayes | 10:25 AM, July 26, 2024 (14px, Space Grotesk Regular)
  7. Mason Reed | 10:30 AM, July 26, 2024 (14px, Space Grotesk Regular)
  8. Sophia Coleman | 10:35 AM, July 26, 2024 (14px, Space Grotesk Regular)
  9. Logan Price | 10:40 AM, July 26, 2024 (14px, Space Grotesk Regular)
  10. Mia Hughes | 10:45 AM, July 26, 2024 (14px, Space Grotesk Regular)

### Real-Time Features:
- **Live Updates:** Attendance list updates automatically
- **Sound Notifications:** Audio confirmation for successful scans
- **Visual Feedback:** Color-coded status indicators
- **Error Handling:** Clear messages for scan failures
- **Backup Methods:** Manual entry when camera fails

---

## 3. My Workshops Page
**Route:** `/workshop-supervisor/my-workshops`  
**Purpose:** View workshops assigned to this supervisor with management tools

### Layout Components:

**Header:** Same as other pages

**Sidebar Navigation:** "My Workshops" highlighted as active

**Main Content Area:**
- Similar workshop card layout as dashboard
- Only shows workshops assigned to current supervisor
- Additional management options per workshop:
  - View attendee list
  - Export attendance report
  - Send notifications to registrants
  - Update workshop details (if permitted)

### Workshop Management Features:
- **Pre-Event:** View registration list, send reminders
- **During Event:** Real-time attendance tracking
- **Post-Event:** Generate reports, export data

---

## 4. Profile Management Page
**Route:** `/workshop-supervisor/profile`  
**Purpose:** Supervisor account management and settings

### Layout Components:

**Main Content Area:**
- **Page Title:** "My Profile" (32px, Space Grotesk Bold)

**Profile Information:**
- **Name:** "Sophia Clark" (22px, Space Grotesk Bold)
- **Email:** "sophia.clark@email.com" (16px, Space Grotesk Regular)
- **Profile Picture:** User avatar with upload option

**Editable Form Sections:**

1. **Basic Information:**
   - **Name:** Text input (16px, Space Grotesk Medium)
   - **Email:** Email input (16px, Space Grotesk Medium)
   - **Date of Birth:** Date picker (16px, Space Grotesk Medium)
   - **Phone Number:** Phone input (16px, Space Grotesk Medium)
   - **National ID:** Text input (16px, Space Grotesk Medium)

2. **Security Settings:**
   - **Password:** Password input (16px, Space Grotesk Medium)
   - **Confirm Password:** Password confirmation (16px, Space Grotesk Medium)

3. **Professional Information:**
   - **Occupation:** Radio buttons (16px, Space Grotesk Medium)
     - Student (9px, Space Grotesk Medium)
     - Employee (9px, Space Grotesk Medium)
   - **Job Title:** Text input (16px, Space Grotesk Medium)

4. **Workshop Preferences:**
   - **Notification Settings:** Email/SMS preferences
   - **Availability:** Time slots and dates
   - **Specializations:** Areas of expertise

5. **Save Changes Button:** (14px, Space Grotesk Bold)

---

## Check-In Process & Workflows

### Standard Check-In Flow:
1. **Workshop Start:** Supervisor opens Check-Ins page
2. **Camera Activation:** Click "Open Camera" button
3. **Barcode Scanning:** Point camera at attendee's QR code
4. **Automatic Processing:** System validates and records attendance
5. **Confirmation:** Visual/audio feedback for successful check-in
6. **Real-time Update:** Attendance count and log update immediately

### Unregistered Attendee Flow:
1. **Manual Entry:** Click "Scan Barcode (Unregistered)" button
2. **Data Collection:** Quick form for name, email, phone
3. **Instant Registration:** Create temporary workshop registration
4. **Immediate Check-in:** Record attendance simultaneously
5. **Follow-up:** Optional: Send workshop materials via email

### Error Handling:
1. **Camera Issues:** Fallback to manual barcode entry
2. **Network Problems:** Queue check-ins for when connectivity returns
3. **Invalid Codes:** Clear error message with resolution steps
4. **Duplicate Scans:** Prevent double check-ins with confirmation dialog

---

## Technical Features

### Barcode/QR Code System:
- **Format:** Industry-standard QR codes
- **Content:** Encrypted attendee information
- **Validation:** Real-time verification against registration database
- **Security:** Time-stamped codes with expiration
- **Backup:** Manual validation using name/ID lookup

### Camera Integration:
- **Auto-focus:** Automatic focusing on QR codes
- **Multiple Formats:** Support for various barcode types
- **Lighting Adaptation:** Works in various lighting conditions
- **Performance:** Fast scanning with minimal delay
- **Device Compatibility:** Works on mobile and tablet devices

### Real-Time Updates:
- **WebSocket Connection:** Live data synchronization
- **Offline Capability:** Queue actions when offline
- **Data Sync:** Automatic sync when connectivity restored
- **Multi-Supervisor:** Support multiple supervisors per workshop
- **Conflict Resolution:** Handle simultaneous check-ins gracefully

---

## Workshop Supervisor Permissions

### What Workshop Supervisors CAN do:
- **Attendance Management:** Check-in registered and unregistered attendees
- **Real-time Tracking:** View live attendance statistics
- **Report Generation:** Export attendance data and reports
- **Camera Access:** Use device camera for barcode scanning
- **Manual Override:** Manually check-in attendees when needed
- **Workshop Info:** View detailed workshop information
- **Attendee Communication:** Send notifications to registrants (if authorized)

### What Workshop Supervisors CANNOT do:
- **Workshop Creation:** Cannot create or modify workshop content
- **User Management:** Cannot manage user accounts or permissions
- **System Administration:** No access to admin functions
- **Hackathon Features:** Cannot access team or idea management
- **Financial Data:** Cannot view payment or financial information
- **Other Workshops:** Cannot manage workshops not assigned to them

### Security Measures:
- **Role-based Access:** Limited to workshop supervision functions
- **Workshop Assignment:** Only access to assigned workshops
- **Data Protection:** Encrypted transmission of attendee data
- **Audit Trail:** All check-in actions logged for review
- **Session Management:** Automatic logout for security

### Mobile Optimization:
- **Touch Interface:** Large, touch-friendly buttons
- **Camera Performance:** Optimized for mobile camera scanning
- **Network Efficiency:** Minimal data usage for check-ins
- **Offline Mode:** Essential functions work without internet
- **Battery Optimization:** Efficient use of device resources

---

## Common UI Patterns

### Typography Hierarchy:
- **Page Titles:** 32px, Space Grotesk Bold
- **Section Headers:** 22px, Space Grotesk Bold
- **Subsection Headers:** 16px, Space Grotesk Medium
- **Body Text:** 14px, Space Grotesk Regular
- **Navigation:** 14px, Ruman One Regular
- **Buttons:** 14px, appropriate font weight

### Color Coding:
- **Success Green:** Successful check-ins, active status
- **Warning Orange:** Unregistered attendees, attention needed
- **Error Red:** Failed scans, system errors
- **Info Blue:** Information messages, help text
- **Primary Brand:** Main actions, navigation highlights

### Interactive Elements:
- **Check-in Buttons:** Prominent, easily accessible
- **Camera Controls:** Large, clear activation buttons
- **Statistics Cards:** Visual hierarchy with numbers emphasized
- **Attendance Log:** Clean table with hover effects
- **Error Messages:** Clear, actionable feedback

### Accessibility:
- **High Contrast:** Clear visibility in various lighting
- **Screen Reader Support:** Proper labeling and descriptions
- **Keyboard Navigation:** Full functionality without mouse
- **Text Size:** Scalable fonts for different needs
- **Sound Feedback:** Audio cues for successful actions