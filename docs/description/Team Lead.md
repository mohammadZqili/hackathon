Team Lead Role - Pages Documentation
Overview
The Team Lead role is responsible for managing their team, submitting and editing ideas, coordinating with team members, and overseeing the team's participation in the hackathon. Based on the Figma analysis, Team Leaders have access to 5 main sections: Dashboard, Our Idea, My Team, Tracks, and Workshops.

1. Team Lead Dashboard
   Route: /team-lead/dashboard
   Purpose: Central hub for team leadership activities and progress tracking
   Layout Components:
   Header

Logo: "RumanHack" branding (left aligned)
Search Bar: Global search functionality
User Profile:

"Team Leader" role indicator
User avatar and dropdown menu


Language Switcher: AR/EN toggle
Dark Mode Toggle: Theme switcher
Notifications: Bell icon with team-related updates

Sidebar Navigation

Dashboard (when active, bold styling)
Our Idea (team idea management)
My Team (team member management)
Tracks (view available tracks)
Workshops (register for workshops)

Main Content Area

Team Overview Cards (4-card layout):

Team Status & Progress
Idea Evaluation Status
Team Member Count
Upcoming Deadlines


Quick Actions Panel:

"Edit Our Idea" (primary button)
"Manage Team Members"
"View Supervisor Feedback"
"Register for Workshop"


Team Activity Feed:

Recent team member activities
Supervisor feedback updates
Deadline reminders
Workshop registrations



Color Scheme:

Primary: #2563EB (Blue - leadership focus)
Secondary: #059669 (Green - progress/success)
Warning: #F59E0B (Orange - pending items)
Error: #EF4444 (Red - urgent items)
Background: #F8FAFC
Cards: #FFFFFF with subtle shadow


2. Our Idea Management
   Route: /team-lead/idea
   Purpose: Comprehensive idea submission, editing, and progress tracking
   Layout Components:
   Page Header

Main Title: "Idea: [Idea Name]" (Space Grotesk Bold, 32px)

Example: "Idea: Streamlined Onboarding Process"


Subtitle: "Submitted by [Team Leader Name]" (Space Grotesk Regular, 14px)

Example: "Submitted by Saleh"



Content Tabs

Overview (active state with bold styling)
Comments (supervisor and team feedback)
Instructions (from supervisors)

Overview Tab Content
Idea Details Section

Title: "Idea Details" (Space Grotesk Bold, 22px)
Description Field:

Label: "Description"
Content: Full idea description text
Example: "This idea aims to improve the onboarding process for new team members, making it more efficient and engaging."



Current Stage Section

Title: "Current Stage" (Space Grotesk Bold, 22px)
Stage Indicator: "Implementation" (Space Grotesk Medium, 16px)
Progress Display: "60% Complete" with visual progress bar

Next Steps Section

Title: "Next Steps" (Space Grotesk Bold, 22px)
Task Cards (3-column layout):

Task 1:

Title: "Complete training module on project management"
Due Date: "Due: July 15, 2024"


Task 2:

Title: "Meet with mentor for feedback session"
Due Date: "Due: July 22, 2024"


Task 3:

Title: "Present progress report to team"
Due Date: "Due: July 29, 2024"





Related Documents Section

Title: "Related Documents" (Space Grotesk Bold, 22px)
File List:

"Onboarding Checklist" (downloadable)
"Training Module Outline" (downloadable)
"Feedback Survey Template" (downloadable)



Action Button

"Edit Idea" button (Ruman One Bold, 16px) - Primary action button

Comments Tab Content
Comments Feed Interface

Comment Display Format:

User avatar and name
Role indicator ("Ecosystems and Communities")
Timestamp ("6 days ago")
Comment content with full text
Reaction buttons and reply options


Sample Comments:

Author: "Michael Busch"
Content: "Dummy comment - But I must explain to you how all this mistaken idea of denouncing pleasure and praising pain was born..."
Replies: Nested comment threads
Interaction: "2 comments" counter



Reply Interface

User Profile: "Drew Koski" (current user)
Text Input: "What are you thoughts?" placeholder
Action Buttons:

"Reply" button (primary)
"Cancel" button



