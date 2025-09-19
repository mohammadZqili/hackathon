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
**THIS PATTERN ENSURES CODE REUSABILITY AND MAINTAINABILITY**

Why this pattern is REQUIRED:
- **Services are REUSABLE** - Multiple controllers can use the same service
- **Repositories are REUSABLE** - Multiple services can use the same repository
- **Business logic is CENTRALIZED** - One place to update, affects all users
- **Testing is EASIER** - Each layer can be tested independently
- **Code is MAINTAINABLE** - Clear separation of concerns

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

## CRITICAL RULES - NEVER VIOLATE THESE

### 1. ALWAYS CHECK FOR EXISTING CODE FIRST
- **SEARCH for existing models** before creating new ones (e.g., Edition NOT HackathonEdition)
- **SEARCH for existing services** before creating new ones
- **SEARCH for existing repositories** before creating new ones
- **LOOK at similar files** in the same directory for patterns

### 2. EXISTING MODELS - DO NOT CREATE DUPLICATES
- `Edition` (NOT HackathonEdition - this already exists!)
- `Team`
- `User`
- `Idea`
- `Track`
- `Organization`
- `Speaker`
- `Workshop`
- `News`
- `CheckIn`

### 3. FOLLOW THE ARCHITECTURE STRICTLY FOR CODE REUSABILITY
```
Controller -> Service -> Repository -> Model
```
- **NEVER skip layers** - This breaks reusability
- **NEVER put business logic in controllers** - Other controllers can't reuse it
- **NEVER query database from controllers** - Makes code duplicate everywhere
- **ALWAYS use services for business logic** - So ALL controllers can reuse the same logic
- **ALWAYS use repositories for data access** - So ALL services can reuse the same queries

Example of REUSABILITY:
- TeamService is used by SystemAdmin, HackathonAdmin, TrackSupervisor controllers
- UserRepository is used by UserService, TeamService, AuthService
- One change in service = affects all controllers using it (GOOD!)

### 4. IMPORT PATTERNS - COPY FROM SAME DIRECTORY
- Check how other files in THE SAME directory import components
- SystemAdmin subdirectories use: `import Default from '../../../Layouts/Default.vue'`
- Ideas pages specifically use: `import Default from '@/Layouts/Default.vue'`

## Common Pitfalls to Avoid

1. **Creating duplicate models** (HackathonEdition when Edition exists)
2. **Wrong import paths** - Always check existing files in same directory
3. **Skipping service layer** - Controllers must NEVER have business logic
4. **Never hardcode colors** - Use theme system variables
5. **Always check user permissions** - Use middleware and gates
6. **Use existing components** - Don't recreate FilePondUploader, RichTextEditor, etc.
7. **Follow role hierarchy** - Respect the multi-tenant edition isolation
8. **Use transactions** - Wrap multi-table updates in DB transactions
9. **Cache carefully** - Clear caches after settings/config changes

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
- **Development tasks tracker in `tasks.md`** - MUST READ AND UPDATE
- **Issue fixes tracker in `fixes.md`** - Document all bug fixes

## SENIOR ENGINEER STANDARDS - MUST FOLLOW

### Act as a Senior Laravel/Software Engineer from Google, Meta, Amazon
When working on this codebase:
- **Think before acting** - Analyze the full impact of changes
- **Complete full implementations** - Never leave partial work
- **Follow best practices** - Use industry-standard patterns
- **Write clean, maintainable code** - Think long-term
- **Consider performance** - Optimize database queries and caching
- **Handle errors gracefully** - Anticipate edge cases

### CRITICAL: Complete Implementation Chains
**NEVER do partial implementations**
When refactoring or fixing:
1. If you modify a Controller → MUST update Service
2. If you modify a Service → MUST update Repository
3. If you modify a Repository → MUST ensure Model compatibility
4. **ALWAYS complete the full chain** - No exceptions

Example of BAD practice:
- Changing controller without updating service/repository
- Adding service methods without repository implementation
- Partial fixes that break other parts

Example of GOOD practice:
- Complete Controller → Service → Repository → Model chain
- Test the full flow after changes
- Ensure all layers are compatible

### Database Table Names - IMPORTANT
**Current database uses these tables:**
- `hackathon_editions` (NOT `editions`)
- `teams`
- `users`
- `ideas`
- `tracks`
- `organizations`
- `speakers`
- `workshops`
- `news`
- `check_ins`

**ALWAYS verify table names in migrations before using in validation rules**

### Error Handling and Debugging Process
When encountering errors:
1. **Read the full error message** - Don't assume
2. **Check the actual implementation** - Look at the code
3. **Verify method signatures** - Ensure interface compatibility
4. **Test the full flow** - From controller to database
5. **Fix ALL related issues** - Not just the immediate error

### File Upload and Image Handling
When working with file uploads (especially FilePond):
1. **Temp storage first** - Upload to `temp/` directory
2. **Move on save** - Move from temp to permanent storage
3. **Handle existing files** - Preserve unchanged files during updates
4. **Clean up temp files** - Remove old temporary files
5. **Use Storage facade** - Never use direct file operations

