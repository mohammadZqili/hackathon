# Testing HackathonAdmin Access

## 1. Login Credentials
- **Email**: hackadmin@example.com
- **Password**: password

## 2. Test URLs
After logging in, try accessing these URLs:

### Test Page (Simple test)
- http://localhost:8000/hackathon-admin/test

### Dashboard
- http://localhost:8000/hackathon-admin/dashboard

### Teams Management
- http://localhost:8000/hackathon-admin/teams

### Ideas Management
- http://localhost:8000/hackathon-admin/ideas

## 3. If You Still Get 403 Error

Run this command to verify the user has the correct role:

```bash
php artisan tinker
>>> $user = \App\Models\User::where('email', 'hackadmin@example.com')->first();
>>> $user->getRoleNames();
>>> exit
```

If the user doesn't have the role, assign it:

```bash
php artisan tinker
>>> $user = \App\Models\User::where('email', 'hackadmin@example.com')->first();
>>> $user->assignRole('hackathon-admin');
>>> exit
```

## 4. Clear All Caches
```bash
php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

## 5. Check Middleware
The custom middleware `CheckHackathonAdminRole` should allow access if the user has:
- `hackathon-admin` role
- `hackathon_admin` role
- `system-admin` role
- `system_admin` role

## 6. Alternative: Create New Test User
If the existing user doesn't work, create a new one:

```bash
php artisan tinker
>>> $user = \App\Models\User::create([
...     'name' => 'Test Hackathon Admin',
...     'email' => 'testhackadmin@test.com',
...     'password' => bcrypt('password'),
...     'edition_id' => 1,
... ]);
>>> $user->assignRole('hackathon-admin');
>>> exit
```

## 7. Debug Mode
If you still have issues, temporarily modify the middleware to see what's happening:

Edit `app/Http/Middleware/CheckHackathonAdminRole.php` and add debug output:

```php
public function handle(Request $request, Closure $next)
{
    if (!auth()->check()) {
        dd('Not logged in');
    }
    
    $user = auth()->user();
    dd([
        'user' => $user->email,
        'roles' => $user->getRoleNames()->toArray(),
        'has_hackathon_admin' => $user->hasRole('hackathon-admin'),
    ]);
    
    // Rest of the code...
}
```

This will show you exactly what roles the user has when accessing the route.