Instructions Tab Content
Supervisor Feedback Section

Title: "Supervisor Feedback" (Space Grotesk Bold, 22px)
Feedback Card:

Supervisor: "Dr. Evelyn Reed"
Timestamp: "2 days ago"
Feedback Content: "Alex, this is a promising concept. I'm particularly impressed with the innovative approach to resource allocation. However, I'd like to see a more detailed breakdown of the implementation timeline and potential risks. Please revise and resubmit within a week."



Reply to Supervisor Interface

User Profile Section: Current user details
Reply Text Area: "What are you thoughts?" placeholder
Action Buttons:

"Reply" button (primary)
"Cancel" button




3. My Team Management
   Route: /team-lead/team
   Purpose: Comprehensive team member management and coordination
   Layout Components:
   Page Header

Team Name: "Team Alpha" (Ruman One Bold, 32px)
Team Leader: "Led by Saleh" (Space Grotesk Regular, 14px)

Team Members Section

Section Title: "Members" (Space Grotesk Bold, 18px)

Members Table
Table Headers (Space Grotesk Medium, 14px):

Name: Member full name
E-mail: Contact email
#Mobile No: Phone number
Status: Membership status
Actions: Available operations

Sample Team Members:

Active Member:

Name: "Ethan Carter"
Email: "saleh@ruman.sa"
Phone: "0565123335"
Status: "Active" (green badge)
Actions: "Remove" button


Pending Member:

Name: "Ethan Carter"
Email: "saleh@ruman.sa"
Phone: "0565123335"
Status: "Pending" (yellow badge)
Actions: "Accept" and "Reject" buttons


Multiple Active Members:

Same format repeated for additional team members
All with consistent data structure



Status Badge System:

