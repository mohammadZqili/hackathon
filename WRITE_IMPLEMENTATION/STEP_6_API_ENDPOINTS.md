# STEP 6: API ENDPOINTS & VALIDATION
## Complete API Documentation with Validation Rules

---

## 📋 INSTRUCTIONS
Document every API endpoint with exact request/response format and validation rules.

---

## ENDPOINT TEMPLATE:
```
### Endpoint: [Name]
**Method:** [GET/POST/PUT/DELETE]
**URL:** /api/[path]
**Auth:** Required/Optional
**Role:** [Required role(s)]
**Rate Limit:** [If applicable]

**Request Headers:**
- Authorization: Bearer {token}
- Content-Type: application/json

**Request Body:**
{
  "field": "type // validation rules"
}

**Validation Rules:**
- field: rule1|rule2|rule3

**Success Response (200):**
{
  "data": {},
  "message": ""
}

**Error Responses:**
- 422: Validation Error
- 401: Unauthorized
- 403: Forbidden
- 404: Not Found
```

---

# AUTHENTICATION ENDPOINTS

## 1. User Registration

### Endpoint: Register User
**Method:** POST
**URL:** /api/register
**Auth:** Not required
**Rate Limit:** 5 requests per minute per IP

**Request Body:**
```json
{
  "name": "string // required|string|max:255",
  "email": "email // required|email|unique:users",
  "phone": "string // required|regex:/^05[0-9]{8}$/",
  "national_id": "string // required|digits:10|unique:users",
  "birth_date": "date // required|date|before:today",
  "occupation": "string // required|in:student,employee",
  "job_title": "string // required_if:occupation,employee|max:100",
  "role": "string // required|in:visitor,team_leader,team_member",
  "password": "string // required|min:8|confirmed",
  "password_confirmation": "string // required"
}
```

**Validation Messages:**
```php
[
  'email.unique' => 'البريد الإلكتروني مسجل مسبقاً / Email already registered',
  'national_id.unique' => 'رقم الهوية مسجل مسبقاً / National ID already registered',
  'phone.regex' => 'رقم الجوال يجب أن يبدأ بـ 05 / Phone must start with 05',
  'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل / Password must be at least 8 characters'
]
```

**Success Response (201):**
```json
{
  "data": {
    "user": {
      "id": 1,
      "name": "أحمد محمد",
      "email": "ahmad@example.com",
      "role": "team_leader"
    },
    "token": "eyJ0eXAiOiJKV1QiLCJhbGc..."
  },
  "message": "تم التسجيل بنجاح / Registration successful"
}
```

---

## 2. User Login

### Endpoint: Login
**Method:** POST
**URL:** /api/login
**Auth:** Not required
**Rate Limit:** 10 attempts per 15 minutes

**Request Body:**
```json
{
  "email": "email // required|email",
  "password": "string // required"
}
```

**Success Response (200):**
```json
{
  "data": {
    "user": {
      "id": 1,
      "name": "أحمد محمد",
      "role": "team_leader",
      "team": {
        "id": 5,
        "name": "Green Team"
      }
    },
    "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "redirect_to": "/team-leader/dashboard"
  },
  "message": "تم تسجيل الدخول / Login successful"
}
```

---

# TEAM MANAGEMENT ENDPOINTS

## 3. Create Team

### Endpoint: Create Team
**Method:** POST
**URL:** /api/team-leader/team
**Auth:** Required
**Role:** team_leader

**Request Body:**
```json
{
  "name": "string // required|unique:teams|min:3|max:100",
  "description": "string // nullable|max:500",
  "track_id": "integer // required|exists:tracks,id"
}
```

**Business Rules:**
- User can only create ONE team
- Registration period must be open
- Track must belong to current hackathon

**Success Response (201):**
```json
{
  "data": {
    "id": 1,
    "name": "Innovation Squad",
    "code": "TEAM-A4B2",
    "track": {
      "id": 2,
      "name": "Environment Track"
    },
    "leader": {
      "id": 1,
      "name": "أحمد محمد"
    },
    "members_count": 1,
    "created_at": "2025-01-15T10:00:00Z"
  },
  "message": "تم إنشاء الفريق بنجاح / Team created successfully"
}
```

