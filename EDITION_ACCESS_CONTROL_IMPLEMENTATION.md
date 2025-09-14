# Hackathon Admin Edition-Based Access Control Implementation

## Overview
This implementation ensures that Hackathon Admins can only see and manage data within their assigned edition(s). They cannot view or modify data from other editions.

## Implementation Steps

### 1. Database Setup
Run the migration to create the user-edition relationship table:
```bash
php artisan migrate --path=database/migrations/2025_09_11_add_edition_admin_relationship.php
```

### 2. Update User Model
Add the trait to the User model:
```php
// In app/Models/User.php
use App\Traits\HackathonAdminEditionTrait;

class User extends Authenticatable implements Auditable
{
    use HackathonAdminEditionTrait;
    // ... existing traits
}
```

### 3. Register Middleware
Update `app/Http/Kernel.php` to register the new middleware:
```php
protected $middlewareAliases = [
    // ... existing middleware
    'hackathon-admin-edition' => \App\Http\Middleware\HackathonAdminEditionMiddleware::class,
];
```

### 4. Update Routes
Replace the existing hackathon admin middleware in routes with the new one:
```php
// In routes/hackathon-admin.php
Route::middleware(['auth', 'hackathon-admin-edition'])->prefix('hackathon-admin')->name('hackathon-admin.')->group(function () {
    // ... routes
});
```

### 5. Update All Controllers
Apply the `HasEditionAccess` trait to all HackathonAdmin controllers:

#### Controllers to Update:
- ✅ IdeaController
- ✅ TeamController  
- ✅ TrackController
- ✅ WorkshopController
- ✅ NewsController
- ✅ DashboardController
- ✅ ReportController
- ✅ UserController
- ✅ CheckinController
- ✅ EditionController
- ✅ OrganizationController
- ✅ SpeakerController
- ✅ SettingsController
- ✅ QRScannerController

#### Key Changes in Each Controller:
1. Add `use App\Traits\HasEditionAccess;`
2. Add `use HasEditionAccess;` inside the class
3. Apply edition filter in index methods: `$query = $this->applyEditionFilter($query);`
4. Check access in show/edit/update/delete methods: `$this->authorizeEditionAccess($resource);`
5. Filter statistics by edition
6. Pass editions and currentEditionId to views

### 6. Update Vue Components
All Vue components need to be updated to handle edition context:

#### Components to Update:
- ✅ Dashboard.vue - Show edition-specific statistics
- ✅ Ideas/Index.vue - Filter by edition
- ✅ Teams/Index.vue - Filter by edition
- ✅ Tracks/Index.vue - Filter by edition
- ✅ Workshops/Index.vue - Filter by edition
- ✅ News/Index.vue - Filter by edition
- ✅ Reports/Index.vue - Generate edition-specific reports
- ✅ All Create/Edit forms - Include edition selection
- ✅ Layout component - Add edition switcher

### 7. Assign Editions to Hackathon Admins
Create a command or admin interface to assign editions:

```php
// Assign an edition to a hackathon admin
$user = User::find($userId);
$edition = Edition::find($editionId);

// Method 1: Set as primary admin
$edition->admin_id = $user->id;
$edition->save();

// Method 2: Assign via pivot table (for multiple editions)
$user->assignedEditions()->attach($editionId, ['role' => 'hackathon_admin']);
```

## Testing Checklist

### Authentication & Authorization
- [ ] Hackathon admin can only login and see their editions
- [ ] Cannot access other editions via URL manipulation
- [ ] 403 error when trying to access unauthorized edition

### Data Filtering
- [ ] Dashboard shows only data from assigned editions
- [ ] Ideas list shows only ideas from assigned editions
- [ ] Teams list shows only teams from assigned editions
- [ ] Tracks list shows only tracks from assigned editions
- [ ] Workshops list shows only workshops from assigned editions
- [ ] Reports only include data from assigned editions

### CRUD Operations
- [ ] Can create resources only in assigned editions
- [ ] Can edit resources only in assigned editions
- [ ] Can delete resources only in assigned editions
- [ ] Cannot modify resources from other editions

### Edition Switching
- [ ] Can switch between assigned editions
- [ ] Data updates when switching editions
- [ ] Current edition persists in session

### UI/UX
- [ ] Edition context is visible on all pages
- [ ] Edition switcher appears in navigation
- [ ] Forms pre-select current edition
- [ ] Export functions respect edition filter

## SQL Queries for Testing

```sql
-- Assign edition to hackathon admin
UPDATE editions SET admin_id = 'USER_ID_HERE' WHERE id = EDITION_ID;

-- Or use pivot table for multiple editions
INSERT INTO user_edition_assignments (user_id, edition_id, role) 
VALUES ('USER_ID_HERE', EDITION_ID, 'hackathon_admin');

-- Check user's accessible editions
SELECT e.* FROM editions e
WHERE e.admin_id = 'USER_ID_HERE'
OR EXISTS (
    SELECT 1 FROM user_edition_assignments uea 
    WHERE uea.user_id = 'USER_ID_HERE' 
    AND uea.edition_id = e.id
);
```

## Common Issues & Solutions

### Issue: Hackathon admin sees "No editions assigned"
**Solution:** Ensure the user is assigned to at least one edition via admin_id or user_edition_assignments table.

### Issue: Data from all editions still visible
**Solution:** Check that the `HasEditionAccess` trait is properly used and `applyEditionFilter()` is called in the controller methods.

### Issue: 403 errors on all pages
**Solution:** Verify the middleware is correctly checking editions and that the user has the 'hackathon_admin' role.

### Issue: Edition switcher not working
**Solution:** Ensure the session is properly storing the current_edition_id and the routes are correctly configured.

## Security Considerations

1. **Always validate edition access** in controllers, not just in middleware
2. **Use database constraints** to ensure referential integrity
3. **Log edition switches** for audit purposes
4. **Validate edition_id** in all form submissions
5. **Check cascade deletes** to prevent orphaned data

## Performance Optimization

1. **Index edition_id columns** in all tables for faster queries
2. **Cache user's accessible editions** to reduce database queries
3. **Use eager loading** for edition relationships
4. **Paginate results** to handle large datasets

```sql
-- Add indexes for better performance
ALTER TABLE teams ADD INDEX idx_edition_id (edition_id);
ALTER TABLE ideas ADD INDEX idx_edition_id (edition_id);
ALTER TABLE tracks ADD INDEX idx_edition_id (edition_id);
ALTER TABLE workshops ADD INDEX idx_hackathon_edition_id (hackathon_edition_id);
```

## Rollback Plan

If issues arise, you can rollback by:
1. Reverting the middleware changes
2. Removing the trait from controllers
3. Restoring original controller methods
4. Dropping the user_edition_assignments table

```bash
# Rollback migration
php artisan migrate:rollback --path=database/migrations/2025_09_11_add_edition_admin_relationship.php
```

## Next Steps

1. Implement admin interface for assigning editions to users
2. Add audit logging for all edition-based actions
3. Create automated tests for edition access control
4. Add edition-based permissions for finer control
5. Implement edition archiving functionality
