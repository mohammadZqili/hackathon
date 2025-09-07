# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

GuacPanel is a Laravel Vue.js admin starter kit built with:
- **Backend**: Laravel 11 with Fortify for authentication
- **Frontend**: Vue.js 3 + Inertia.js + Tailwind CSS v4
- **Testing**: PHPUnit with Pest
- **Key Features**: Role-based permissions (Spatie), audit logging, backup management, 2FA, magic link auth, dark mode, charts, data tables

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
vendor/bin/phpunit          # Run PHPUnit tests (configured via phpunit.xml)
vendor/bin/pest             # Run Pest tests (preferred testing framework)
```

### Common Artisan Commands
```bash
php artisan key:generate           # Generate application key
php artisan storage:link           # Link storage directory
php artisan backup:run             # Run backup (Spatie Backup)
php artisan backup:clean           # Clean old backups
php artisan permission:cache-reset # Clear permission cache
```

## Architecture Overview

### Authentication System
- **Laravel Fortify** handles authentication, registration, password resets, and 2FA
- **Magic Link Authentication** via `MagicLinkController` and email templates
- **Two-Factor Authentication** with recovery codes
- **Password Policies** with expiry enforcement via `CheckPasswordExpiry` middleware
- **Login History** tracking in `LoginHistory` model

### Authorization & Permissions  
- **Spatie Laravel Permission** for role-based access control
- Custom traits: `HasProtectedRoles`, `HasProtectedPermission`
- Visual role/permission management in admin interface
- Protected system roles and permissions cannot be deleted

### Data Management
- **TanStack Vue Table** for data tables with server-side pagination/sorting
- **Laravel Auditing** for tracking model changes
- **Spatie Backup** for database and file backups
- **Laravel Scout + Typesense** for search functionality

### Frontend Architecture
- **Inertia.js** provides SPA experience with server-side routing
- **Vue.js 3 Composition API** components
- **Tailwind CSS v4** with dark mode support
- **ApexCharts** for data visualization
- **FilePond** for file uploads

### Key Directories
```
app/
├── Actions/Fortify/          # Fortify authentication actions
├── Http/Controllers/         # Controllers (Admin*, Auth/, User*)
├── Http/Middleware/          # Custom middleware (2FA, password checks)
├── Models/                   # Eloquent models
├── Policies/                 # Authorization policies
└── Traits/                   # Reusable traits

resources/js/
├── Components/               # Reusable Vue components
├── Layouts/                  # Page layouts (Auth, Default, Public)
├── Pages/                    # Inertia.js pages
└── utils/                    # JavaScript utilities

database/
├── migrations/               # Database schema migrations
└── seeders/                  # Database seeders
```

### Important Models
- **User**: Enhanced with 2FA, login history, permissions
- **Setting**: Application-wide configuration
- **Personalisation**: User-specific preferences
- **LoginHistory**: Authentication audit trail
- **FinancialMetric**: Sample data for charts/widgets

### Middleware Stack
- `RequireTwoFactor`: Enforces 2FA when enabled
- `CheckPasswordExpiry`: Forces password changes
- `ForcePasswordChange`: Redirects to password change
- `DisableAccount`: Blocks disabled users
- `HandleInertiaRequests`: Shares data with frontend

### Configuration Files
- **Tailwind**: `tailwind.config.js` with CSS v4 configuration
- **Vite**: `vite.config.js` for asset building
- **PHPUnit**: `phpunit.xml` for testing configuration
- **PostCSS**: `postcss.config.js` for CSS processing