**Error Response (409):**
```json
{
  "error": "لديك فريق بالفعل / You already have a team",
  "existing_team": {
    "id": 3,
    "name": "Previous Team"
  }
}
```

---

## 4. Invite Team Member

### Endpoint: Invite Member
**Method:** POST
**URL:** /api/team-leader/team/invite
**Auth:** Required
**Role:** team_leader

**Request Body:**
```json
{
  "email": "email // required_without:national_id|email|exists:users,email",
  "national_id": "string // required_without:email|digits:10|exists:users,national_id"
}
```

**Business Rules:**
- Maximum 5 members per team (including leader)
- User must not be in another team
- Cannot invite self

**Success Response (200):**
```json
{
  "data": {
    "invitation": {
      "id": 12,
      "team_id": 1,
      "invited_user": {
        "id": 5,
        "name": "سارة أحمد",
        "email": "sara@example.com"
      },
      "status": "pending",
      "expires_at": "2025-01-17T10:00:00Z"
    }
  },
  "message": "تم إرسال الدعوة / Invitation sent"
}
```

---

## 5. Join Team Request

### Endpoint: Request to Join Team
**Method:** POST
**URL:** /api/team-member/team/join
**Auth:** Required
**Role:** team_member

**Request Body:**
```json
{
  "team_code": "string // required|exists:teams,code",
  "message": "string // nullable|max:200"
}
```

**Success Response (200):**
```json
{
  "data": {
    "request": {
      "id": 8,
      "team": {
        "id": 1,
        "name": "Innovation Squad",
        "leader_name": "أحمد محمد"
      },
      "status": "pending",
      "message": "أود الانضمام لفريقكم"
    }
  },
  "message": "تم إرسال طلب الانضمام / Join request sent"
}
```

---

# IDEA MANAGEMENT ENDPOINTS

## 6. Submit Idea

### Endpoint: Submit Idea
**Method:** POST
**URL:** /api/team-leader/idea
**Auth:** Required
**Role:** team_leader

**Request Body:**
```json
{
  "title": "string // required|min:10|max:200",
  "description": "string // required|min:100|max:5000",
  "files": "array // nullable|max:8",
  "submit_for_review": "boolean // required"
}
```

**File Upload (Multipart):**
```
POST /api/team-leader/idea/upload
Content-Type: multipart/form-data

file: (binary) // required|file|mimes:pdf,ppt,pptx,doc,docx|max:15360
```

**Success Response (201):**
```json
{
  "data": {
    "idea": {
      "id": 1,
      "title": "Smart Recycling System",
      "status": "pending",
      "team_id": 1,
      "track_id": 2,
      "files_count": 3,
      "submitted_at": "2025-01-15T14:00:00Z"
    }
  },
  "message": "تم إرسال الفكرة للمراجعة / Idea submitted for review"
}
```

---

## 7. Review Idea (Supervisor)

### Endpoint: Review Idea
**Method:** POST
**URL:** /api/track-supervisor/ideas/{id}/review
**Auth:** Required
**Role:** track_supervisor

**Request Body:**
```json
{
  "decision": "string // required|in:approved,rejected,needs_revision",
  "scores": {
    "innovation": "integer // required|min:0|max:20",
    "feasibility": "integer // required|min:0|max:20",
    "impact": "integer // required|min:0|max:20",
    "presentation": "integer // required|min:0|max:20",
    "team_capability": "integer // required|min:0|max:20"
  },
  "feedback": "string // required|min:50|max:2000",
  "private_notes": "string // nullable|max:500"
}
```

**Success Response (200):**
```json
{
  "data": {
    "review": {
      "id": 1,
      "idea_id": 1,
      "decision": "approved",
      "total_score": 85,
      "feedback": "فكرة ممتازة ومبتكرة...",
      "reviewed_at": "2025-01-16T10:00:00Z"
    }
  },
  "message": "تم حفظ المراجعة / Review saved"
}
```

---

# WORKSHOP ENDPOINTS

## 8. Register for Workshop

### Endpoint: Workshop Registration
**Method:** POST
**URL:** /api/workshops/{id}/register
**Auth:** Required
**Role:** any

