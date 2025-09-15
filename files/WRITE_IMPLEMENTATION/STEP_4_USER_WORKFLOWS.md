# STEP 4: USER WORKFLOWS
## Complete Step-by-Step User Journeys

---

## 📋 INSTRUCTIONS
Document every workflow with exact steps, state changes, and error scenarios.

---

## WORKFLOW TEMPLATE:
```
### Workflow: [Name]
**Actor:** [Role]
**Goal:** [What user wants to achieve]
**Preconditions:** [What must be true before starting]

**Steps:**
1. [Action] → [System Response] → [State Change]
2. ...

**Database Changes:**
- Table: [name] | Action: [INSERT/UPDATE/DELETE] | Fields: [list]

**Notifications:**
- Email to: [recipient] | Template: [name] | Trigger: [when]

**Error Scenarios:**
1. Error: [what went wrong] | Handling: [how to handle] | Message: [shown to user]

**Success Criteria:**
- [What indicates success]
```

---

# CORE WORKFLOWS

## 1. NEW USER REGISTRATION AND ROLE SELECTION

### Workflow: User Registration with Role
**Actor:** New User
**Goal:** Register and select participation type
**Preconditions:** Registration period is open

**Steps:**
1. User visits /register → System shows registration form → No state change
2. User fills basic info (name, email, phone, national_id) → Client validates format → Show inline validation
3. User selects occupation (student/employee) → If employee, show job title field → Update form
4. User selects role (visitor/team_leader/team_member) → Show role description → Update form
5. User sets password → Validate strength → Show password requirements
6. User submits form → System validates all fields → Check uniqueness
7. System creates user account → Send verification email → User created with 'unverified' status
8. User clicks email link → System verifies email → Update status to 'active'
9. System redirects to role-specific dashboard → User logged in → Session created

**Database Changes:**
- Table: users | Action: INSERT | Fields: all user fields + role
- Table: email_verifications | Action: INSERT | Fields: token, user_id, expires_at
- Table: activity_log | Action: INSERT | Fields: user_id, action='registered'

**Notifications:**
- Email to: User | Template: welcome_[role] | Trigger: After registration
- Email to: User | Template: verify_email | Trigger: Immediately

**Error Scenarios:**
1. Error: Email already exists | Handling: Show inline error | Message: "البريد مستخدم / Email already registered"
2. Error: National ID exists | Handling: Show inline error | Message: "رقم الهوية مسجل / National ID already registered"
3. Error: Invalid phone format | Handling: Show inline error | Message: "صيغة الجوال خاطئة / Invalid phone format"
4. Error: Registration closed | Handling: Redirect to closed page | Message: "التسجيل مغلق / Registration closed"

**Success Criteria:**
- User account created in database
- User can login with credentials
- User sees role-appropriate dashboard

---

## 2. TEAM CREATION BY LEADER

### Workflow: Team Leader Creates Team
**Actor:** Team Leader
**Goal:** Create a team for hackathon participation
**Preconditions:** User role is team_leader, no existing team, registration open

**Steps:**
1. Leader clicks "Create Team" on dashboard → System checks existing team → Show form if none
2. Leader enters team name → System checks uniqueness real-time → Show availability
3. Leader enters description (optional) → Character counter updates → Max 500 chars
4. Leader selects track from dropdown → System shows track description → Track selected
5. Leader submits form → System validates all fields → Create team
6. System generates unique team code → Create team record → Leader becomes first member
7. System shows success message → Display team code prominently → Option to copy code
8. Leader redirected to team management → Show empty members list → Show invite button

**Database Changes:**
- Table: teams | Action: INSERT | Fields: name, description, leader_id, track_id, hackathon_id, code
- Table: team_members | Action: INSERT | Fields: team_id, user_id, role='leader', status='active'
- Table: activity_log | Action: INSERT | Fields: user_id, action='team_created', team_id

**Notifications:**
- Email to: Team Leader | Template: team_created | Trigger: After creation
- In-app notification: "Team created successfully"

