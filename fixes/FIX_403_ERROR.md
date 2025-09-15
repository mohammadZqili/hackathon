# Fix for 403 Error - HackathonAdmin Access

## Problem
Getting 403 Forbidden error when accessing `/hackathon-admin/*` routes even when logged in as hackathon admin.

## Solution Applied

### 1. Custom Middleware Created
Created custom middleware to handle role checking more flexibly:
- `app/Http/Middleware/CheckHackathonAdminRole.php`
- `app/Http/Middleware/CheckTrackSupervisorRole.php`

These middlewares check for multiple role variations (with hyphens and underscores).

### 2. Routes Updated
Updated route files to use custom middleware:
- `routes/hackathon-admin.php` - Uses `check_hackathon_admin` middleware
- `routes/track-supervisor.php` - Uses `check_track_supervisor` middleware

### 3. Test Routes Added
- `/hackathon-admin/test` - Test page for HackathonAdmin
- `/test-role` - Debug endpoint to check user roles

## How to Test

### Step 1: Clear All Caches
```bash
php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### Step 2: Check User Role
Visit `/test-role` after logging in to see:
- Your current roles
- Your permissions
- Role check results

### Step 3: Access HackathonAdmin Areas
Try these URLs:
1. `/hackathon-admin/test` - Simple test page
2. `/hackathon-admin/dashboard` - Dashboard
3. `/hackathon-admin/teams` - Teams management
4. `/hackathon-admin/ideas` - Ideas management

## If Still Getting 403

### Option 1: Check Your User's Role
```bash
php artisan tinker
>>> $user = \App\Models\User::where('email', 'your-email@example.com')->first();
>>> $user->getRoleNames();
```

### Option 2: Assign Role to Your User
```bash
php artisan tinker
>>> $user = \App\Models\User::where('email', 'your-email@example.com')->first();
>>> $user->assignRole('hackathon-admin');
>>> $user->update(['edition_id' => 1]); // Assign to edition 1
```

### Option 3: Create New Test User
```bash
php artisan tinker
>>> use App\Models\User;
>>> $user = User::create([
...     'name' => 'Hack Admin Test',
...     'email' => 'hacktest@example.com',
...     'password' => bcrypt('password'),
...     'edition_id' => 1,
... ]);
>>> $user->assignRole('hackathon-admin');
```

Then login with:
- Email: hacktest@example.com  
- Password: password

## How It Works

The custom middleware (`CheckHackathonAdminRole`) allows access if user has ANY of:
- `hackathon-admin` role (with hyphen)
- `hackathon_admin` role (with underscore)
- `system-admin` role (system admins can access everything)
- `system_admin` role

This handles the inconsistency in role naming between hyphen and underscore versions.

## Files Modified
1. `app/Http/Middleware/CheckHackathonAdminRole.php` - New custom middleware
2. `app/Http/Middleware/CheckTrackSupervisorRole.php` - New custom middleware
3. `app/Http/Kernel.php` - Registered new middleware
4. `routes/hackathon-admin.php` - Updated to use custom middleware
5. `routes/track-supervisor.php` - Updated to use custom middleware
6. `routes/web.php` - Added test route

## Quick Debug
If you need to debug, temporarily edit `app/Http/Middleware/CheckHackathonAdminRole.php`:

```php
public function handle(Request $request, Closure $next)
{
    $user = auth()->user();
    
    // Debug output - remove after testing
    dd([
        'user' => $user->email,
        'roles' => $user->getRoleNames()->toArray(),
        'has_hackathon-admin' => $user->hasRole('hackathon-admin'),
        'edition_id' => $user->edition_id,
    ]);
    
    // Rest of code...
}
```

This will show exactly what roles the user has when trying to access the route.