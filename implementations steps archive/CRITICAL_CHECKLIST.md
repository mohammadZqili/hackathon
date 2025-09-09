# 🔥 **CRITICAL IMPLEMENTATION CHECKLIST**
**Quick Reference Guide - Don't Miss These!**

## ⚠️ **CRITICAL ISSUES THAT WILL CAUSE FAILURE IF MISSED**

### **1. DATABASE FOREIGN KEY ORDER** 🚨
```sql
-- MUST CREATE IN THIS EXACT ORDER:
1. users
2. hackathon_editions  
3. tracks
4. teams
5. ideas
6. workshops
7. team_members (pivot)
8. idea_files
9. idea_reviews
10. workshop_registrations
11. workshop_attendances
```

### **2. ROLE HIERARCHY** 🚨
```php
// EXACT PRIORITY ORDER (NEVER CHANGE):
1. system_admin      // Full access
2. hackathon_admin   // Current edition only
3. track_supervisor  // Assigned track only
4. team_leader       // Own team only
5. team_member       // Read-only
```

### **3. MISSING ENV VARIABLES** 🚨
```bash
# ADD THESE TO .env OR SYSTEM WILL FAIL:
QUEUE_CONNECTION=redis
BROADCAST_DRIVER=pusher
CACHE_DRIVER=redis
SESSION_DRIVER=redis

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=mt1

TWITTER_CONSUMER_KEY=
TWITTER_CONSUMER_SECRET=
TWITTER_ACCESS_TOKEN=
TWITTER_ACCESS_TOKEN_SECRET=

MAIL_MAILER=smtp
MAIL_HOST=
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=
MAIL_FROM_NAME="${APP_NAME}"

FILESYSTEM_DISK=public
MAX_FILE_UPLOAD=15360  # 15MB in KB
MAX_FILES_PER_IDEA=8
```

---

## 🔴 **SYSTEM ADMIN CRITICAL PATHS**

### **Must-Have Pages**
```
✅ /system-admin/dashboard
✅ /system-admin/editions (CRUD)
✅ /system-admin/users (CRUD)
✅ /system-admin/settings/smtp
✅ /system-admin/settings/branding
✅ /system-admin/settings/twitter
✅ /system-admin/reports
```

### **Unique Permissions**
```php
// ONLY system_admin can:
- Create/delete hackathon editions
- Change user roles
- Access system settings
- View cross-edition data
- Manage Twitter integration
```

---

## 🟡 **HACKATHON ADMIN CRITICAL PATHS**

### **Must-Have Pages**
```
✅ /hackathon-admin/dashboard
✅ /hackathon-admin/teams (approve/reject)
✅ /hackathon-admin/ideas (review)
✅ /hackathon-admin/workshops (CRUD)
✅ /hackathon-admin/news (CRUD + Twitter)
```

### **Data Scoping**
```php
// ALWAYS filter by current edition:
$currentEdition = auth()->user()->managed_hackathon_id;
$teams = Team::where('hackathon_id', $currentEdition)->get();
```

---

## 🟢 **TRACK SUPERVISOR CRITICAL PATHS**

### **Must-Have Pages**
```
✅ /track-supervisor/dashboard
✅ /track-supervisor/ideas (review only assigned track)
✅ /track-supervisor/workshops
```

### **Data Scoping**
```php
// ALWAYS filter by assigned track:
$assignedTrack = auth()->user()->track_id;
$ideas = Idea::whereHas('team', function($q) use ($assignedTrack) {
    $q->where('track_id', $assignedTrack);
})->get();
```

---

## 🔵 **TEAM LEADER CRITICAL PATHS**

### **Must-Have Pages**
```
✅ /team-leader/dashboard
✅ /team-leader/team (edit own team)
✅ /team-leader/idea (submit/edit)
```

### **Permissions Check**
```php
// ALWAYS verify team ownership:
if ($team->leader_id !== auth()->id()) {
    abort(403);
}
```