**Request Body:**
```json
{
  "dietary_requirements": "string // nullable|max:200",
  "accessibility_needs": "string // nullable|max:200"
}
```

**Success Response (201):**
```json
{
  "data": {
    "registration": {
      "id": 45,
      "workshop": {
        "id": 3,
        "title": "AI in Environmental Solutions",
        "date": "2025-02-01T14:00:00Z",
        "location": "Hall A"
      },
      "qr_code": "WS-2025-045-A3B4C5",
      "qr_image": "data:image/png;base64,iVBORw0KGgoAAAA..."
    }
  },
  "message": "تم التسجيل بنجاح / Registration successful"
}
```

---

## 9. Check-in Attendance

### Endpoint: Workshop Check-in
**Method:** POST
**URL:** /api/workshop-supervisor/checkin
**Auth:** Required
**Role:** workshop_supervisor

**Request Body:**
```json
{
  "qr_code": "string // required|exists:workshop_registrations,qr_code",
  "workshop_id": "integer // required|exists:workshops,id"
}
```

**Success Response (200):**
```json
{
  "data": {
    "attendee": {
      "name": "أحمد محمد",
      "email": "ahmad@example.com",
      "checked_in_at": "2025-02-01T14:05:00Z"
    },
    "workshop": {
      "total_registered": 50,
      "total_attended": 35,
      "attendance_rate": "70%"
    }
  },
  "message": "تم تسجيل الحضور / Attendance recorded"
}
```

---

# ADMIN ENDPOINTS

## 10. Create Workshop (Admin)

### Endpoint: Create Workshop
**Method:** POST
**URL:** /api/hackathon-admin/workshops
**Auth:** Required
**Role:** hackathon_admin

**Request Body:**
```json
{
  "title_ar": "string // required|max:200",
  "title_en": "string // required|max:200",
  "description_ar": "string // required|max:2000",
  "description_en": "string // required|max:2000",
  "date_time": "datetime // required|after:now",
  "duration_minutes": "integer // required|min:30|max:480",
  "max_seats": "integer // required|min:10|max:500",
  "venue_id": "integer // required|exists:venues,id",
  "speaker_ids": "array // required|min:1",
  "organization_ids": "array // nullable",
  "registration_deadline": "datetime // required|before:date_time"
}
```

**Success Response (201):**
```json
{
  "data": {
    "workshop": {
      "id": 10,
      "title": "AI in Environmental Solutions",
      "date_time": "2025-02-01T14:00:00Z",
      "speakers": [
        {
          "id": 1,
          "name": "Dr. Sarah Johnson",
          "title": "AI Researcher"
        }
      ],
      "organizations": [
        {
          "id": 1,
          "name": "Tech Institute",
          "logo": "/logos/tech-institute.png"
        }
      ],
      "registration_open": true,
      "seats_available": 100
    }
  },
  "message": "تم إنشاء الورشة / Workshop created"
}
```

---

## 11. Dashboard Statistics

### Endpoint: Get Dashboard Stats
**Method:** GET
**URL:** /api/{role}/dashboard
**Auth:** Required
**Role:** varies by endpoint

**Query Parameters:**
```
?period=week|month|all // default: all
&hackathon_id=1 // optional, defaults to current
```

**Success Response (200):**
```json
{
  "data": {
    "statistics": {
      "teams": {
        "total": 45,
        "active": 40,
        "with_ideas": 35
      },
      "ideas": {
        "total": 35,
        "pending": 10,
        "approved": 20,
        "rejected": 3,
        "needs_revision": 2
      },
      "participants": {
        "total": 180,
        "by_role": {
          "team_leader": 45,
          "team_member": 135
        }
      },
      "workshops": {
        "total": 12,
        "upcoming": 8,
        "completed": 4,
        "total_attendance": 320
      }
    },
    "charts": {
      "registration_timeline": [...],
      "ideas_by_track": [...],
      "daily_activity": [...]
    }
  }
}
```

---

## 12. Export Reports

### Endpoint: Export Report
**Method:** POST
**URL:** /api/hackathon-admin/reports/export
**Auth:** Required
**Role:** hackathon_admin

