Supervisor Role - Pages Documentation
Overview
The Supervisor role (Track Supervisor) is responsible for evaluating ideas submitted to their assigned tracks, providing feedback to teams, managing workshop attendance, and overseeing track-specific activities. Based on the Figma analysis, supervisors have access to 5 main sections: Dashboard, Ideas, Tracks, Workshops, and Check-Ins.

1. Supervisor Dashboard
   Route: /supervisor/dashboard Purpose: Overview of supervisor activities and key metrics

Layout Components:
Header
Logo: "RumanHack" branding (left aligned)
Search Bar: Global search functionality
User Profile:
"Supervisor" role indicator
User avatar and dropdown menu
Language Switcher: AR/EN toggle
Dark Mode Toggle: Theme switcher
Notifications: Bell icon with unread indicators
Sidebar Navigation
Dashboard (active state with bold styling)
Ideas (evaluation management)
Tracks (assigned tracks overview)
Workshops (workshop management)
Check-Ins (attendance tracking)
Main Content Area
Key Performance Indicators (card-based layout):
Total Ideas Assigned
Pending Evaluations (with urgency indicators)
Completed Reviews This Week
Workshop Attendance Rates
Average Evaluation Score Given
Quick Actions Section:
"Review Next Idea" (primary action button)
"View Track Performance"
"Manage Workshop Attendance"
"Generate Evaluation Report"
Recent Activity Feed:
Recently submitted ideas
Evaluation deadlines approaching
Team communication requests
Workshop attendance updates
Color Scheme:
Primary:
#7C3AED (Purple - evaluation focus)
Secondary:
#059669 (Green - completion/success)
Warning:
#F59E0B (Orange - pending items)
Error:
#EF4444 (Red - overdue items)
Background:
#F8FAFC
Cards:
#FFFFFF with subtle shadow
2. Submitted Ideas Management
   Route: /supervisor/ideas Purpose: Comprehensive idea evaluation and management interface

Layout Components:
Page Header
Title: "Submitted Ideas" (Space Grotesk Bold, 32px)
Subtitle: "Browse and manage all submitted ideas from various teams."
Search Bar: "Search ideas by title, team, or track" with full-width input
Ideas Table Interface
Table Headers (Space Grotesk Medium, 14px):

Title: Idea name (sortable)
Team: Team name and leader
Submission Date: Date submitted
Track: Assigned track with badge
Status: Current evaluation status
Actions: Available operations
Sample Data Rows (Space Grotesk Regular, 14px):

