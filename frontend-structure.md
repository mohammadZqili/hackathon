# GuacPanel Frontend Structure

## Overview
GuacPanel uses Vue.js 3 with Inertia.js for a seamless single-page application experience. The frontend architecture follows a modular approach with Tailwind CSS v4 for styling and dark mode support.

## Directory Structure

```
resources/js/
├── Components/          # Reusable Vue components
├── Layouts/            # Page layout templates
├── Pages/              # Inertia.js pages
├── Shared/             # Shared components across layouts
├── utils/              # JavaScript utilities
├── app.js              # Main application entry point
└── darkMode.js         # Theme management utilities
```

## Layouts (3 Main Templates)

### 1. **Public.vue** (`resources/js/Layouts/Public.vue`)
- **Purpose**: Marketing/documentation pages layout
- **Features**:
  - Responsive sidebar navigation with mobile overlay
  - Theme switcher (light/dark/auto)
  - Logo with customizable branding
  - Footer integration
  - Sidebar state persistence in localStorage
  - Keyboard navigation support (Escape key)
- **Components Used**: `NavSidebarDesktop`, `Footer`
- **Target Pages**: Documentation, marketing content

### 2. **Default.vue** (`resources/js/Layouts/Default.vue`)
- **Purpose**: Main authenticated user interface
- **Features**:
  - Full admin dashboard layout
  - System notices display
  - Search functionality (desktop/mobile)
  - User profile dropdown
  - Notification system
  - Settings access
  - Responsive sidebar with mobile support
  - Dynamic header height based on system notices
- **Components Used**: `NavSidebarDesktop`, `NavProfile`, `Notification`, `FlashMessage`, `Footer`, `ColorThemeSwitcher`, `Logo`, `Search`, `SystemNotice`
- **Target Pages**: Dashboard, admin pages, user account pages

### 3. **Auth.vue** (`resources/js/Layouts/Auth.vue`)
- **Purpose**: Authentication and minimal pages
- **Features**:
  - Minimal design focused on forms
  - Logo display with custom branding support
  - Flash message integration
  - Simple footer with homepage link
  - Dark mode support
- **Components Used**: `Logo`, `FlashMessage`
- **Target Pages**: Login, register, password reset

## Pages Structure

### Root Pages (4 pages)
- `Home.vue` - Landing page
- `Dashboard.vue` - Main dashboard
- `Chart.vue` - Charts demonstration
- `Terms.vue` - Terms of service

### Authentication Pages (`Auth/` - 8 pages)
- `Login.vue` - User login
- `Register.vue` - User registration
- `RegisterMagicLink.vue` - Magic link registration
- `ForgotPassword.vue` - Password recovery
- `ResetPassword.vue` - Password reset form
- `ChangePassword.vue` - Password change
- `ConfirmPassword.vue` - Password confirmation
- `TwoFactorChallenge.vue` - 2FA verification

### User Account Pages (`UserAccount/` - 4 pages)
- `IndexPage.vue` - Profile management
- `IndexSessionPage.vue` - Active sessions
- `IndexTwoFactorAuthenticationPage.vue` - 2FA settings
- `IndexPasswordExpiredPage.vue` - Password expiry handler

### Admin Pages (`Admin/` - 6 main pages)
- `IndexSettingPage.vue` - System settings
- `IndexManageSettingPage.vue` - Advanced settings
- `IndexBackupPage.vue` - Backup management
- `IndexAuditPage.vue` - Audit logs
- `IndexLoginHistoryPage.vue` - Login tracking
- `IndexSessionPage.vue` - Session management

### Admin Sub-modules
#### User Management (`Admin/User/` - 2 pages)
- `IndexUserPage.vue` - User listing/management
- `EditUserPage.vue` - User editing

#### Personalization (`Admin/Personalisation/` - 1 page)
- `IndexPage.vue` - Branding/customization

#### System Notices (`Admin/Notice/` - 3 pages)
- `IndexPage.vue` - Notice listing
- `CreatePage.vue` - Create notices
- `EditPage.vue` - Edit notices

#### Permissions & Roles (`Admin/PermissionRole/` - 3 pages)
- `IndexPermissionRolePage.vue` - Main permissions interface
- `PermissionsTab.vue` - Permissions management
- `RolesTab.vue` - Role management

### Documentation Pages (`Documentation/` - 4 pages)
- `IndexDocumentationPage.vue` - Documentation hub
- `IndexFeaturePage.vue` - Feature documentation
- `IndexInstallPage.vue` - Installation guide
- `IndexComponentPage.vue` - Component documentation

