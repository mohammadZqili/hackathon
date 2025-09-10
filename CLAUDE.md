# CLAUDE.md

This file provides guidance to Claude Code when working with this repository.

## Project Overview
GuacPanel - Hackathon Management System built with Laravel 11 and Vue.js 3

## Development Commands

### Frontend Development
```bash
npm run dev        # Start Vite development server
npm run build      # Build for production
```

### Laravel Commands
```bash
php artisan serve                    # Start Laravel development server
php artisan migrate                  # Run database migrations
php artisan db:seed                  # Seed the database
php artisan migrate:fresh --seed     # Fresh migration with seeding
```

### Testing
```bash
vendor/bin/phpunit          # Run PHPUnit tests
vendor/bin/pest             # Run Pest tests
```

## Project-Specific Instructions

### System Admin Page Development Guidelines

#### Layout Requirements
- Always use Default layout with header and sidebar: `import Default from '../../../Layouts/Default.vue'`
- Use `<Head title="[Page Title]" />` for page titles
- Wrap content in `<Default>` component

#### Theme Color Implementation
All System Admin pages must use dynamic GuacPanel theme colors. Add this to every page:

```javascript
// Add to script setup
const themeColor = ref({
    primary: '#0d9488',
    hover: '#0f766e',
    rgb: '13, 148, 136',
    gradientFrom: '#0d9488',
    gradientTo: '#14b8a6'
})

onMounted(() => {
    const root = document.documentElement
    const primary = getComputedStyle(root).getPropertyValue('--primary-color').trim() || '#0d9488'
    const hover = getComputedStyle(root).getPropertyValue('--primary-hover').trim() || '#0f766e'
    const rgb = getComputedStyle(root).getPropertyValue('--primary-color-rgb').trim() || '13, 148, 136'
    const gradientFrom = getComputedStyle(root).getPropertyValue('--primary-gradient-from').trim() || '#0d9488'
    const gradientTo = getComputedStyle(root).getPropertyValue('--primary-gradient-to').trim() || '#14b8a6'

    themeColor.value = {
        primary: primary || themeColor.value.primary,
        hover: hover || themeColor.value.hover,
        rgb: rgb || themeColor.value.rgb,
        gradientFrom: gradientFrom || themeColor.value.gradientFrom,
        gradientTo: gradientTo || themeColor.value.gradientTo
    }
})

const themeStyles = computed(() => ({
    '--theme-primary': themeColor.value.primary,
    '--theme-hover': themeColor.value.hover,
    '--theme-rgb': themeColor.value.rgb,
    '--theme-gradient-from': themeColor.value.gradientFrom,
    '--theme-gradient-to': themeColor.value.gradientTo,
}))
```

#### Color Application Rules
- **Never use hardcoded colors** like `text-blue-500` or `bg-green-100`
- Primary text/links: `:style="{ color: themeColor.primary }"`
- Buttons: `:style="{ background: \`linear-gradient(135deg, \${themeColor.gradientFrom}, \${themeColor.gradientTo})\` }"`
- Focus states: `:style="{ '--tw-ring-color': themeColor.primary }"`
- Active/selected states: Use theme primary color
- Status badges: Use theme color with appropriate opacity

#### Form Styling
```vue
<style scoped>
input[type="text"]:focus,
input[type="number"]:focus,
input[type="date"]:focus,
select:focus,
textarea:focus {
    border-color: var(--theme-primary) !important;
    box-shadow: 0 0 0 3px rgba(var(--theme-rgb), 0.1) !important;
}

input[type="checkbox"]:checked {
    background-color: var(--theme-primary) !important;
    border-color: var(--theme-primary) !important;
}
</style>
```

#### Dark Mode Support
Always include dark mode variants:
- `text-gray-900 dark:text-white` for main text
- `bg-white dark:bg-gray-800` for cards/containers
- `border-gray-300 dark:border-gray-600` for borders
- `text-gray-600 dark:text-gray-400` for secondary text

#### Design Sources
- Page structures: `/design_files/vue_files_tailwind/Admin role/[FOLDER_NAME]/`
- Design images: `/design_files/figma_images/Admin/`
- Use exact content structure from design files BUT replace static colors with theme colors

#### Reusable Components
Always use existing components when available:
- FilePondUploader for file uploads
- RichTextEditor for text editing (TipTap)
- Existing table components with theme colors
- Modal/dialog components from the codebase

#### Reference Pages
- **Ideas Page** (theme integration example): `/resources/js/Pages/SystemAdmin/Ideas/Index.vue`
- **Editions Pages** (complete CRUD): `/resources/js/Pages/SystemAdmin/Editions/`
- **News Pages** (media handling): `/resources/js/Pages/SystemAdmin/News/`

### Important Development Notes
- Always use real data, not mock data
- Ensure all CRUD operations are functional
- Test with different theme colors by changing GuacPanel settings
- Verify dark mode works correctly
- Maintain responsive design
- Check form focus states use theme colors
- Always track tasks in TASKS.md with prompt, time, and description
- Technical notes and preferences should be documented in CLAUDE.md

## Settings System Implementation Notes

### Database Structure
The settings system uses a key-value structure in the `system_settings` table:
- `key`: Unique identifier for the setting
- `value`: String value (booleans stored as '1' or '0')
- `group`: Category (smtp, branding, notifications, sms, twitter)
- `type`: Data type hint (string, boolean, integer, json)

### Settings Access Pattern
```php
// PHP access via helper
use App\Helpers\Settings;
$appName = Settings::get('app.name');
$emailEnabled = Settings::emailNotificationsEnabled();

// Vue component access
$page.props.settings.app_name
$page.props.settings.notifications.email_enabled
```

### Boolean Handling
- Always store booleans as '1' (true) or '0' (false) strings in database
- Use `$request->boolean()` in controllers for checkbox handling
- Convert to proper boolean when retrieving: `$value === '1'`

### Cache Strategy
- Settings cached for 3600 seconds (1 hour)
- Cache cleared on any update via `SettingsServiceProvider::clearCache()`
- Config cache also cleared with `\Artisan::call('config:clear')`

### Form Handling with Inertia
```javascript
// Use useForm for proper form handling
const form = useForm({
    field: props.settings?.field || defaultValue
})

// Transform data if needed
form.transform(() => processedData).post(route('route.name'))
```

### Transaction Safety
All settings updates wrapped in database transactions:
```php
DB::beginTransaction();
try {
    // Update settings
    DB::commit();
} catch (\Exception $e) {
    DB::rollBack();
    // Handle error
}
```