---

## 🟣 **TEAM MEMBER CRITICAL PATHS**

### **Must-Have Pages**
```
✅ /team-member/dashboard (read-only)
✅ /team-member/team (view only)
✅ /team-member/workshops (register)
```

### **No Edit Rights**
```javascript
// Frontend: Hide all edit buttons
v-if="userRole !== 'team_member'"

// Backend: Block all updates
if (auth()->user()->hasRole('team_member')) {
    abort(403, 'View only access');
}
```

---

## 📱 **ARABIC/RTL SUPPORT CHECKLIST**

### **Backend**
```php
// app/Http/Middleware/SetLocale.php
✅ Detect browser language
✅ Set Carbon locale
✅ Load correct translation files
```

### **Frontend**
```vue
<!-- Check EVERY component -->
✅ :dir="isRTL ? 'rtl' : 'ltr'"
✅ Replace ml- with mr- classes
✅ Replace left with right positions
✅ Use Cairo/Tajawal fonts
✅ Flip icons if needed
```

### **Database**
```sql
-- ALL text fields must be:
✅ COLLATE utf8mb4_unicode_ci
✅ Support Arabic characters
```

---

## 🔒 **SECURITY CHECKLIST**

### **Authentication**
```php
✅ 2FA for admin roles
✅ Password complexity rules
✅ Session timeout after 30 minutes
✅ Login attempts throttling
```

### **Authorization**
```php
✅ Check permissions in EVERY controller method
✅ Use policies for model access
✅ Validate file uploads (type, size)
✅ Sanitize all user inputs
```

### **API Security**
```php
✅ Rate limiting per role
✅ API versioning (/api/v1/)
✅ CORS configuration
✅ Security headers middleware
```

---

## 🚀 **PERFORMANCE CHECKLIST**

### **Database**
```sql
✅ Index foreign keys
✅ Index status fields
✅ Index datetime fields
✅ Use eager loading
✅ Avoid N+1 queries
```

### **Caching**
```php
✅ Cache dashboard stats (5 min)
✅ Cache user permissions
✅ Cache team lists
✅ Clear cache on updates
```

### **Frontend**
```javascript
✅ Lazy load components
✅ Use pagination (20 items)
✅ Debounce search (300ms)
✅ Compress images
✅ Minify JS/CSS
```

---

## 📊 **WEBSOCKET EVENTS**

### **Must Broadcast**
```javascript
// Team events
✅ team.status.updated
✅ team.member.joined
✅ team.member.left

// Idea events
✅ idea.submitted
✅ idea.reviewed
✅ idea.status.changed

// Workshop events
✅ workshop.registration.opened
✅ workshop.capacity.reached
```

---

## 📧 **EMAIL TEMPLATES REQUIRED**

### **Critical Emails**
```
✅ welcome.blade.php          // User registration
✅ team-approved.blade.php     // Team approval
✅ team-rejected.blade.php     // Team rejection
✅ idea-reviewed.blade.php     // Idea feedback
✅ workshop-reminder.blade.php // Workshop reminder
✅ password-reset.blade.php    // Password reset
```

---

## 🧪 **TESTING PRIORITIES**

### **Must Test**
```php
// Authentication
✅ Each role can login
✅ Correct dashboard redirect
✅ Menu shows correct items

// Authorization
✅ Role boundaries work
✅ Data scoping works
✅ 403 on unauthorized access

// Critical Flows
✅ Team creation
✅ Idea submission
✅ Workshop registration
✅ QR code scanning
```

---

## 📦 **QUEUE JOBS**

### **Must Queue**
```php
✅ Email sending
✅ Export generation
✅ Twitter posting
✅ Workshop reminders
✅ File processing
```

### **Queue Workers**
```bash
# MUST RUN IN PRODUCTION:
php artisan queue:work --sleep=3 --tries=3 --max-time=3600
```