### Monitoring Pages (`Monitoring/` - 1 page)
- `IndexPage.vue` - System monitoring

## Components Structure

### Core Components (20 components)
- `Logo.vue` - Brand logo with customization
- `Modal.vue` - Modal dialogs
- `Pagination.vue` - Data pagination
- `PageHeader.vue` - Page headers
- `Switch.vue` - Toggle switches
- `FlashMessage.vue` - Success/error messages
- `Notification.vue` - User notifications
- `NavSidebarDesktop.vue` - Main sidebar navigation
- `NavProfile.vue` - User profile dropdown
- `ColorThemeSwitcher.vue` - Theme toggle
- `SystemNotice.vue` - System-wide notices
- `ArticleNavigation.vue` - Content navigation
- `CodeBlock.vue` - Code syntax highlighting
- `Datatable.vue` - Data tables with sorting/filtering
- `FilePondUploader.vue` - File upload component

### Form Components (5 components)
- `FormInput.vue` - Text inputs
- `FormTextarea.vue` - Multi-line text
- `FormSelect.vue` - Select dropdowns
- `FormCheckbox.vue` - Checkboxes
- `FormRadioGroup.vue` - Radio button groups

### Chart Components (`Charts/` - 4 components)
- `ApexAreaChart.vue` - Area charts
- `ApexBarChart.vue` - Bar charts
- `ApexDonutChart.vue` - Donut charts
- `ApexLineChart.vue` - Line charts

### Widget Components (`Widgets/` - 4 components)
- `AchievementWidget.vue` - Achievement displays
- `MetricWidget.vue` - Key metrics
- `StatWidget.vue` - Statistics display
- `StockWidget.vue` - Stock information

### Search Components (`Typesense/` - 3 components)
- `Search.vue` - Main search interface
- `SearchResults.vue` - Search result display
- `FederatedSearch.vue` - Multi-source search

## Shared Components Structure

### Public Shared (`Shared/Public/` - 3 components)
- `NavSidebarDesktop.vue` - Public site navigation
- `Footer.vue` - Site footer
- `ArticleNavigation.vue` - Article/documentation navigation

## Utilities

### JavaScript Utilities (`utils/` - 1 file)
- `themeInit.js` - Theme initialization and management

## Key Features

### Theme Management
- **Three theme modes**: Light, dark, auto (system preference)
- **Persistent state**: Theme choice saved in localStorage
- **Dynamic switching**: Real-time theme updates without page refresh
- **System integration**: Respects user's OS theme preference

### Responsive Design
- **Mobile-first approach**: All layouts work on mobile devices
- **Breakpoint system**: Uses Tailwind CSS responsive utilities
- **Touch-friendly**: Mobile interactions for navigation and forms
- **Accessibility**: ARIA labels, keyboard navigation, focus management

### Search Integration
- **Typesense powered**: Fast, typo-tolerant search
- **Federated search**: Multiple content types
- **Mobile optimized**: Separate mobile search overlay
- **Real-time results**: Instant search as you type

### Authentication Features
- **Multi-factor authentication**: 2FA support with recovery codes
- **Magic link authentication**: Passwordless login option
- **Password policies**: Expiry enforcement and complexity rules
- **Session management**: Active session tracking and control

### Data Management
- **TanStack Vue Table**: Advanced data tables with server-side operations
- **Real-time updates**: Live data refresh capabilities
- **Audit logging**: Complete change tracking
- **Backup integration**: Database and file backup management

## Architecture Patterns

### Layout Hierarchy
```
App.vue
├── Public.vue (Marketing/Docs)
├── Default.vue (Admin/Dashboard)
└── Auth.vue (Authentication)
```

### Component Composition
- **Composition API**: Modern Vue 3 reactive patterns
- **Prop-based communication**: Clear component interfaces
- **Event emission**: Child-to-parent communication
- **Provide/inject**: Deep component tree sharing

### State Management
- **Inertia.js props**: Server-side data sharing
- **Local reactive state**: Vue 3 ref/reactive
- **Persistent storage**: localStorage for user preferences
- **Page props**: Shared data across components

### Performance  
- **Component lazy loading**: Dynamic imports for large components
- **Image optimization**: Responsive images with dark mode variants
- **CSS purging**: Tailwind CSS unused style removal
- **Bundle splitting**: Separate chunks for different page types
