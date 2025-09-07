# Visitor Role - Pages Documentation

## Overview
The Visitor role is designed for individuals who are not part of hackathon teams but are interested in attending workshops and learning sessions. Visitors have a simplified interface focused on workshop discovery, registration, and attendance management. Based on the Figma analysis, Visitors have access to 4 main sections: All Workshops, My Workshops, Login, and Registration.

---

## 1. Registration Page
**Route:** `/visitor/register`  
**Purpose:** Account creation for new visitors wanting to access workshops

### Layout Components:

**Left Panel - Welcome Section:**
- **Greeting:** "Welcome Back!" (45px, Ruman One Bold)
- **Description:** "If you Already Have An Account, Please Sign in" (16px, Ruman One Regular)  
- **Action Button:** "Login" button to switch to login form
- **Footer:** "@2025 RumanHack. All rights reserved." (16px, Space Grotesk Regular)

**Right Panel - Registration Form:**
- **Form Title:** "Register" (19px, Space Grotesk Bold)

**Form Fields:**
1. **User Type Selection:** (Radio buttons)
   - Team Leader (9px, Space Grotesk Medium)
   - Team Member (9px, Space Grotesk Medium)
   - **Visitor** (selected by default) (9px, Space Grotesk Medium)

2. **Personal Information:**
   - **Name:** Text input (11px, Space Grotesk Regular)
   - **Email:** Text input with validation (11px, Space Grotesk Regular)
   - **National ID:** Text input (11px, Space Grotesk Regular)
   - **Mobile Number:** Text input (11px, Space Grotesk Regular)
   - **Password:** Password input (11px, Space Grotesk Regular)
   - **Confirm Password:** Password confirmation (11px, Space Grotesk Regular)

3. **Professional Information:**
   - **Occupation:** Radio button selection (16px, Space Grotesk Medium)
     - Student (9px, Space Grotesk Medium)
     - Employee (9px, Space Grotesk Medium)
   - **Job Title:** Text input (conditional, shows when Employee selected) (11px, Space Grotesk Regular)

4. **Submit Button:** "Register" (16px, Ruman One Bold)

### Form Validation:
- Email format validation
- Password confirmation matching
- Required field validation
- National ID format validation
- Mobile number format validation

---

## 2. Login Page
**Route:** `/visitor/login`  
**Purpose:** Authentication for existing visitors

### Layout Components:

**Left Panel - Welcome Section:**
- **Greeting:** "Hello, Friend!" (45px, Ruman One Bold)
- **Description:** "Enter your personal details and start your journey with us." (16px, Ruman One Regular)
- **Action Button:** "Sign Up" button to switch to registration
- **Footer:** "@2025 RumanHack. All rights reserved."

**Right Panel - Login Form:**
- **Form Title:** "Login" (28px, Space Grotesk Bold)

**Form Fields:**
1. **Email:** Input field (16px, Space Grotesk Regular)
2. **Password:** Password input field (16px, Space Grotesk Regular)
3. **Submit Button:** "Login" (16px, Ruman One Bold)

**Social Login Section:**
- **Divider Text:** "or continue with" (16px, Space Grotesk Regular)
- **Social Media Buttons:** Google, Facebook, etc. (icons provided)

### Authentication Features:
- Email/password validation
- Social media login integration
- Password reset functionality
- Remember me option
- Redirect to dashboard after successful login

---

## 3. All Workshops Page
**Route:** `/visitor/workshops`  
**Purpose:** Browse and register for available workshops

### Layout Components:

**Header:** Standard visitor header with navigation

**Sidebar Navigation:**
- **Dashboard**
- **All Workshops** (active, bold styling)
- **My Workshops**

**Main Content Area:**
- **Page Title:** "Upcoming Workshops" (32px, Space Grotesk Bold)
- **Description:** "Explore our upcoming workshops designed to enhance your skills and knowledge in various fields. Register now to secure your spot!" (14px, Space Grotesk Regular)

**Workshop Cards:**

1. **Workshop Card 1:**
   - **Title:** "Mastering Data Analysis with Python" (16px, Space Grotesk Bold)
   - **Speaker/Details:** "Speaker: Dr. Evelyn Reed | Sponsor: Data Insights Inc. | Date: July 15, 2024 | Time: 10:00 AM - 12:00 PM" (14px, Space Grotesk Regular)
   - **Description:** "Learn to analyze complex datasets using Python, covering data manipulation, visualization, and statistical analysis." (14px, Space Grotesk Regular)
   - **Action:** "Register" button (14px, Space Grotesk Medium)