Active: Green background (#D1FAE5), text (#065F46)
Pending: Yellow background (#FEF3C7), text (#92400E)

Action Buttons:

"Add Member" (primary button at bottom)
"Accept" (green button for pending members)
"Reject" (red button for pending members)
"Remove" (red button for active members)


4. Add Team Member
   Route: /team-lead/add-member
   Purpose: Invite new members to join the team
   Layout Components:
   Page Header

Title: "Add Team Member" (Space Grotesk Bold, 32px)

Member Invitation Form
Form Fields (Space Grotesk Medium, 16px labels):

Full Name:

Label: "Full Name"
Placeholder: "Enter member's full name"
Input type: Text


Email:

Label: "Email"
Placeholder: "Enter member's email"
Input type: Email


Mobile Number:

Label: "Mobile Number"
Placeholder: "Enter member's mobile Number"
Input type: Tel



Form Actions

"Send Invitation" button (Ruman One Bold, 16px) - Primary action

Invitation Process Flow:

Team Leader fills out member details
System sends email invitation to potential member
Member receives invitation with team details
Member can accept/decline invitation
Team Leader sees pending status in My Team page
Team Leader can accept/reject the member request


5. Tracks Overview (Team Lead View)
   Route: /team-lead/tracks
   Purpose: View available tracks and understand track requirements
   Layout Components:
   Page Header

Title: "Tracks" (Space Grotesk Bold, 32px)
Subtitle: "Manage tracks for the current hackathon edition."

Tracks Display Table
Table Headers:

Track Name: Name of the track
Description: Brief track description
Assigned Supervisor: Supervisor contact

Available Tracks:

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



Track Selection Features:

Read-only view for Team Leaders (cannot edit tracks)
Track information for planning and understanding requirements
Supervisor contact information for communication
Track requirements and evaluation criteria (if expanded)


6. Workshops (Team Lead View)
   Route: /team-lead/workshops
   Purpose: Browse and register for available workshops
   Layout Components:
   Page Header

Title: "Upcoming Workshops" (Space Grotesk Bold, 32px)
Description: "Explore our upcoming workshops designed to enhance your skills and knowledge in various fields. Register now to secure your spot!"

Workshop Cards Layout
Workshop Card Structure (vertical card layout):

Mastering Data Analysis with Python

Details: "Speaker: Dr. Evelyn Reed | Sponsor: Data Insights Inc. | Date: July 15, 2024 | Time: 10:00 AM - 12:00 PM"
Title: Bold formatting (Space Grotesk Bold, 16px)
Description: "Learn to analyze complex datasets using Python, covering data manipulation, visualization, and statistical analysis."
Action: "Register" button (Space Grotesk Medium, 14px)


Advanced Web Development with React

Details: "Speaker: Mr. Ethan Carter | Sponsor: WebDev Solutions | Date: July 22, 2024 | Time: 2:00 PM - 4:00 PM"
Title: Bold formatting
Description: "Dive deep into React, building dynamic and responsive web applications with state management and component architecture."
Action: "Register" button


Introduction to Machine Learning

Details: "Speaker: Ms. Olivia Bennett | Sponsor: AI Innovations | Date: July 29, 2024 | Time: 11:00 AM - 1:00 PM"
Title: Bold formatting
Description: "Get started with machine learning, covering fundamental concepts, algorithms, and practical applications using popular libraries."
Action: "Register" button



Workshop Registration Features:

Individual registration (not team-wide)
Email confirmation with barcode
Capacity tracking and availability
Multiple workshop registration allowed
Prerequisites and requirements display


7. Dashboard (Alternative View)
   Route: /team-lead/dashboard
   Purpose: Alternative dashboard layout focusing on team performance
   Layout Components:
   Team Performance Overview

Team Progress Cards:

Idea submission status
Team formation completion
Workshop participation
Supervisor feedback status



Recent Team Activity

Activity Timeline:

Member additions/removals
Idea updates and submissions
Supervisor interactions
Workshop registrations
Deadline reminders



Team Communication Hub

Message Center:

Internal team messages
Supervisor communications
System notifications
Announcement feed



Deadline Tracker

Upcoming Deadlines:

Idea submission deadlines
Supervisor feedback responses
Workshop registration deadlines
Team milestone dates




Common UI Components & Design Specifications
Typography System

Main Titles: Space Grotesk Bold (32px)
Section Titles: Space Grotesk Bold (22px)
Subsection Titles: Space Grotesk Bold (18px)
Body Text: Space Grotesk Regular (16px for descriptions, 14px for tables)
Labels: Space Grotesk Medium (16px for forms, 14px for tables)
Navigation: Ruman One Bold/Regular (14px)
Buttons: Ruman One Bold (16px)

Navigation Structure
├── Dashboard (Team overview and quick actions)
├── Our Idea (Idea management and editing)
│   ├── Overview (idea details and progress)
│   ├── Comments (team and supervisor feedback)
│   └── Instructions (supervisor guidance)
├── My Team (Team member management)
│   ├── Team Members List
│   ├── Add Team Member
│   └── Member Status Management
├── Tracks (Available tracks information)
└── Workshops (Workshop browsing and registration)
Status Badge System

Active Member: Green background (#D1FAE5), text (#065F46)
Pending Member: Yellow background (#FEF3C7), text (#92400E)
Idea Approved: Green background (#D1FAE5), text (#065F46)
Idea Pending: Yellow background (#FEF3C7), text (#92400E)
Idea Needs Revision: Orange background (#FED7AA), text (#9A3412)

Form Design Standards

Input Fields: 16px padding, border radius, focus states
Labels: 16px Space Grotesk Medium, positioned above inputs
Placeholders: 16px Space Grotesk Regular, muted text color
Buttons: Ruman One Bold, 16px, appropriate color coding
Validation: Clear error messages with red highlighting

Responsive Design Features

Desktop: Full table layouts with all columns
Tablet: Condensed tables with essential information
Mobile: Card-based stacking layout
Touch Interactions: Optimized button sizes for mobile

Interactive Elements

Comment Threading: Nested replies with visual hierarchy
Real-time Updates: Live status changes and notifications
File Attachments: Upload and download capabilities
Progress Tracking: Visual progress bars and completion states

Accessibility Features

Keyboard Navigation: Full keyboard support
Screen Reader Support: Proper ARIA labels
High Contrast: Sufficient color contrast ratios
Focus Indicators: Clear visual focus states
Semantic HTML: Proper heading hierarchy and structure