# RTL/LTR Implementation Guide for Arabic & English Support

## Overview
This guide explains how to implement RTL (Right-to-Left) support for Arabic and LTR (Left-to-Right) support for English in your GuacPanel application.

## 🚀 Quick Setup

### 1. Run Migration
```bash
php artisan migrate
```
This adds the `locale` field to the users table.

### 2. Clear Cache
```bash
php artisan config:clear
php artisan cache:clear
```

### 3. Compile Assets
```bash
npm run dev
# or for production
npm run build
```

## 📁 Files Created/Modified

### Backend Files
- `/app/Http/Middleware/SetLocale.php` - Middleware to set locale and direction
- `/app/Http/Controllers/LanguageController.php` - Controller for language switching
- `/database/migrations/xxx_add_locale_to_users_table.php` - Migration for user locale
- `/resources/lang/en/dashboard.php` - English translations
- `/resources/lang/ar/dashboard.php` - Arabic translations

### Frontend Files
- `/resources/css/rtl.css` - RTL utility classes
- `/resources/js/composables/useLocalization.js` - Vue composable for localization
- `/resources/js/Components/LanguageSwitcher.vue` - Language switcher component

### Modified Files
- `/app/Http/Kernel.php` - Added SetLocale middleware
- `/app/Http/Middleware/HandleInertiaRequests.php` - Share locale data with Inertia
- `/routes/web.php` - Added language switching routes
- `/resources/css/app.css` - Import RTL styles

## 🎯 How to Use in Vue Components

### 1. Import the Localization Composable
```javascript
import { useLocalization } from '@/composables/useLocalization'
```

### 2. Use in Your Component
```vue
<template>
    <div :dir="direction">
        <!-- Page Title -->
        <h1 :class="isRTL ? 'text-right' : 'text-left'">
            {{ t('dashboard') }}
        </h1>
        
        <!-- Welcome Message with Parameter -->
        <p>{{ t('welcome', { name: userName }) }}</p>
        
        <!-- Flex Container with RTL Support -->
        <div class="flex items-center" :class="isRTL ? 'flex-row-reverse' : ''">
            <span>{{ t('team_members') }}</span>
            <span>{{ stats.count }}</span>
        </div>
        
        <!-- Icon that needs flipping in RTL -->
        <svg :class="flipIcon">
            <!-- Arrow icon -->
        </svg>
    </div>
</template>

<script setup>
import { useLocalization } from '@/composables/useLocalization'

// Get localization utilities
const { 
    t,                    // Translation function
    locale,              // Current locale (en/ar)
    direction,           // Current direction (ltr/rtl)
    isRTL,              // Boolean for RTL check
    switchLanguage,      // Function to switch language
    flipIcon,           // Class for flipping icons
    textAlign,          // Computed text alignment
    marginStart,        // Direction-aware margin start
    marginEnd,          // Direction-aware margin end
    paddingStart,       // Direction-aware padding start
    paddingEnd,         // Direction-aware padding end
} = useLocalization()
</script>
```

### 3. Add Language Switcher to Your Layout
```vue
<template>
    <header>
        <!-- Other header content -->
        <LanguageSwitcher />
    </header>
</template>

<script setup>
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue'
</script>
```

## 🌐 Translation Keys

### Adding New Translations

1. **English** - Add to `/resources/lang/en/dashboard.php`:
```php
return [
    'new_key' => 'English text',
    'with_param' => 'Hello :name',
];
```

2. **Arabic** - Add to `/resources/lang/ar/dashboard.php`:
```php
return [
    'new_key' => 'النص العربي',
    'with_param' => 'مرحبا :name',
];
```

## 🎨 CSS Classes for RTL

### Tailwind RTL Classes
```html
<!-- Text alignment -->
<div class="text-left rtl:text-right">Content</div>

<!-- Margins -->
<div class="ml-4 rtl:ml-0 rtl:mr-4">Content</div>

<!-- Padding -->
<div class="pl-4 rtl:pl-0 rtl:pr-4">Content</div>

<!-- Flex direction -->
<div class="flex rtl:flex-row-reverse">Content</div>

<!-- Borders -->
<div class="border-l rtl:border-l-0 rtl:border-r">Content</div>

<!-- Positioning -->
<div class="left-0 rtl:left-auto rtl:right-0">Content</div>
```

### Custom RTL Classes (in rtl.css)
```css
/* Flip icons horizontally */
[dir="rtl"] .flip-rtl {
    transform: scaleX(-1);
}

/* Chevron icons */
[dir="rtl"] .chevron-right {
    transform: rotate(180deg);
}
```

## 📋 Common Patterns