2. **Workshop Card 2:**
   - **Title:** "Advanced Web Development with React" (16px, Space Grotesk Bold)
   - **Speaker/Details:** "Speaker: Mr. Ethan Carter | Sponsor: WebDev Solutions | Date: July 22, 2024 | Time: 2:00 PM - 4:00 PM" (14px, Space Grotesk Regular)
   - **Description:** "Dive deep into React, building dynamic and responsive web applications with state management and component architecture." (14px, Space Grotesk Regular)
   - **Action:** "Register" button (14px, Space Grotesk Medium)

3. **Workshop Card 3:**
   - **Title:** "Introduction to Machine Learning" (16px, Space Grotesk Bold)
   - **Speaker/Details:** "Speaker: Ms. Olivia Bennett | Sponsor: AI Innovations | Date: July 29, 2024 | Time: 11:00 AM - 1:00 PM" (14px, Space Grotesk Regular)
   - **Description:** "Get started with machine learning, covering fundamental concepts, algorithms, and practical applications using popular libraries." (14px, Space Grotesk Regular)
   - **Action:** "Register" button (14px, Space Grotesk Medium)

### Card Features:
- Clean, professional layout
- Clear information hierarchy
- Speaker and sponsor information
- Date and time prominently displayed
- One-click registration process
- Hover effects for interactivity

### Registration Process:
1. Click "Register" button
2. Fill registration form (Name, Email, Phone, National ID)
3. Submit registration
4. Receive confirmation email with QR code
5. Success notification with next steps

---

## 4. My Workshops Page
**Route:** `/visitor/my-workshops`  
**Purpose:** View registered workshops and manage attendance

### Layout Components:

**Header:** Standard visitor header

**Sidebar Navigation:**
- **Dashboard**
- **All Workshops**
- **My Workshops** (active, bold styling)

**Main Content Area:**
- **Page Title:** "My Registered Workshops" (32px, Space Grotesk Bold)
- **Description:** "View your registered workshops and access your personal barcode for check-in at each event." (14px, Space Grotesk Regular)

**Registered Workshop Cards:**

1. **Workshop Card 1:**
   - **Title:** "Mastering Data Analysis with Python" (16px, Space Grotesk Bold)
   - **Speaker/Details:** "Speaker: Dr. Sophia Clark | Sponsor: Data Insights Inc. | Date: July 15, 2024 | Time: 10:00 AM - 12:00 PM" (14px, Space Grotesk Regular)
   - **Description:** "Learn to analyze complex datasets using Python, covering data manipulation, visualization, and statistical analysis." (14px, Space Grotesk Regular)
   - **Action:** "View Barcode" button (14px, Space Grotesk Medium)

2. **Workshop Card 2:**
   - **Title:** "Advanced Web Development with React" (16px, Space Grotesk Bold)
   - **Speaker/Details:** "Speaker: Mr. Lucas Harper | Sponsor: WebDev Solutions | Date: July 22, 2024 | Time: 2:00 PM - 4:00 PM" (14px, Space Grotesk Regular)
   - **Description:** "Dive deep into React, building dynamic and responsive web applications with state management and component architecture." (14px, Space Grotesk Regular)
   - **Action:** "View Barcode" button (14px, Space Grotesk Medium)

3. **Workshop Card 3:**
   - **Title:** "Introduction to Machine Learning" (16px, Space Grotesk Bold)
   - **Speaker/Details:** "Speaker: Ms. Amelia Turner | Sponsor: AI Innovations | Date: July 29, 2024 | Time: 11:00 AM - 1:00 PM" (14px, Space Grotesk Regular)
   - **Description:** "Get started with machine learning, covering fundamental concepts, algorithms, and practical applications using popular libraries." (14px, Space Grotesk Regular)
   - **Action:** "View Barcode" button (14px, Space Grotesk Medium)

### Barcode Functionality:
- Click "View Barcode" opens modal with QR code
- QR code contains unique registration information
- Scannable by workshop supervisors for attendance
- Downloadable/printable option
- Instructions for use at workshop venue