**Error Scenarios:**
1. Error: Team name taken | Handling: Show inline error | Message: "اسم الفريق محجوز / Team name already taken"
2. Error: Already has team | Handling: Redirect to existing | Message: "لديك فريق بالفعل / You already have a team"
3. Error: Track full | Handling: Disable option | Message: "المسار ممتلئ / Track is full"
4. Error: Past deadline | Handling: Show deadline message | Message: "انتهى وقت إنشاء الفرق / Team creation period ended"

**Success Criteria:**
- Team exists in database
- Leader can access team management
- Team code is unique and shareable

---

## 3. TEAM MEMBER INVITATION FLOW

### Workflow: Invite and Join Team
**Actor:** Team Leader & Team Member
**Goal:** Add members to team
**Preconditions:** Team exists, less than 5 members

**Steps:**

**Part A - Leader Invites:**
1. Leader opens team page → System shows current members → Show invite button
2. Leader clicks "Invite Member" → Modal opens → Show invite options
3. Leader enters email OR national ID → System searches for user → Validate input
4. System finds user → Check if user available → Show user preview
5. Leader confirms invitation → System sends invitation → Create invitation record
6. Invited user receives email → Email contains team info → Link to accept/reject

**Part B - Member Accepts:**
7. Member clicks email link → System validates token → Show team details
8. Member clicks "Accept" → System checks team capacity → Add to team
9. System updates member status → Send confirmation to both → Update team count
10. Member redirected to team dashboard → Can now see team details → Access idea

**Part C - Member Requests:**
1. Member without team browses teams → System shows available teams → Filter by track
2. Member clicks "Request to Join" → System creates request → Notify leader
3. Leader receives notification → Reviews member profile → Approve/Reject
4. If approved → Member added to team → Both notified
5. If rejected → Member notified → Can request other teams

**Database Changes:**
- Table: team_invitations | Action: INSERT | Fields: team_id, email, token, status
- Table: team_members | Action: INSERT | Fields: team_id, user_id, role='member', status
- Table: team_join_requests | Action: INSERT/UPDATE | Fields: team_id, user_id, status
- Table: notifications | Action: INSERT | Fields: user_id, type, message

**Notifications:**
- Email to: Invited User | Template: team_invitation | Trigger: On invite
- Email to: Leader | Template: join_request | Trigger: On request
- Email to: Member | Template: invitation_accepted | Trigger: On accept
- In-app: Real-time notification to leader

**Error Scenarios:**
1. Error: User not found | Handling: Show not found | Message: "المستخدم غير موجود / User not found"
2. Error: User in another team | Handling: Show status | Message: "المستخدم في فريق آخر / User already in a team"
3. Error: Team full (5 members) | Handling: Disable invite | Message: "الفريق مكتمل / Team is full"
4. Error: Invitation expired | Handling: Show expired | Message: "انتهت صلاحية الدعوة / Invitation expired"

**Success Criteria:**
- Member successfully added to team
- Both parties receive confirmation
- Team member count updated

---

## 4. IDEA SUBMISSION WORKFLOW

### Workflow: Submit Team Idea
**Actor:** Team Leader (with member collaboration)
**Goal:** Submit idea for track review
**Preconditions:** Team exists, submission period open

**Steps:**
1. Leader clicks "Submit Idea" → System checks existing idea → Show form if none
2. Leader enters idea title → Validate length (10-200 chars) → Show counter
3. Leader enters detailed description → Rich text editor available → Min 100 chars
4. Leader uploads presentation file → Validate type (PDF/PPT) → Show upload progress
5. Leader adds supporting documents → Max 8 files, 15MB each → Show file list
6. Team member views idea → Can edit if permitted → Changes logged
7. Member uploads additional file → File added to list → Audit log entry
8. Leader reviews final idea → Clicks "Submit for Review" → Confirmation dialog
9. System locks idea for editing → Changes status to 'pending' → Notify supervisor
10. Supervisor receives notification → Idea appears in review queue → Email sent

**Database Changes:**
- Table: ideas | Action: INSERT | Fields: team_id, title, description, status='pending'
- Table: idea_files | Action: INSERT | Fields: idea_id, filename, path, uploaded_by
- Table: idea_audit_logs | Action: INSERT | Fields: idea_id, user_id, action, timestamp
- Table: notifications | Action: INSERT | Fields: user_id, type='new_idea'