**Request Body:**
```json
{
  "type": "string // required|in:teams,ideas,workshops,participants,full",
  "format": "string // required|in:excel,pdf,csv",
  "filters": {
    "track_id": "integer // nullable",
    "status": "string // nullable",
    "date_from": "date // nullable",
    "date_to": "date // nullable"
  },
  "include": {
    "statistics": "boolean // default: true",
    "charts": "boolean // default: false",
    "detailed_data": "boolean // default: true"
  }
}
```

**Success Response (200):**
```json
{
  "data": {
    "download_url": "/downloads/reports/report-2025-01-15-teams.xlsx",
    "expires_at": "2025-01-15T15:00:00Z",
    "file_size": "245KB",
    "rows_count": 450
  },
  "message": "تم إنشاء التقرير / Report generated"
}
```

---

# NOTIFICATION ENDPOINTS

## 13. Send Notification

### Endpoint: Send Notification
**Method:** POST
**URL:** /api/notifications/send
**Auth:** Required
**Role:** varies

**Request Body:**
```json
{
  "recipient_type": "string // required|in:user,team,track,all",
  "recipient_id": "integer // required_unless:recipient_type,all",
  "type": "string // required|in:email,sms,in_app",
  "subject": "string // required|max:200",
  "message": "string // required|max:1000",
  "priority": "string // required|in:low,normal,high,urgent"
}
```

---

# SEARCH & FILTER ENDPOINTS

## 14. Search Teams

### Endpoint: Search Teams
**Method:** GET
**URL:** /api/search/teams
**Auth:** Required

**Query Parameters:**
```
?q=search_term // Search in team name, leader name
&track_id=1 // Filter by track
&has_idea=true // Teams with submitted ideas
&status=active // active, inactive
&sort=name|created_at|members_count // default: name
&order=asc|desc // default: asc
&page=1 // Pagination
&per_page=20 // Items per page
```

---

# FILE MANAGEMENT ENDPOINTS

## 15. Upload File

### Endpoint: Upload File
**Method:** POST
**URL:** /api/files/upload
**Auth:** Required

**Request (Multipart):**
```
Content-Type: multipart/form-data

file: (binary) // required
type: idea_attachment|profile_photo|news_image // required
entity_id: 1 // ID of related entity
```

**Validation by Type:**
```php
'idea_attachment' => 'mimes:pdf,ppt,pptx,doc,docx|max:15360',
'profile_photo' => 'image|mimes:jpeg,png,jpg|max:2048',
'news_image' => 'image|mimes:jpeg,png,jpg,webp|max:5120'
```

---

## API DOCUMENTATION COMPLETE CHECKLIST
- ☐ Authentication endpoints documented
- ☐ Team management endpoints documented
- ☐ Idea submission endpoints documented
- ☐ Workshop endpoints documented
- ☐ Admin endpoints documented
- ☐ All validation rules specified
- ☐ All error responses defined
- ☐ Rate limiting defined
- ☐ File upload constraints specified
- ☐ Business rules documented

---

## GLOBAL ERROR RESPONSES

### 401 Unauthorized
```json
{
  "error": "غير مصرح / Unauthorized",
  "message": "يجب تسجيل الدخول / Please login"
}
```

### 403 Forbidden
```json
{
  "error": "غير مسموح / Forbidden",
  "message": "ليس لديك صلاحية / You don't have permission"
}
```

### 404 Not Found
```json
{
  "error": "غير موجود / Not Found",
  "message": "المورد المطلوب غير موجود / Resource not found"
}
```

### 422 Validation Error
```json
{
  "message": "البيانات المدخلة غير صحيحة / Invalid input data",
  "errors": {
    "field_name": [
      "Error message 1",
      "Error message 2"
    ]
  }
}
```

### 429 Too Many Requests
```json
{
  "error": "طلبات كثيرة / Too Many Requests",
  "message": "حاول مرة أخرى بعد 60 ثانية / Try again after 60 seconds",
  "retry_after": 60
}
```

### 500 Server Error
```json
{
  "error": "خطأ في الخادم / Server Error",
  "message": "حدث خطأ، حاول مرة أخرى / An error occurred, please try again"
}
```

---

## NOTES
[Add any API-specific implementation notes or considerations]
