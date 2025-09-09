# 🎯 TRACK SUPERVISOR ROLE - COMPLETE BUSINESS DETAILS
## Comprehensive Design Specifications and User Flows

---

## 📊 ROLE OVERVIEW

### Role Identity
- **English Name**: Track Supervisor
- **Arabic Name**: مشرف مسار
- **Database Value**: `track_supervisor`
- **Primary Color Theme**: Orange/Amber Gradient (#F59E0B to #D97706)
- **Secondary Color**: Light orange tints for backgrounds
- **Icon Theme**: Filled icons for authority indication

### Core Responsibilities
1. Review and evaluate ideas in assigned track ONLY
2. Provide detailed feedback to teams
3. Score ideas based on criteria
4. Monitor track progress
5. Communicate with teams
6. Generate track-specific reports
7. Recommend winners

### Assignment Method
- Assigned by Hackathon Admin
- One supervisor per track (can supervise multiple tracks)
- Cannot self-assign or change tracks
- Time-bound assignment (per hackathon edition)

---

## 📄 PAGE 1: SUPERVISOR DASHBOARD

### Page Information
- **URL**: `/track-supervisor/dashboard`
- **File References**:
  - Figma: `Supervisor_Dashboard.png`
  - Vue: Available in `supervisor/` folder
- **Primary Purpose**: Overview of review tasks and track statistics

### Visual Layout Structure

#### Header Section (Professional Theme)
- **Left Side**:
  - Hackathon logo with "Supervisor Portal" text
  - Track name badge (Orange)
  
- **Right Side**:
  - Notification bell with count
  - User profile with "Supervisor" badge
  - Logout option

#### Sidebar Navigation (280px width)
**Navigation Items**:
1. **Dashboard** (Active)
   - Icon: Dashboard grid (filled)
   - Orange gradient background when active

2. **Ideas to Review**
   - Icon: Document stack
   - Badge: Pending count (red if > 0)
   
3. **Teams in Track**
   - Icon: Users group
   - Shows team count
   
4. **My Track**
   - Icon: Flag
   - Track statistics
   
5. **Communications**
   - Icon: Chat bubbles
   - Unread messages badge
   
6. **Reports**
   - Icon: Chart bar
   - Export options

#### Main Content Area

##### Key Metrics Cards (4 columns grid)

1. **Pending Reviews Card**
   - Background: Red gradient (#EF4444 to #DC2626)
   - Large number display
   - "Ideas awaiting review"
   - Urgency indicator if deadline approaching

2. **Under Review Card**
   - Background: Yellow gradient (#F59E0B to #D97706)
   - Currently reviewing count
   - Average time per review

3. **Completed Reviews Card**
   - Background: Green gradient (#10B981 to #059669)
   - Total reviewed
   - Approval rate percentage

4. **Track Statistics Card**
   - Background: Blue gradient (#3B82F6 to #2563EB)
   - Total teams in track
   - Average score
   - Quality indicator

##### Review Priority Section
- **Title**: "Ideas Requiring Immediate Attention" (18px, bold)
- **Priority Algorithm**:
  1. Deadline proximity
  2. Submission order
  3. Team completeness
  
- **List View**:
  - Idea title
  - Team name
  - Submission time
  - Time remaining badge
  - "Start Review" button

##### Track Overview Chart
- **Type**: Donut chart
- **Data**: Ideas by status
  - Pending (red)
  - Under Review (yellow)
  - Approved (green)
  - Rejected (gray)
  - Needs Revision (orange)

##### Recent Activity Timeline
- **Items**:
  - Review completed
  - Feedback sent
  - Team responded
  - Status changed
- **Format**: Time-based with icons

---

## 📄 PAGE 2: IDEAS LIST

### Page Information
- **URL**: `/track-supervisor/ideas`
- **File References**:
  - Figma: Part of supervisor designs
- **Primary Purpose**: Manage all ideas in assigned track

### Visual Layout Structure

#### Page Header
- **Title**: "Ideas in [Track Name]" (24px, bold)
- **Subtitle**: Total count and statistics
- **Actions**: 
  - Filter dropdown
  - Sort options
  - Export button

#### Filter Bar
- **Status Filter**:
  - All
  - Pending Review
  - Under Review
  - Reviewed
  - Needs Revision
  
- **Sort By**:
  - Submission Date (default)
  - Team Name
  - Score
  - Priority

#### Ideas Table

##### Table Columns
1. **ID** (5%)
   - Unique identifier
   - Clickable link

2. **Idea Title** (25%)
   - Bold text
   - Truncated with tooltip
   - Status badge

3. **Team** (15%)
   - Team name
   - Leader name below
   - Member count

4. **Submitted** (15%)
   - Date and time
   - "X days ago" format
   - Late submission warning

5. **Status** (15%)
   - Color-coded badge
   - Interactive (dropdown on click)
   - Quick status change

6. **Score** (10%)
   - Numerical score if reviewed
   - "-" if pending
   - Color coded by range

7. **Priority** (10%)
   - High/Medium/Low
   - Visual indicator
   - Based on deadline

8. **Actions** (5%)
   - Review button/icon
   - View details
   - Download files

##### Row States
- **Pending**: Light red background tint
- **Under Review**: Light yellow tint
- **Reviewed**: Default white
- **Hover**: Slight gray background
- **Selected**: Orange border

##### Bulk Actions
- **Select All** checkbox
- **Bulk Actions** dropdown:
  - Mark as reviewed
  - Export selected
  - Send message

---

## 📄 PAGE 3: IDEA REVIEW

### Page Information
- **URL**: `/track-supervisor/ideas/{id}/review`
- **File References**:
  - Figma: `Idea.png`
- **Primary Purpose**: Detailed review interface

### Visual Layout Structure

#### Review Header
- **Breadcrumb**: Dashboard > Ideas > [Idea Title]
- **Status**: Current review status
- **Timer**: Time spent on review (auto-tracking)
- **Actions**: Save Draft, Submit Review

#### Layout: Two-Column (70/30 split)

##### Left Column: Idea Details

###### Idea Information Card
- **Title**: Large, prominent (20px)
- **Team**: With member avatars
- **Track**: Confirmation of correct track
- **Submission**: Date and time
- **Late Submission**: Red warning if applicable

###### Problem Statement Section
- **Label**: "Problem Statement"
- **Content**: Full text (readonly)
- **Character Count**: Shown
- **Expand/Collapse**: For long text

###### Solution Section
- **Label**: "Proposed Solution"
- **Content**: Rich text display
- **Formatting**: Preserved from submission
- **Media**: Embedded images if any

###### Target Audience Section
- **Label**: "Target Audience"
- **Content**: Text display
- **Demographics**: If specified

###### Innovation Aspects
- **Unique Features**: Listed
- **Competitive Advantage**: Described
- **Market Opportunity**: Analyzed

###### Files Section
- **Label**: "Supporting Documents" 
- **File List**:
  - File icon by type
  - Name and size
  - Upload date
  - Preview button (for images/PDF)
  - Download button
  - Virus scan status

##### Right Column: Review Panel

###### Scoring Section
- **Title**: "Evaluation Criteria"
- **Scoring Method**: 1-10 scale per criteria

**Criteria Cards** (Each criterion):
1. **Innovation** (25% weight)
   - Slider: 1-10
   - Current score display
   - Text input for notes
   
2. **Feasibility** (25% weight)
   - Technical feasibility
   - Resource availability
   - Timeline realistic

3. **Impact** (20% weight)
   - Problem significance
   - Solution effectiveness
   - Scalability potential

4. **Presentation** (15% weight)
   - Clarity of explanation
   - Document quality
   - Professional presentation

5. **Team Capability** (15% weight)
   - Team composition
   - Skills match
   - Collaboration evidence

**Total Score**:
- Auto-calculated
- Large display (24px)
- Color coded:
  - 80-100: Green
  - 60-79: Yellow
  - 40-59: Orange
  - Below 40: Red

###### Feedback Section
- **Label**: "Detailed Feedback"
- **Type**: Rich text editor
- **Features**:
  - Bold, italic, lists
  - Minimum 100 characters
  - Maximum 2000 characters
  - Templates available
  
**Feedback Templates**:
- "Excellent Innovation"
- "Needs Technical Improvement"
- "Unclear Problem Statement"
- "Strong Team, Weak Execution"

###### Decision Section
- **Label**: "Review Decision"
- **Options** (Radio buttons):
  1. **Approve** (Green)
     - Proceed to next round
  2. **Approve with Conditions**
     - Specify conditions
  3. **Request Revision** (Orange)
     - Specify required changes
     - Set deadline
  4. **Reject** (Red)
     - Provide detailed reason

###### Private Notes
- **Label**: "Internal Notes (Not visible to team)"
- **Purpose**: For admin/other supervisors
- **Textarea**: 500 characters max

#### Review Actions Bar (Sticky bottom)
- **Save as Draft**: Saves progress
- **Request Clarification**: Opens communication
- **Submit Review**: Final submission
- **Cancel**: Returns to list (with confirmation)

---

## 📄 PAGE 4: TEAMS IN TRACK

### Page Information
- **URL**: `/track-supervisor/teams`
- **File References**:
  - Figma: Part of supervisor section
- **Primary Purpose**: View all teams in supervised track

### Visual Layout Structure

#### Teams Grid View
- **Layout**: 3 columns on desktop
- **Card Height**: 220px

#### Team Card Structure
- **Header**:
  - Team name (16px, bold)
  - Team code badge
  - Status indicator (active/inactive)

- **Body**:
  - Leader photo and name
  - Member count: "4/5 members"
  - Idea status badge
  - Last activity timestamp

- **Footer**:
  - View Team button
  - View Idea button (if submitted)
  - Contact button

#### Team Details Modal
**Triggered by**: View Team button

**Modal Content**:
- Team full information
- Member list with contacts
- Idea submission history
- Communication log
- Performance metrics

---

## 📄 PAGE 5: TRACK OVERVIEW

### Page Information
- **URL**: `/track-supervisor/track`
- **Primary Purpose**: Comprehensive track statistics

### Visual Layout Structure

#### Track Information Header
- **Track Name**: Large display
- **Description**: Full track description
- **Supervisor**: Your name and avatar
- **Co-supervisors**: If any

#### Statistics Dashboard

##### Charts Section
1. **Submission Timeline** (Line chart)
   - Daily submissions
   - Cumulative total
   - Deadline marker

2. **Score Distribution** (Histogram)
   - Score ranges
   - Bell curve overlay
   - Average line

3. **Team Performance** (Bar chart)
   - Teams ranked by score
   - Color coded by status

4. **Review Progress** (Progress bars)
   - Reviewed vs Pending
   - Time to deadline
   - Quality metrics

##### Top Performers Section
- **Title**: "Leading Teams"
- **List**: Top 5 teams
- **Display**: Score, title, strengths

##### Areas of Concern
- **Low Scoring Ideas**: Need attention
- **Incomplete Submissions**: Missing documents
- **No Response Teams**: Not responding to feedback

---

## 📄 PAGE 6: COMMUNICATIONS

### Page Information
- **URL**: `/track-supervisor/communications`
- **Primary Purpose**: Communicate with teams

### Visual Layout Structure

#### Communication Channels

##### Announcements Tab
- **Create Announcement**:
  - To: All teams in track
  - Subject line
  - Message body (rich text)
  - Attachments
  - Send button

- **Sent Announcements**:
  - List view
  - Read receipts
  - Response count

##### Direct Messages Tab
- **Conversation List** (Left sidebar):
  - Team name
  - Last message preview
  - Unread indicator
  - Timestamp

- **Message Thread** (Right panel):
  - Chat interface
  - Message bubbles
  - Timestamps
  - Read receipts
  - File attachments

##### Feedback Responses Tab
- **Team Responses** to feedback:
  - Original feedback
  - Team's response
  - Action required flag
  - Reply option

#### Message Composer
- **Rich Text Editor**
- **Attachment Support**
- **Message Templates**:
  - Request more information
  - Revision required
  - Positive feedback
  - Meeting request

---

## 📄 PAGE 7: REPORTS

### Page Information
- **URL**: `/track-supervisor/reports`
- **Primary Purpose**: Generate and export track reports

### Visual Layout Structure

#### Report Types

##### 1. Track Summary Report
- **Sections**:
  - Executive summary
  - Statistics overview
  - Team listings
  - Score distributions
  - Recommendations

- **Export Options**:
  - PDF (formatted)
  - Excel (data only)
  - Word (editable)

##### 2. Detailed Review Report
- **Per Idea**:
  - Full review details
  - Scores breakdown
  - Feedback provided
  - Decision rationale

##### 3. Team Performance Report
- **Metrics**:
  - Submission quality
  - Response time
  - Revision compliance
  - Final scores

#### Report Generator
- **Filters**:
  - Date range
  - Status filter
  - Score range
  - Team selection

- **Customization**:
  - Include/exclude sections
  - Add custom notes
  - Branding options

- **Preview**: Before export
- **Schedule**: Automatic generation

---

## 🔄 SUPERVISOR WORKFLOWS

### Workflow A: Complete Review Process

1. **Assignment**
   - Receive track assignment from admin
   - Email notification
   - Access credentials provided

2. **Initial Review**
   - Login to supervisor portal
   - View assigned track
   - Check pending ideas count

3. **Idea Review Process**
   - Open idea from priority list
   - Read complete submission
   - Download and review files
   - Score each criterion
   - Write detailed feedback
   - Make decision
   - Submit review

4. **Follow-up**
   - Monitor team responses
   - Answer clarifications
   - Update reviews if needed

5. **Final Recommendations**
   - Review all scores
   - Identify top performers
   - Submit recommendations
   - Generate final report

### Workflow B: Revision Request Process

1. **Identify Issues**
   - Missing information
   - Unclear sections
   - Technical concerns

2. **Request Revision**
   - Select "Request Revision"
   - Specify exact requirements
   - Set reasonable deadline
   - Send to team

3. **Monitor Response**
   - Track revision status
   - Send reminders
   - Review resubmission

4. **Final Review**
   - Compare changes
   - Update scores
   - Final decision

### Workflow C: Bulk Review Process

1. **Sort by Priority**
   - Deadline approaching
   - Complete submissions first

2. **Quick Review Mode**
   - Streamlined interface
   - Keyboard shortcuts
   - Quick scoring

3. **Batch Actions**
   - Apply similar feedback
   - Bulk status updates

---

## 🎨 VISUAL DESIGN SPECIFICATIONS

### Color Scheme
```css
/* Primary - Orange Theme */
--supervisor-primary: #F59E0B;
--supervisor-primary-dark: #D97706;
--supervisor-primary-light: #FEF3C7;

/* Status Colors */
--pending: #EF4444;
--reviewing: #F59E0B;
--approved: #10B981;
--rejected: #6B7280;
--revision: #3B82F6;
```

### Typography
```css
/* Specific to Supervisor */
.supervisor-heading {
  font-size: 24px;
  font-weight: 700;
  color: #D97706;
}

.review-text {
  font-size: 14px;
  line-height: 1.6;
  color: #374151;
}
```

### Components
```css
/* Score Slider */
.score-slider {
  height: 8px;
  background: #E5E7EB;
  border-radius: 4px;
}

.score-slider-fill {
  background: linear-gradient(90deg, #EF4444, #F59E0B, #10B981);
  height: 100%;
  border-radius: 4px;
}

/* Review Card */
.review-card {
  border-left: 4px solid #F59E0B;
  background: white;
  padding: 20px;
  margin-bottom: 16px;
}
```

---

## 📱 RESPONSIVE BEHAVIOR

### Mobile (< 768px)
- Single column layout
- Collapsed scoring section
- Swipeable cards
- Bottom sheet for actions

### Tablet (768px - 1024px)
- Two column layout maintained
- Touch-optimized sliders
- Larger touch targets

### Desktop (> 1024px)
- Full three-panel layout
- Hover states enabled
- Keyboard navigation

---

## 🔒 PERMISSIONS & RESTRICTIONS

### Supervisor CAN:
- View ideas in assigned track(s) only
- Score and review ideas
- Provide feedback
- Request revisions
- Communicate with teams
- Generate track reports
- View team details in track
- Export data from track

### Supervisor CANNOT:
- View ideas from other tracks
- Create or delete teams
- Submit ideas
- Edit team submissions
- Change track assignments
- Access system settings
- View other supervisors' private notes
- Override admin decisions

---

## ⌨️ KEYBOARD SHORTCUTS

- **Alt + R**: Start review
- **Alt + S**: Save draft
- **Alt + Enter**: Submit review
- **1-9, 0**: Quick score (1-10)
- **Alt + N**: Next idea
- **Alt + P**: Previous idea
- **Alt + F**: Focus feedback field
- **Escape**: Close modal/cancel

---

## 📊 METRICS & KPIs

### Supervisor Performance Metrics
1. **Review Completion Rate**: Target 100%
2. **Average Review Time**: Target 15-30 min
3. **Feedback Quality Score**: Based on length/detail
4. **Response Time**: Target < 24 hours
5. **Revision Resolution**: Target < 48 hours

### Track Health Metrics
1. **Submission Rate**: X% of teams submitted
2. **Quality Distribution**: Bell curve expected
3. **Engagement Rate**: Team responses to feedback
4. **Revision Success Rate**: Improved scores after revision

---

## 🌍 LOCALIZATION

### Arabic Translations
```javascript
const supervisorTranslations = {
  'Review': 'مراجعة',
  'Score': 'نقاط',
  'Feedback': 'ملاحظات',
  'Approve': 'موافقة',
  'Reject': 'رفض',
  'Revision Required': 'يتطلب مراجعة',
  'Track Supervisor': 'مشرف المسار',
  'Evaluation Criteria': 'معايير التقييم',
  'Submit Review': 'إرسال المراجعة'
}
```

### RTL Considerations
- Slider directions reversed
- Progress bars RTL
- Score displays right-aligned
- Navigation menu on right

---

## 🚨 CRITICAL FEATURES

These MUST work perfectly:

1. **Track Isolation**: Supervisor can ONLY see assigned track
2. **Score Calculation**: Weighted scoring accurate
3. **Feedback Delivery**: Teams receive feedback immediately
4. **Revision Tracking**: All changes logged
5. **Report Generation**: Accurate data export
6. **Communication**: Two-way messaging functional

---

## 📝 IMPLEMENTATION NOTES

### State Management
```javascript
// Supervisor state
{
  supervisor: {
    id: 'xxx',
    name: 'Supervisor Name',
    tracks: ['track_id_1'],
    current_track: 'track_id_1'
  },
  
  reviews: {
    pending: [],
    in_progress: {},
    completed: [],
    current_review: null
  },
  
  scoring: {
    criteria: [],
    weights: {},
    current_scores: {},
    total_score: 0
  }
}
```

### API Endpoints
```javascript
// Supervisor specific endpoints
GET    /api/supervisor/dashboard
GET    /api/supervisor/tracks
GET    /api/supervisor/ideas
GET    /api/supervisor/ideas/{id}
POST   /api/supervisor/ideas/{id}/review
PUT    /api/supervisor/ideas/{id}/review
POST   /api/supervisor/ideas/{id}/feedback
GET    /api/supervisor/teams
GET    /api/supervisor/communications
POST   /api/supervisor/messages
GET    /api/supervisor/reports
POST   /api/supervisor/reports/generate
```

---

## 🐛 EDGE CASES

1. **Multiple supervisors per track**: Show co-supervisor indicator
2. **Supervisor reassignment**: Preserve review history
3. **Late submissions**: Special handling and flags
4. **Incomplete reviews**: Auto-save and recovery
5. **Conflicting scores**: Require resolution
6. **Network failure during review**: Local storage backup
7. **File corruption**: Graceful error handling

---

## ✅ TESTING CHECKLIST

### Functional Tests
- [ ] Track assignment works
- [ ] Can only see assigned track
- [ ] Review form validates
- [ ] Scores calculate correctly
- [ ] Feedback saves and sends
- [ ] Reports generate accurately

### UI/UX Tests
- [ ] Responsive on all devices
- [ ] Arabic RTL layout
- [ ] Loading states work
- [ ] Error messages clear
- [ ] Navigation intuitive

### Performance Tests
- [ ] Large file downloads
- [ ] Multiple simultaneous reviews
- [ ] Report generation speed
- [ ] Search performance

### Security Tests
- [ ] Track isolation enforced
- [ ] Cannot access other tracks via URL
- [ ] Review data encrypted
- [ ] Session management

---

This completes the comprehensive business details for the Track Supervisor role.