Innovative Marketing Campaign
Team: Marketing Team
Date: 2024-07-26
Track: Marketing (blue badge)
Status: Pending Review (yellow badge)
Action: "View Details" button
Product Enhancement Suggestions
Team: Product Development Team
Date: 2024-07-25
Track: Product (purple badge)
Status: Approved (green badge)
Action: "View Details" button
Streamlining Operations
Team: Operations Team
Date: 2024-07-24
Track: Operations (orange badge)
Status: Rejected (red badge)
Action: "View Details" button
Customer Service Improvements
Team: Customer Support Team
Date: 2024-07-23
Track: Customer Service (teal badge)
Status: In Progress (blue badge)
Action: "View Details" button
New Sales Strategies
Team: Sales Team
Date: 2024-07-22
Track: Sales (green badge)
Status: Completed (gray badge)
Action: "View Details" button
Status Color Coding:
Pending Review: Yellow (
#F59E0B)
Approved: Green (
#10B981)
Rejected: Red (
#EF4444)
In Progress: Blue (
#3B82F6)
Completed: Gray (
#6B7280)
3. Idea Detail & Evaluation Page
   Route: /supervisor/ideas/{id} Purpose: Detailed idea view with evaluation tools and decision making

Layout Components:
Page Header
Main Title: "Idea: [Idea Name]" (Space Grotesk Bold, 32px)
Subtitle: "Submitted by [Team Name]" (Space Grotesk Regular, 14px)
Navigation Tabs: "Overview" and "Response"
Idea Details Section
Basic Information Panel:

Team Name: Display team information
Idea Leader: Team leader name (e.g., "Sarah Chen")
Submission Date/Time: "2024-07-26 14:30"
Track: Assigned track name
Hackathon Edition: "Summer 2024"
Description Section
Title: "Description" (Space Grotesk Bold, 22px)
Content: Full idea description with rich text formatting
Example text: "This campaign aims to leverage social media influencers and interactive content to increase brand awareness and engagement. The strategy includes a series of online contests, collaborative posts with influencers, and a user-generated content campaign."
Related Documents Section
Title: "Related Documents" (Space Grotesk Bold, 22px)
File List: Downloadable attachments
"Onboarding Checklist" (with file icon)
"Training Module Outline" (with file icon)
"Feedback Survey Template" (with file icon)
Decision Making Panel
Title: "Make Decision..." (Space Grotesk Bold, 22px)
Decision Options (radio buttons):
Accept (green button)
Reject (red button)
Need Edit (orange button)
Feedback Section
Feedback Text Area:
Placeholder: "Provide feedback or required changes for the idea's acceptance"
Rich text editor with formatting options
Scoring Section
Title: "Score" (Space Grotesk Bold, 22px)
Score Input: "Add Score From 100" with numerical input field
4. Tracks Management
   Route: /supervisor/tracks
   Purpose: Overview and management of assigned tracks

Layout Components:
Page Header
Title: "Tracks" (Space Grotesk Bold, 32px)
Subtitle: "Manage tracks for the current hackathon edition."
Tracks Table
Table Headers (Space Grotesk Medium, 14px):

Track Name: Name of the track
Description: Brief track description
Assigned Supervisor: Supervisor name
Track Data Rows:

AI Innovation
Description: "Develop AI-powered solutions for real-world problems."
Supervisor: "Dr. Anya Sharma"
Web Development
Description: "Build responsive and scalable web applications."
Supervisor: "Mr. Ben Carter"
Mobile Apps
Description: "Create innovative mobile applications for iOS and Android."
Supervisor: "Ms. Chloe Davis"
Data Science
Description: "Analyze data and derive insights to solve complex challenges."
Supervisor: "Dr. Ethan Foster"
Hardware Hacking
Description: "Design and build hardware prototypes."
Supervisor: "Mr. Finn Gallagher"
5. Workshops Management
   Route: /supervisor/workshops Purpose: Workshop listing, registration, and attendance management

Layout Components:
Page Header
Title: "Upcoming Workshops" (Space Grotesk Bold, 32px)
Description: "Explore our upcoming workshops designed to enhance your skills and knowledge in various fields. Register now to secure your spot!"
Workshop Cards Layout
Workshop Card Structure (vertical card layout):

Mastering Data Analysis with Python
Details: "Speaker: Dr. Evelyn Reed | Sponsor: Data Insights Inc. | Date: July 15, 2024 | Time: 10:00 AM - 12:00 PM"
Description: "Learn to analyze complex datasets using Python, covering data manipulation, visualization, and statistical analysis."
Action: "Register" button
Advanced Web Development with React
Details: "Speaker: Mr. Ethan Carter | Sponsor: WebDev Solutions | Date: July 22, 2024 | Time: 2:00 PM - 4:00 PM"
Description: "Dive deep into React, building dynamic and responsive web applications with state management and component architecture."
Action: "Register" button
Introduction to Machine Learning
Details: "Speaker: Ms. Olivia Bennett | Sponsor: AI Innovations | Date: July 29, 2024 | Time: 11:00 AM - 1:00 PM"
Description: "Get started with machine learning, covering fundamental concepts, algorithms, and practical applications using popular libraries."
Action: "Register" button
6. Workshop Attendance (Check-Ins)
   Route: /supervisor/check-ins Purpose: Workshop attendance tracking and barcode scanning

Layout Components:
Page Header
Title: "Workshop Attendance" (Space Grotesk Bold, 32px)
Subtitle: "Confirm attendance for the workshop"
Check-In Tools Section
Title: "Check-In" (Space Grotesk Bold, 22px)
Action Buttons:
"Open Camera" button (for barcode scanning)
"Scan Barcode (Unregistered)" button
Attendance Overview Section
Title: "Attendance Overview" (Space Grotesk Bold, 22px)
Statistics Cards (3-column layout):

Registered
Count: "150"
Label: "Registered" (Space Grotesk Medium, 16px)
Attendees
Count: "120"
Label: "Attendees" (Space Grotesk Medium, 16px)
Unregistered
Count: "30"
Label: "Unregistered" (Space Grotesk Medium, 16px)
Attendance Log Table
Table Headers:

Visitor Name: Attendee full name
Attendance Time/Date: Check-in timestamp
Sample Attendance Records:

Liam Harper - 10:00 AM, July 26, 2024
Olivia Bennett - 10:05 AM, July 26, 2024
Noah Carter - 10:10 AM, July 26, 2024
Ava Mitchell - 10:15 AM, July 26, 2024
Ethan Foster - 10:20 AM, July 26, 2024
Isabella Hayes - 10:25 AM, July 26, 2024
Mason Reed - 10:30 AM, July 26, 2024
Sophia Coleman - 10:35 AM, July 26, 2024
Logan Price - 10:40 AM, July 26, 2024
Mia Hughes - 10:45 AM, July 26, 2024
7. Profile Management
   Route: /supervisor/profile Purpose: Personal profile management and account settings

Layout Components:
Page Header
Title: "My Profile" (Space Grotesk Bold, 32px)
Profile Information Section
User Profile Card:

Name: "Sophia Clark" (Space Grotesk Bold, 22px)
Email: "sophia.clark@email.com" (Space Grotesk Regular, 16px)
Profile Avatar: User photo placeholder
Profile Form Fields
Form Sections (Space Grotesk Medium, 16px labels):

Name: Text input field
Email: Email input field
Date of Birth: Date picker
Phone Number: Phone input field
National ID: Text input field
Password: Password input field
Confirm Password: Password confirmation field
Occupation: Radio buttons
"Student" option
"Employee" option
Job Title: Text input field
Form Actions:

"Save Changes" button (Space Grotesk Bold, 14px)
Common UI Components & Design Specifications
Typography System
Headings: Space Grotesk Bold (32px for main titles, 22px for sections)
Body Text: Space Grotesk Regular (16px standard, 14px for tables)
Labels: Space Grotesk Medium (16px for forms, 14px for tables)
Navigation: Ruman One Bold/Regular (14px)
Navigation Structure
├── Dashboard (Home overview)
├── Ideas (Submitted ideas management)
│   ├── Ideas List
│   └── Idea Details (with evaluation tools)
├── Tracks (Assigned tracks overview)
├── Workshops (Workshop management)
│   ├── Workshop List
│   └── Workshop Registration
└── Check-Ins (Workshop attendance)
├── Barcode Scanning
└── Attendance Tracking
Status Badge System
Pending Review: Yellow background (
#FEF3C7), text (
#92400E)
Approved: Green background (
#D1FAE5), text (
#065F46)
Rejected: Red background (
#FEE2E2), text (
#991B1B)
In Progress: Blue background (
#DBEAFE), text (
#1E40AF)
Completed: Gray background (
#F3F4F6), text (
#374151)
Responsive Design Features
Desktop: Full table layouts with all columns visible
Tablet: Condensed table with essential columns
Mobile: Card-based layout stacking vertically
Touch Interactions: Larger tap targets for mobile devices
Accessibility Considerations
High Contrast: Sufficient color contrast for all text
Keyboard Navigation: Full keyboard accessibility
Screen Reader Support: Proper ARIA labels and semantic HTML
Focus Indicators: Clear focus states for interactive elements