---

## 🚢 **DEPLOYMENT CHECKLIST**

### **Pre-Deploy**
```bash
✅ composer install --no-dev --optimize-autoloader
✅ npm run build
✅ php artisan config:cache
✅ php artisan route:cache
✅ php artisan view:cache
✅ php artisan optimize
```

### **Post-Deploy**
```bash
✅ php artisan migrate --force
✅ php artisan db:seed --class=ProductionSeeder
✅ php artisan storage:link
✅ php artisan queue:restart
✅ php artisan websockets:restart
```

### **Permissions**
```bash
✅ chmod -R 755 storage
✅ chmod -R 755 bootstrap/cache
✅ chown -R www-data:www-data storage
✅ chown -R www-data:www-data bootstrap/cache
```

---

## 🔥 **COMMON MISTAKES TO AVOID**

### **Database**
```
❌ Don't forget to add indexes
❌ Don't use MyISAM (use InnoDB)
❌ Don't forget soft deletes
❌ Don't forget timestamps
```

### **Security**
```
❌ Don't trust user input
❌ Don't expose .env file
❌ Don't commit API keys
❌ Don't skip CSRF protection
```

### **Performance**
```
❌ Don't load all records (paginate)
❌ Don't forget to cache
❌ Don't use sync jobs for emails
❌ Don't forget to optimize images
```

### **Frontend**
```
❌ Don't hardcode API URLs
❌ Don't forget loading states
❌ Don't forget error handling
❌ Don't forget mobile responsive
```

---

## 📱 **MOBILE RESPONSIVE BREAKPOINTS**

```css
/* Use these exact breakpoints */
sm: 640px   /* Mobile landscape */
md: 768px   /* Tablet portrait */
lg: 1024px  /* Tablet landscape */
xl: 1280px  /* Desktop */
2xl: 1536px /* Large desktop */

/* Critical mobile fixes */
✅ Sidebar must be collapsible
✅ Tables must scroll horizontally
✅ Forms must be single column on mobile
✅ Modals must be full screen on mobile
```

---

## 🎯 **FINAL VERIFICATION CHECKLIST**

Before going live, verify:

### **Roles & Permissions**
- [ ] All 5 roles can login
- [ ] Each sees correct menu
- [ ] Data scoping works
- [ ] Unauthorized = 403

### **Critical Features**
- [ ] Team creation works
- [ ] Idea submission works
- [ ] Workshop registration works
- [ ] QR scanning works
- [ ] Email sending works

### **Arabic Support**
- [ ] RTL layout works
- [ ] Arabic fonts load
- [ ] Translations complete
- [ ] Date formats correct

### **Performance**
- [ ] Page load < 2 seconds
- [ ] API response < 200ms
- [ ] No N+1 queries
- [ ] Caching enabled

### **Security**
- [ ] HTTPS enabled
- [ ] Headers set
- [ ] CORS configured
- [ ] Rate limiting active

### **Monitoring**
- [ ] Error logging works
- [ ] Audit trail active
- [ ] Backups configured
- [ ] Monitoring alerts set

---

## 💀 **IF SOMETHING BREAKS**

### **Quick Fixes**
```bash
# Clear everything
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear

# Rebuild
composer dump-autoload
php artisan config:cache
php artisan route:cache
npm run build

# Reset database (DEVELOPMENT ONLY)
php artisan migrate:fresh --seed
```

### **Debug Mode**
```php
// .env (NEVER in production)
APP_DEBUG=true
APP_ENV=local
LOG_LEVEL=debug
```

### **Common Errors**
```
"Class not found" → composer dump-autoload
"Route not found" → php artisan route:clear
"Permission denied" → Check file permissions
"419 error" → CSRF token issue
"500 error" → Check logs in storage/logs
```

---

**⚡ USE THIS CHECKLIST DAILY TO ENSURE NOTHING IS MISSED!**