### Status Indicators:
- **Upcoming:** Workshop not yet started
- **In Progress:** Workshop currently happening
- **Completed:** Workshop finished, attendance recorded
- **Missed:** Did not attend registered workshop

---

## 5. My Profile Page
**Route:** `/visitor/profile`  
**Purpose:** Manage visitor account information and settings

### Layout Components:

**Header:** Standard visitor header

**Sidebar Navigation:** (if applicable)

**Main Content Area:**
- **Page Title:** "My Profile" (32px, Space Grotesk Bold)

**Profile Information Section:**
- **Profile Picture:** User avatar (circular, 40x40px)
- **Name Display:** "Sophia Clark" (22px, Space Grotesk Bold)
- **Email Display:** "sophia.clark@email.com" (16px, Space Grotesk Regular)

**Editable Form Fields:**

1. **Basic Information:**
   - **Name:** Text input (16px, Space Grotesk Medium)
   - **Email:** Email input (16px, Space Grotesk Medium)
   - **Date of Birth:** Date picker (16px, Space Grotesk Medium)
   - **Phone Number:** Text input (16px, Space Grotesk Medium)
   - **National ID:** Text input (16px, Space Grotesk Medium)

2. **Account Security:**
   - **Password:** Password input (16px, Space Grotesk Medium)
   - **Confirm Password:** Password confirmation (16px, Space Grotesk Medium)

3. **Professional Information:**
   - **Occupation:** Radio button selection (16px, Space Grotesk Medium)
     - Student (9px, Space Grotesk Medium)
     - Employee (9px, Space Grotesk Medium)
   - **Job Title:** Text input (conditional) (16px, Space Grotesk Medium)

4. **Save Changes Button:** (14px, Space Grotesk Bold)

### Profile Features:
- Real-time form validation
- Profile picture upload
- Password strength requirements
- Account deletion option
- Privacy settings
- Notification preferences

---

## Common UI Elements & Patterns

### Typography:
- **Primary Font:** Ruman One (headings, navigation)
- **Secondary Font:** Space Grotesk (body text, forms)
- **Accent Font:** Inter (UI elements)

### Color Palette:
- **Primary Brand Color:** Used for buttons and active states
- **Success Green:** For successful actions
- **Warning Yellow:** For pending states
- **Error Red:** For validation errors
- **Neutral Grays:** For text and backgrounds

### Interactive Elements:
- **Registration Buttons:** Clear call-to-action styling
- **Barcode Buttons:** Distinct from registration buttons
- **Form Inputs:** Clean, accessible design
- **Cards:** Hover effects with subtle shadows
- **Navigation:** Simple, focused menu structure

### Responsive Design:
- **Mobile-optimized:** Forms adapt to smaller screens
- **Touch-friendly:** Large buttons and input areas
- **Simplified navigation:** Collapsible menu on mobile
- **Optimized content:** Readable on all devices

---

## Visitor Permissions & Capabilities:

### What Visitors CAN do:
- Create and manage their account
- Browse all available workshops
- Register for multiple workshops
- View their registered workshops
- Access barcode for workshop check-in
- Update personal information
- Cancel workshop registrations (within policy)

### What Visitors CANNOT do:
- Access hackathon team features
- Submit ideas or participate in competitions
- View other users' information
- Access admin or supervisor functions
- Create or manage workshops
- Access internal hackathon communications

### User Experience Considerations:
- **Simplified Interface:** Focus only on workshop-related features
- **Clear Navigation:** Easy access to workshops and personal info
- **Mobile-First:** Optimized for mobile workshop discovery and check-in
- **Quick Registration:** Streamlined process for workshop sign-up
- **Clear Instructions:** Help text for barcode usage and attendance
- **Confirmation Systems:** Email confirmations for all registrations

### Workshop Registration Flow:
1. **Discovery:** Browse workshops on "All Workshops" page
2. **Details:** View comprehensive workshop information
3. **Registration:** One-click registration with form validation
4. **Confirmation:** Immediate success message and email confirmation
5. **Barcode Access:** View and download QR code for attendance
6. **Attendance:** Present barcode at workshop for check-in
7. **Management:** View all registered workshops in "My Workshops"

### Error Handling:
- **Network Issues:** Offline capability for viewing registered workshops
- **Registration Errors:** Clear error messages with resolution steps
- **Validation Issues:** Real-time form validation with helpful feedback
- **Authentication:** Secure login with password recovery options