**Notifications:**
- Email to: Team Leader | Template: idea_submitted | Trigger: On submit
- Email to: Track Supervisor | Template: new_idea_to_review | Trigger: On submit
- In-app: "Idea submitted successfully"

**Error Scenarios:**
1. Error: Title too short/long | Handling: Show requirements | Message: "العنوان 10-200 حرف / Title must be 10-200 characters"
2. Error: Description too short | Handling: Show minimum | Message: "الوصف أقل من 100 حرف / Description minimum 100 characters"
3. Error: File too large | Handling: Reject file | Message: "الملف أكبر من 15 ميجا / File exceeds 15MB"
4. Error: Wrong file type | Handling: Reject file | Message: "نوع الملف غير مدعوم / File type not supported"
5. Error: Submission closed | Handling: Show deadline | Message: "انتهت فترة التسليم / Submission period ended"

**Success Criteria:**
- Idea saved with 'pending' status
- Files uploaded successfully
- Supervisor notified
- Team cannot edit after submission

---

## 5. IDEA REVIEW BY SUPERVISOR

### Workflow: Review and Evaluate Idea
**Actor:** Track Supervisor
**Goal:** Review submitted idea and provide decision
**Preconditions:** Idea submitted, supervisor assigned to track

**Steps:**
1. Supervisor opens review dashboard → System shows pending ideas → Sort by date
2. Supervisor clicks idea to review → System loads full details → Mark as 'under_review'
3. Supervisor reads description → Downloads files → Reviews thoroughly
4. Supervisor scores each criterion → System calculates total → Show score breakdown
5. Supervisor writes feedback → Min 50 characters → Guidance for team
6. Supervisor selects decision:
   - **If Approve:** → Status = 'approved' → Team can proceed
   - **If Reject:** → Status = 'rejected' → Team notified with reasons
   - **If Needs Revision:** → Status = 'needs_revision' → Team can edit and resubmit
7. Supervisor submits review → System saves decision → Send notifications
8. Team receives email → Can view feedback → Take appropriate action

**Database Changes:**
- Table: ideas | Action: UPDATE | Fields: status, supervisor_id, reviewed_at
- Table: idea_reviews | Action: INSERT | Fields: idea_id, supervisor_id, scores, feedback, decision
- Table: notifications | Action: INSERT | Fields: user_id, type='idea_reviewed'

**Notifications:**
- Email to: Team Leader | Template: idea_[status] | Trigger: On review submit
- Email to: Team Members | Template: idea_status_update | Trigger: On review
- In-app: Real-time notification to team

**Error Scenarios:**
1. Error: Incomplete scoring | Handling: Prevent submit | Message: "يجب تقييم كل المعايير / Must score all criteria"
2. Error: No feedback | Handling: Require feedback | Message: "الملاحظات مطلوبة / Feedback required"
3. Error: No decision selected | Handling: Require selection | Message: "اختر القرار / Select decision"

**Success Criteria:**
- Review saved in database
- Team notified of decision
- Idea status updated
- Feedback available to team

---

## 6. WORKSHOP REGISTRATION

### Workflow: Register for Workshop
**Actor:** Any User (Visitor, Team Member, etc.)
**Goal:** Register to attend workshop
**Preconditions:** Workshop published, seats available

**Steps:**
1. User browses workshop list → System shows available workshops → Filter/search options
2. User clicks workshop details → System shows full information → Speakers, time, location
3. User clicks "Register" → System checks if logged in → Redirect to login if not
4. System checks existing registration → Prevent duplicate → Show if already registered
5. If visitor role → Show registration form → Collect required info
6. If other role → Use existing profile → Pre-fill information
7. User confirms registration → System creates registration → Generate unique QR code
8. System sends confirmation email → Email contains QR code → Add to calendar link
9. User redirected to "My Workshops" → Shows registration list → Can view/print QR

**Database Changes:**
- Table: workshop_registrations | Action: INSERT | Fields: workshop_id, user_id, barcode, status
- Table: workshops | Action: UPDATE | Fields: registered_count (increment)

