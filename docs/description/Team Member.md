# Team Member Role - Pages Documentation

## Overview
The Team Member role is designed for participants who join an existing team to collaborate on hackathon ideas. Team Members have viewing access to team activities, can participate in discussions, contribute to idea development, and register for workshops. Based on the Figma analysis, Team Members have access to 6 main sections: Dashboard, Our Idea, My Team, Tracks, Workshops, and News.

---

## 1. Team Member Dashboard
**Route:** `/team-member/dashboard`  
**Purpose:** Central hub providing overview of team activities, idea status, and quick access to key features

### Layout Components:

**Header**
- **Logo:** "RumanHack" branding (left aligned)
- **Search Bar:** Global search functionality with search icon
- **User Profile:**
  - "Team Member" role indicator
  - User avatar and dropdown menu
- **Theme Controls:**
  - Language Switcher: AR/EN toggle
  - Dark Mode Toggle: Theme switcher
  - Notifications: Bell icon with updates

**Sidebar Navigation**
- **Dashboard** (active with bold styling)
- **Our Idea** (view team's submitted idea)
- **My Team** (view team members)
- **Tracks** (browse available tracks)
- **Workshops** (register for events)
- **News** (hackathon updates)

**Main Content Area**
- **Page Title:** "Dashboard" (32px, Space Grotesk Bold)
- **Summary Section:** (22px heading)
  - **My Team Card:**
    - Title: "My Team" (16px bold)
    - Description: "Manage your team members and their roles"
    - Action: "View Team" button
  - **Idea Status Card:**
    - Title: "Idea Status" (16px bold)  
    - Description: "Track the progress of your ideas and initiatives"
    - Action: "View Ideas" button
  - **Upcoming Workshops Card:**
    - Title: "Upcoming Workshops" (16px bold)
    - Description: "See the schedule for upcoming workshops and training sessions"
    - Action: "View Workshops" button

### Color Scheme:
- Background: Clean white/dark theme compatible
- Cards: Light background with subtle borders
- Primary buttons: Brand color styling
- Text: Dark gray for readability

---

## 2. Our Idea Page
**Route:** `/team-member/our-idea`  
**Purpose:** View and engage with the team's submitted idea, including supervisor feedback and progress tracking

### Layout Components:

**Header:** Same as Dashboard

**Sidebar Navigation:** "Our Idea" highlighted as active

**Main Content Area:**
- **Idea Title:** "Idea: Streamlined Onboarding Process" (32px, Space Grotesk Bold)
- **Submitter:** "Submitted by Saleh" (14px, Space Grotesk Regular)

**Tab Navigation:**
- **Overview** (active tab)
- **Comments** (discussion thread)
- **Instructions** (supervisor requirements)

**Idea Details Section:** (22px heading)
- **Description Block:**
  - Label: "Description" (14px)
  - Content: Detailed idea description in paragraph form
- **Problem Statement:**
  - Label: "Problem" (14px)  
  - Content: Description of the problem being solved
- **Solution Approach:**
  - Label: "Solution" (14px)
  - Content: Proposed solution methodology

**Current Stage Section:** (22px heading)
- **Implementation Progress:**
  - Label: "Implementation" (16px Medium)
  - Progress bar: Visual indicator (60% complete)
  - Status text: "60% Complete"

**Next Steps Section:** (22px heading)
- **Task List:**
  - Task 1: "Complete training module on project management"
    - Due date: "July 15, 2024"
  - Task 2: "Meet with mentor for feedback session"
    - Due date: "July 22, 2024"  
  - Task 3: "Present progress report to team"
    - Due date: "July 29, 2024"

**Related Documents Section:** (22px heading)
- **Document List:**
  - "Onboarding Checklist" (with icon)
  - "Training Module Outline" (with icon)
  - "Feedback Survey Template" (with icon)

**Supervisor Feedback Section:**
- **Feedback Card:**
  - Supervisor: "Dr. Evelyn Reed"
  - Timestamp: "2 days ago"
  - Feedback content: Detailed supervisor comments and instructions
  - Feedback status indicator

**Comments Section:**
- **Comment Thread:**
  - User avatars and names
  - Comment content with rich text formatting
  - Reaction buttons (emoji reactions)
  - Reply functionality
  - "What are your thoughts?" input field
  - Reply/Cancel buttons

---

## 3. My Team Page
**Route:** `/team-member/my-team`  
**Purpose:** View team information, member details, and team structure

### Layout Components:

**Header:** Same as Dashboard

**Sidebar Navigation:** "My Team" highlighted as active

**Main Content Area:**
- **Team Title:** "Team Alpha" (32px, Ruman One Bold)
- **Team Lead:** "Led by Saleh" (14px, Space Grotesk Regular)

**Members Section:** (18px heading)
- **Members Table:**
  - **Table Headers:**
    - Name (14px Medium)
    - E-mail (14px Medium)  
    - #Mobile No (14px Medium)
    - Status (14px Medium)
  - **Member Rows:**
    - Member 1: Ethan Carter, saleh@ruman.sa, 0565123335, Active (green badge)
    - Member 2: Ethan Carter, saleh@ruman.sa, 0565123335, Pending (yellow badge)
    - Additional members with similar format
  
**Table Styling:**
- Clean borders between rows
- Alternating row colors for readability
- Status badges with color coding:
  - Active: Green background
  - Pending: Yellow/orange background

**Permissions:**
- Team Members can view all team information
- No edit/delete capabilities (view-only)
- Cannot invite or remove members

---

## 4. Tracks Page
**Route:** `/team-member/tracks`  
**Purpose:** Browse available hackathon tracks and their details

### Layout Components:

**Header:** Same as Dashboard

**Sidebar Navigation:** "Tracks" highlighted as active

**Main Content Area:**
- **Page Title:** "Tracks" (32px, Space Grotesk Bold)
- **Description:** "Manage tracks for the current hackathon edition" (14px, Space Grotesk Regular)

**Tracks Table:**
- **Table Headers:**
  - Track Name (14px Medium)
  - Description (14px Medium)
  - Assigned Supervisor (14px Medium)

- **Track Entries:**
  1. **AI Innovation**
     - Description: "Develop AI-powered solutions for real-world problems"
     - Supervisor: "Dr. Anya Sharma"
  
  2. **Web Development**  
     - Description: "Build responsive and scalable web applications"
     - Supervisor: "Mr. Ben Carter"
  
  3. **Mobile Apps**
     - Description: "Create innovative mobile applications for iOS and Android"
     - Supervisor: "Ms. Chloe Davis"
  
  4. **Data Science**
     - Description: "Analyze data and derive insights to solve complex challenges"
     - Supervisor: "Dr. Ethan Foster"
  
  5. **Hardware Hacking**
     - Description: "Design and build hardware prototypes"
     - Supervisor: "Mr. Finn Gallagher"

**Table Features:**
- Clean, organized layout
- Hover effects on rows
- Read-only access for Team Members
- Clear typography hierarchy

---

## 5. Workshops Page  
**Route:** `/team-member/workshops`
**Purpose:** Browse and register for available workshops and training sessions

### Layout Components:

**Header:** Same as Dashboard

**Sidebar Navigation:** "Workshops" highlighted as active

**Main Content Area:**
- **Page Title:** "Upcoming Workshops" (32px, Space Grotesk Bold)
- **Description:** "Explore our upcoming workshops designed to enhance your skills and knowledge in various fields. Register now to secure your spot!" (14px, Space Grotesk Regular)

**Workshop Cards:**

1. **Workshop Card 1:**
   - **Title:** "Mastering Data Analysis with Python" (16px Bold)
   - **Details:** "Speaker: Dr. Evelyn Reed | Sponsor: Data Insights Inc. | Date: July 15, 2024 | Time: 10:00 AM - 12:00 PM"
   - **Description:** "Learn to analyze complex datasets using Python, covering data manipulation, visualization, and statistical analysis"
   - **Action:** "Register" button (14px Medium)

2. **Workshop Card 2:**
   - **Title:** "Advanced Web Development with React" (16px Bold)
   - **Details:** "Speaker: Mr. Ethan Carter | Sponsor: WebDev Solutions | Date: July 22, 2024 | Time: 2:00 PM - 4:00 PM"  
   - **Description:** "Dive deep into React, building dynamic and responsive web applications with state management and component architecture"
   - **Action:** "Register" button (14px Medium)

3. **Workshop Card 3:**
   - **Title:** "Introduction to Machine Learning" (16px Bold)
   - **Details:** "Speaker: Ms. Olivia Bennett | Sponsor: AI Innovations | Date: July 29, 2024 | Time: 11:00 AM - 1:00 PM"
   - **Description:** "Get started with machine learning, covering fundamental concepts, algorithms, and practical applications using popular libraries"
   - **Action:** "Register" button (14px Medium)

**Card Design:**
- Clean white background with subtle shadows
- Well-organized information hierarchy
- Clear call-to-action buttons
- Consistent spacing and typography

**Registration Process:**
- Click "Register" opens registration modal
- Form fields: Name, Email, Phone, National ID
- Confirmation email with QR code for attendance
- Success message after registration

---

## 6. News Page
**Route:** `/team-member/news`
**Purpose:** View hackathon updates, announcements, and news

### Layout Components:

**Header:** Same as Dashboard

**Sidebar Navigation:** "News" highlighted as active

**Main Content Area:**
- **Page Title:** "News & Updates" (32px, Space Grotesk Bold)
- **Description:** "Stay updated with the latest hackathon news, announcements, and important updates"

**News Feed:**
- **News Article Cards:**
  - Article title (20px Bold)
  - Publication date
  - Article preview/excerpt
  - Featured image (if applicable)
  - "Read More" link
- **Chronological ordering** (newest first)
- **Pagination** for older articles

**News Categories:**
- Hackathon updates
- Workshop announcements  
- Winner announcements
- General updates
- Important notices

---

## Common UI Elements & Patterns

### Typography:
- **Primary Font:** Ruman One (for headings, navigation)
- **Secondary Font:** Space Grotesk (for body text, descriptions)
- **Accent Font:** Inter (for UI elements, comments)

### Color Palette:
- **Primary Brand Color:** Used for buttons, active states
- **Success Green:** For active status badges
- **Warning Yellow:** For pending status badges  
- **Neutral Grays:** For text hierarchy and backgrounds
- **Dark Mode Support:** All components support theme switching

### Interactive Elements:
- **Buttons:** Consistent styling with hover states
- **Cards:** Hover effects with subtle shadows
- **Tables:** Row hover highlighting
- **Form Elements:** Clean, accessible inputs
- **Navigation:** Active state highlighting

### Responsive Design:
- **Mobile-first approach**
- **Flexible grid system**  
- **Collapsible sidebar** on mobile
- **Touch-friendly interface elements**
- **Optimized content layout** for different screen sizes

### Accessibility Features:
- **Keyboard navigation support**
- **Screen reader compatibility**
- **High contrast mode support**
- **Clear focus indicators**
- **Semantic HTML structure**

---

## Team Member Permissions & Limitations:

### What Team Members CAN do:
- View all team information and activities
- Participate in idea discussions (comments)
- Register for workshops and events
- Browse tracks and hackathon information  
- View supervisor feedback and instructions
- Access team documents and resources
- Receive notifications about team updates

### What Team Members CANNOT do:
- Edit or modify team ideas
- Invite or remove team members
- Submit new ideas independently
- Access admin or supervisor functions
- Modify team settings or configuration
- Delete comments or content from others

### User Experience Considerations:
- **Clear Role Identification:** UI clearly indicates Team Member role
- **Intuitive Navigation:** Easy access to relevant features
- **Real-time Updates:** Live notifications for important changes
- **Collaborative Features:** Commenting and discussion tools
- **Mobile Optimization:** Full functionality on mobile devices
- **Help & Support:** Context-sensitive help and documentation