### 1. Form with RTL Support
```vue
<template>
    <form :dir="direction">
        <div class="mb-4">
            <label :class="isRTL ? 'text-right' : 'text-left'">
                {{ t('name') }}
            </label>
            <input 
                type="text" 
                class="w-full"
                :class="isRTL ? 'text-right' : 'text-left'"
                :placeholder="t('enter_name')"
            />
        </div>
    </form>
</template>
```

### 2. Table with RTL Support
```vue
<template>
    <table :dir="direction">
        <thead>
            <tr>
                <th :class="isRTL ? 'text-right' : 'text-left'">
                    {{ t('name') }}
                </th>
                <th :class="isRTL ? 'text-right' : 'text-left'">
                    {{ t('email') }}
                </th>
            </tr>
        </thead>
    </table>
</template>
```

### 3. Cards with RTL Support
```vue
<template>
    <div class="flex gap-4" :class="isRTL ? 'flex-row-reverse' : ''">
        <div class="card">
            <h3 :class="isRTL ? 'text-right' : 'text-left'">
                {{ t('card_title') }}
            </h3>
            <p :class="isRTL ? 'text-right' : 'text-left'">
                {{ t('card_content') }}
            </p>
        </div>
    </div>
</template>
```

### 4. Navigation with RTL Support
```vue
<template>
    <nav class="flex items-center" :class="isRTL ? 'flex-row-reverse' : ''">
        <Link 
            v-for="item in menuItems" 
            :key="item.name"
            :href="item.route"
            class="px-4 py-2"
            :class="isRTL ? 'ml-2' : 'mr-2'"
        >
            {{ t(item.label) }}
        </Link>
    </nav>
</template>
```

## 🔧 Utility Functions

### Direction-Aware Styles
```javascript
// Use in computed properties or methods
const buttonStyles = computed(() => ({
    ...marginStart('1rem'),  // Applies margin-left in LTR, margin-right in RTL
    ...paddingEnd('0.5rem'), // Applies padding-right in LTR, padding-left in RTL
    textAlign: textAlign.value // 'left' in LTR, 'right' in RTL
}))
```

### Conditional Classes
```javascript
const containerClasses = computed(() => 
    directionalClasses(
        'border-l-4 pl-4',      // LTR classes
        'border-r-4 pr-4'       // RTL classes
    )
)
```

## 🌍 Browser Support

The implementation supports all modern browsers:
- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

## 🐛 Troubleshooting

### Language Not Switching
1. Clear browser cache
2. Check if `SetLocale` middleware is registered
3. Verify translation files exist
4. Check console for errors

### RTL Styles Not Applied
1. Ensure `rtl.css` is imported in `app.css`
2. Check if HTML `dir` attribute is set
3. Verify `direction` is passed to components
4. Run `npm run dev` to recompile

### Translations Not Showing
1. Check translation keys match exactly
2. Verify `loadTranslations()` method in HandleInertiaRequests
3. Ensure locale is set correctly in session

## 📝 Best Practices

1. **Always use translation keys** instead of hardcoded text
2. **Test both directions** when developing new features
3. **Use semantic HTML** for better RTL support
4. **Avoid inline styles** that specify direction
5. **Use logical properties** when possible (start/end instead of left/right)
6. **Keep translations consistent** in length to maintain layout
7. **Test with real Arabic content** not just Lorem Ipsum

## 🚀 Advanced Features

### Custom Fonts for Arabic
```css
/* In app.css */
@font-face {
    font-family: 'Arabic Font';
    src: url('/fonts/arabic-font.woff2') format('woff2');
}

[dir="rtl"] {
    font-family: 'Arabic Font', sans-serif;
}
```

### Number Formatting
```javascript
// Format numbers based on locale
const formatNumber = (num) => {
    return new Intl.NumberFormat(locale.value).format(num)
}
```

### Date Formatting
```javascript
// Format dates based on locale
const formatDate = (date) => {
    return new Intl.DateTimeFormat(locale.value, {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    }).format(new Date(date))
}
```

## 📚 Resources

- [MDN: RTL Web Design](https://developer.mozilla.org/en-US/docs/Glossary/RTL)
- [W3C: Internationalization](https://www.w3.org/International/)
- [Vue I18n Documentation](https://vue-i18n.intlify.dev/)
- [Tailwind CSS RTL Support](https://tailwindcss.com/docs/hover-focus-and-other-states#rtl-support)

## ✅ Checklist for New Pages

When creating new pages, ensure:
- [ ] Import `useLocalization` composable
- [ ] Use `t()` function for all text
- [ ] Add `:dir="direction"` to root element
- [ ] Apply RTL classes for layout
- [ ] Test in both Arabic and English
- [ ] Add translations to both language files
- [ ] Handle icon flipping where needed
- [ ] Verify form inputs work in RTL
- [ ] Check responsive design in both directions