**Notifications:**
- Email to: User | Template: workshop_registration | Trigger: On register
- Email to: User | Template: workshop_reminder | Trigger: 24 hours before
- SMS (optional): Reminder 2 hours before

**Error Scenarios:**
1. Error: Workshop full | Handling: Disable registration | Message: "الورشة ممتلئة / Workshop is full"
2. Error: Already registered | Handling: Show existing | Message: "مسجل مسبقاً / Already registered"
3. Error: Time conflict | Handling: Show warning | Message: "تعارض في الوقت / Time conflict with another workshop"
4. Error: Registration closed | Handling: Show closed | Message: "التسجيل مغلق / Registration closed"

**Success Criteria:**
- Registration saved in database
- QR code generated and unique
- Confirmation email sent
- User can view registration

---

## 7. WORKSHOP CHECK-IN

### Workflow: Check-in for Workshop Attendance
**Actor:** Workshop Supervisor
**Goal:** Record attendance for workshop
**Preconditions:** Workshop day, supervisor assigned

**Steps:**
1. Supervisor opens check-in page → System shows today's workshops → Select workshop
2. Supervisor starts QR scanner → Camera permission requested → Scanner ready
3. Attendee shows QR code → Supervisor scans code → System validates code
4. System checks registration → Verify workshop match → Check if already checked
5. System marks attendance → Update attendance status → Show success message
6. Counter updates in real-time → Show checked/total → Progress bar
7. For manual check-in → Enter national ID → Search registration → Mark attendance
8. At workshop end → Generate attendance report → Export to Excel

**Database Changes:**
- Table: workshop_registrations | Action: UPDATE | Fields: attendance=true, checked_in_at
- Table: workshops | Action: UPDATE | Fields: attended_count

**Notifications:**
- In-app: "Check-in successful" (per scan)
- Email to: Admin | Template: attendance_report | Trigger: Workshop end

**Error Scenarios:**
1. Error: Invalid QR | Handling: Show error | Message: "رمز غير صالح / Invalid QR code"
2. Error: Wrong workshop | Handling: Show workshop name | Message: "رمز لورشة أخرى / QR for different workshop"
3. Error: Already checked | Handling: Show time | Message: "تم التسجيل في [time] / Already checked in at [time]"
4. Error: Not registered | Handling: Offer manual | Message: "غير مسجل / Not registered"

**Success Criteria:**
- Attendance marked in database
- Real-time count updates
- Report generated accurately

---

## 8. NEWS PUBLICATION WITH TWITTER

### Workflow: Publish News with Auto-Tweet
**Actor:** Hackathon Admin
**Goal:** Publish news and auto-post to Twitter/X
**Preconditions:** Admin logged in, Twitter API configured

**Steps:**
1. Admin clicks "Add News" → System shows news form → Empty form ready
2. Admin enters title (Arabic & English) → Validate length for tweet → Show preview
3. Admin enters content → Rich text editor → Add images if needed
4. Admin uploads feature image → Validate size/format → Show preview
5. Admin enables "Post to Twitter" → System checks API connection → Show Twitter preview
6. Admin clicks "Publish" → System saves news → Generate news page
7. System prepares tweet → Truncate if needed → Include link to full news
8. System posts to Twitter → Via API call → Get tweet ID
9. System stores tweet ID → Link news to tweet → Show success
10. News appears on public site → Tweet visible on Twitter → Cross-linked

**Database Changes:**
- Table: news | Action: INSERT | Fields: title_ar, title_en, content, image, published_at
- Table: news | Action: UPDATE | Fields: tweet_id, tweet_url
- Table: activity_log | Action: INSERT | Fields: action='news_published'

**Notifications:**
- Email to: Subscribers | Template: news_update | Trigger: On publish
- Twitter: Auto-post with link
- In-app: "News published successfully"

**Error Scenarios:**
1. Error: Twitter API fail | Handling: Save news anyway | Message: "فشل النشر على تويتر / Twitter post failed"
2. Error: Image too large | Handling: Compress or reject | Message: "حجم الصورة كبير / Image too large"
3. Error: Title too long for tweet | Handling: Auto-truncate | Message: "سيتم اختصار العنوان / Title will be truncated"

