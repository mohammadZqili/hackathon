# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

GuacPanel Hackathon Management System - A multi-role Laravel 11 application with Vue.js 3, Inertia.js, and Tailwind CSS 4 for managing hackathon events, teams, ideas, and workshops.

## Tech Stack

- **Backend**: Laravel 11, PHP 8.2+
- **Frontend**: Vue.js 3, Inertia.js, Tailwind CSS 4
- **Database**: MySQL/SQLite
- **Build Tools**: Vite 6
- **Authentication**: Laravel Fortify, Sanctum
- **Permissions**: Spatie Laravel Permission
- **Testing**: PHPUnit, Pest
- **Search**: Typesense Scout Driver
- **File Management**: FilePond
- **Rich Text**: TipTap Editor
- **Charts**: ApexCharts

## Development Commands

### Essential Commands
```bash
# Development servers (run both)
npm run dev                           # Start Vite dev server (hot reload)
php artisan serve                     # Start Laravel server

# Database
php artisan migrate                   # Run migrations
php artisan db:seed                   # Seed database
php artisan migrate:fresh --seed      # Reset and reseed database

# Build for production
npm run build                         # Build frontend assets
php artisan config:cache              # Cache configuration
php artisan route:cache               # Cache routes

# Testing
vendor/bin/phpunit                   # Run PHPUnit tests
vendor/bin/pest                      # Run Pest tests
php artisan test                     # Run application tests

# Cache management
php artisan cache:clear              # Clear application cache
php artisan config:clear             # Clear config cache
php artisan view:clear               # Clear view cache
```

## Architecture

### CRITICAL: MUST FOLLOW Controller -> Service -> Repository -> Model Pattern

**THIS IS MANDATORY FOR ALL FEATURES - NO EXCEPTIONS**

The application uses a strict layered architecture:

1. **Controllers** (app/Http/Controllers/)
   - ONLY handle HTTP requests/responses
   - MUST call Service methods for ALL business logic
   - Return Inertia views or JSON responses
   - NO direct database queries
   - NO business logic

2. **Services** (app/Services/)
   - Contains ALL business logic
   - MUST call Repository methods for data access
   - Handle database transactions
   - Process and transform data
   - Validate business rules

3. **Repositories** (app/Repositories/)
   - ONLY database queries and data access
   - NO business logic
   - Return Eloquent models or collections
   - Extend BaseRepository

4. **Models** (app/Models/)
   - Database table representation
   - Define relationships
   - Casts and mutators only
   - NO business logic

### Multi-Role System
Seven distinct user roles with specific permissions:
1. **system_admin**: Full system control
2. **hackathon_admin**: Edition management
3. **track_supervisor**: Track and team oversight
4. **workshop_supervisor**: Workshop management
5. **team_leader**: Team management
6. **team_member**: Idea submission and collaboration
7. **visitor**: Workshop attendance only

### Frontend Structure
- **Pages**: Role-specific Vue components in `resources/js/Pages/{Role}/`
- **Layouts**: Default, Auth, and Public layouts
- **Components**: Reusable components in `resources/js/Components/`
- **Composables**: Shared logic in `resources/js/Composables/`

## Key Development Patterns

### Controller Pattern
```php
public function index(Request $request)
{
    return Inertia::render('RoleName/PageName/Index', [
        'data' => $this->service->getData($request),
        'filters' => $request->only(['search', 'status'])
    ]);
}
```

### Service Pattern
```php
class EntityService extends BaseService implements EntityServiceInterface
{
    public function __construct(EntityRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
```

### Vue Page Pattern
```vue
<script setup>
import { useForm } from '@inertiajs/vue3'
import Default from '@/Layouts/Default.vue'

const props = defineProps({
    data: Object
})

const form = useForm({
    field: props.data?.field || ''
})

const submit = () => {
    form.post(route('route.name'))
}
</script>
```

### Dynamic Theme System
All pages must use dynamic theme colors:
```javascript
const themeColor = ref({
    primary: '#0d9488',
    hover: '#0f766e',
    rgb: '13, 148, 136'
})

onMounted(() => {
    const root = document.documentElement
    themeColor.value.primary = getComputedStyle(root)
        .getPropertyValue('--primary-color').trim() || '#0d9488'
})
```

## Database Conventions

### Migrations
- Use descriptive names: `2024_01_01_000000_create_entity_table.php`
- Always include timestamps and soft deletes where appropriate
- Foreign keys should cascade on delete when appropriate

### Models
- Use singular names: `User`, `Team`, `Idea`
- Define relationships clearly
- Use casts for JSON fields and dates
- Implement soft deletes where needed

## API Routes Pattern

### Role-based routing structure:
```php
Route::prefix('system-admin')->name('system-admin.')
    ->middleware(['auth', 'role:system_admin'])
    ->group(function () {
        Route::resource('editions', EditionController::class);
    });
```

## Settings System

### Database Key-Value Store
Settings stored in `system_settings` table:
- Access via `Settings::get('key')` helper
- Boolean values stored as '1' or '0' strings
- Cached for 1 hour, cleared on updates

### Form Handling
```javascript
const form = useForm({
    setting_key: props.settings?.setting_key || defaultValue
})
```

## Important Files & Locations

### Configuration
- `.env` - Environment variables
- `config/` - Laravel configuration
- `database/migrations/` - Database schema
- `database/seeders/` - Initial data

### Role-Specific Code
- `app/Http/Controllers/{Role}/` - Role controllers
- `resources/js/Pages/{Role}/` - Role Vue pages
- `app/Services/` - Business logic services
- `app/Repositories/` - Data access repositories

### Design References
- `design_files/vue_files_tailwind/` - Template references
- `design_files/figma_images/` - Design mockups

## Common Pitfalls to Avoid

1. **Never hardcode colors** - Use theme system variables
2. **Always check user permissions** - Use middleware and gates
3. **Use existing components** - Don't recreate FilePondUploader, RichTextEditor, etc.
4. **Follow role hierarchy** - Respect the multi-tenant edition isolation
5. **Use transactions** - Wrap multi-table updates in DB transactions
6. **Cache carefully** - Clear caches after settings/config changes

## Testing Approach

### Unit Tests
- Test services and repositories independently
- Mock external dependencies
- Focus on business logic

### Feature Tests
- Test complete user flows
- Include authentication and authorization
- Test with different user roles

### Run specific tests:
```bash
php artisan test --filter=EditionTest
vendor/bin/pest --filter "can create edition"
```

## Debugging Tips

### Common Issues
1. **403 Forbidden**: Check user roles and permissions
2. **419 Page Expired**: CSRF token issue, check session config
3. **500 Server Error**: Check Laravel logs in `storage/logs/`
4. **Vite connection failed**: Ensure `npm run dev` is running

### Useful Commands
```bash
php artisan route:list --name=system-admin  # List specific routes
php artisan tinker                          # Interactive shell
php artisan db:show                         # Database info
tail -f storage/logs/laravel.log           # Watch logs
```

## Deployment Considerations

1. Run `npm run build` for production assets
2. Set `APP_ENV=production` and `APP_DEBUG=false`
3. Generate application key: `php artisan key:generate`
4. Cache configuration: `php artisan config:cache`
5. Cache routes: `php artisan route:cache`
6. Run migrations: `php artisan migrate --force`
7. Set proper file permissions on `storage/` and `bootstrap/cache/`

## External Documentation

- Design system in `/design_files/`
- Role definitions in `files/ROLES_AND_RESPONSIBILITIES.md`
- Implementation guides in `files/WRITE_IMPLEMENTATION/`