Example for images in updates:
```php
// Check if it's a new temp image
if (Str::startsWith($imagePath, 'temp/')) {
    // Move from temp to permanent
    $newPath = $this->moveImageFromTemp($imagePath, 'target/dir/');
} else {
    // Keep existing image
    $newPath = $imagePath;
}
```

### Vue Component Standards
1. **Use useLocalization composable** - NOT vue-i18n directly
   ```javascript
   import { useLocalization } from '@/composables/useLocalization'
   const { t, isRTL, direction, locale } = useLocalization()
   ```

2. **Check import paths in same directory** - Copy patterns from siblings
3. **Use FilePondUploader component** - Don't create custom uploaders
4. **Use RichTextEditor component** - For all rich text fields
5. **Follow theme system** - Use CSS variables for colors

### Testing and Validation
Before considering any task complete:
1. **Test create functionality** - Can you create new records?
2. **Test read functionality** - Do lists and details work?
3. **Test update functionality** - Can you edit existing records?
4. **Test delete functionality** - Can you remove records?
5. **Check permissions** - Does role-based access work?
6. **Verify UI/UX** - Are all forms and buttons working?

### Code Review Checklist
Before finishing any feature:
- [ ] Architecture pattern followed (Controller → Service → Repository → Model)
- [ ] No business logic in controllers
- [ ] No direct database queries in controllers/services
- [ ] All methods have proper return types
- [ ] Interfaces match implementations
- [ ] Error handling in place
- [ ] Database transactions used where needed
- [ ] Proper validation rules
- [ ] Permissions checked
- [ ] Cache cleared if needed

### Common Fixes Applied
1. **Missing repository methods** - Add them with proper signatures
2. **Interface mismatches** - Update to match base interfaces
3. **Import errors in Vue** - Use useLocalization instead of vue-i18n
4. **File upload issues** - Handle temp and existing files properly
5. **Database column errors** - Use correct column names (is_current not is_active)

### Development Workflow
1. **Understand the request** - What exactly needs to be done?
2. **Check existing code** - Look for similar implementations
3. **Plan the implementation** - Think through all layers
4. **Implement completely** - Do the full chain
5. **Test thoroughly** - Verify all functionality
6. **Clean up** - Remove debug code, clear caches

### IMPORTANT REMINDERS
- **NEVER skip steps** - Complete implementations only
- **ALWAYS test** - Don't assume it works
- **CHECK table/column names** - Verify against actual database
- **FOLLOW patterns** - Copy from existing similar code
- **COMPLETE the chain** - Controller → Service → Repository → Model
- **HANDLE errors** - Anticipate and handle edge cases
- **PRESERVE existing data** - Don't lose data during updates

## Task & Issue Tracking Protocol

### Task Tracking (tasks.md)
**MANDATORY**: For every user request that requires code changes:

1. **Before Starting Work**:
   - Add task to `tasks.md` with:
     - Task number (sequential)
     - Date (YYYY-MM-DD)
     - User prompt (exact request)
     - Planned solution approach
     - Status: Pending

2. **During Implementation**:
   - Update status to "In Progress"
   - List all files being modified
   - Document key code changes

3. **After Completion**:
   - Update status to "Completed"
   - Add code summary
   - Note any related tasks

4. **Task Format**:
   ```markdown
   ## Task #XXX
   **Date**: YYYY-MM-DD
   **User Prompt**: "Original request"
   **Solution**: Implementation approach
   **Files Affected**: List of files
   **Code Summary**: Key changes
   **Status**: Pending | In Progress | Completed
   ```

## Issue Tracking Protocol

### When Encountering Errors:
1. **Document in fixes.md** immediately with:
   - Issue type and description (concise, 1-2 lines)
   - Affected role(s)
   - Console error message
   - Applied fix (brief, actionable)

2. **Check Cross-Role Impact**:
   - If issue exists in one role (SystemAdmin, HackathonAdmin, TrackSupervisor)
   - MUST check if same issue exists in other roles
   - If found in multiple roles, note in fixes.md under "Should Check in Other Roles"
   - Don't fix all occurrences - just document and notify

3. **Format for fixes.md**:
   ```markdown
   ### Issue Title
   **Issue:** Brief description
   **Roles Affected:** Role names
   **When:** Trigger condition
   **Error:** Console/system error
   **Fix:** Solution applied

   #### Should Check in Other Roles:
   - [ ] RoleName - Component/feature to check
   ```

4. **Be Smart & Concise**:
   - Maximum 3 lines for descriptions
   - Focus on actionable information

## Design Implementation Guidelines

For implementing pages with consistent design and theme:
- **System Admin Pages**: See `SYSTEM_ADMIN_DESIGN_PROMPT.md` for complete guidelines
- **All Role Pages**: Reference `/design_files/vue_files_tailwind/[role_name]/` for page structures
- **Theme Colors**: Always use dynamic theme system, never hardcode colors
- **Design Files**: Reference design templates for accurate layouts
- **Figma Images**: Check `/design_files/figma_images/` for visual references
   - Group similar issues together