**Success Criteria:**
- News saved and visible
- Tweet posted successfully
- Links work both ways

---

## 9. HACKATHON EDITION CREATION

### Workflow: Create New Hackathon Edition
**Actor:** System Admin
**Goal:** Set up new year's hackathon
**Preconditions:** System admin access

**Steps:**
1. Admin opens editions page → System shows all editions → Click "New Edition"
2. Admin enters year (e.g., 2025) → System checks uniqueness → Validate year
3. Admin sets edition name → Default: "Hackathon [year]" → Can customize
4. Admin sets key dates:
   - Registration open/close dates
   - Idea submission open/close dates
   - Event start/end dates
5. Admin can clone previous edition → Copy tracks, settings → Adjust dates
6. System creates edition structure → Initialize required tables → Set as draft
7. Admin assigns hackathon admin → Select from users → Grant permissions
8. Admin activates edition → Deactivate others → Make current
9. System switches to new edition → All new data under this → Archive old

**Database Changes:**
- Table: hackathons | Action: INSERT | Fields: year, name, dates, status
- Table: tracks | Action: INSERT | Fields: hackathon_id, name (if cloned)
- Table: settings | Action: INSERT | Fields: hackathon-specific settings

**Notifications:**
- Email to: Hackathon Admin | Template: edition_assigned | Trigger: On assignment
- System log: Edition created and activated

**Error Scenarios:**
1. Error: Year exists | Handling: Show existing | Message: "السنة موجودة / Year already exists"
2. Error: Invalid date order | Handling: Validate sequence | Message: "ترتيب التواريخ خاطئ / Date sequence invalid"
3. Error: No admin assigned | Handling: Require assignment | Message: "يجب تعيين مشرف / Must assign admin"

**Success Criteria:**
- Edition created and active
- All dates set correctly
- Admin can manage edition

---

## 10. COMPREHENSIVE REPORTING

### Workflow: Generate and Export Reports
**Actor:** Hackathon Admin
**Goal:** Generate comprehensive reports
**Preconditions:** Data exists in system

**Steps:**
1. Admin opens reports section → System shows report types → Select report
2. Admin selects report type:
   - Teams Report
   - Ideas Report
   - Workshop Attendance
   - Track Statistics
   - Overall Summary
3. Admin sets filters → Date range, track, status → Apply filters
4. System generates report → Process data → Show preview
5. Admin views charts/tables → Interactive elements → Drill-down available
6. Admin clicks "Export" → Choose format (Excel/PDF) → Generate file
7. System creates file → Include all data → Format properly
8. File downloads → Admin saves locally → Can share

**Database Changes:**
- Table: report_logs | Action: INSERT | Fields: type, filters, generated_by, timestamp

**Notifications:**
- In-app: "Report generated successfully"
- Email (optional): Send report to recipients

**Error Scenarios:**
1. Error: No data | Handling: Show empty state | Message: "لا توجد بيانات / No data available"
2. Error: Too much data | Handling: Paginate or limit | Message: "البيانات كثيرة / Too much data, applying limits"

**Success Criteria:**
- Report accurate and complete
- Export works correctly
- Format professional

---

## WORKFLOW DOCUMENTATION COMPLETE CHECKLIST
- ☐ Registration workflow complete
- ☐ Team creation workflow complete
- ☐ Member invitation workflow complete
- ☐ Idea submission workflow complete
- ☐ Review process workflow complete
- ☐ Workshop registration workflow complete
- ☐ Check-in process workflow complete
- ☐ News publication workflow complete
- ☐ Edition creation workflow complete
- ☐ Reporting workflow complete
- ☐ All database changes documented
- ☐ All notifications specified
- ☐ All error scenarios covered

---

## CRITICAL PATHS IDENTIFIED

### Must Work First:
1. User Registration → Login → Dashboard
2. Team Creation → Member Addition → Idea Submission
3. Workshop Creation → Registration → Check-in

### Can Be Parallel:
1. News Management
2. Reporting
3. System Settings

---

## NOTES
[Add any workflow-specific observations or dependencies]
