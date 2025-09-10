# System Admin Page Design Implementation Prompt

## Standard Instruction for New System Admin Pages

When creating or updating any System Admin page, use this exact prompt:

---

### PROMPT TO USE:

"Please implement/update the [PAGE_NAME] page in System Admin with the following requirements:

1. **Layout**: Use the Default layout from GuacPanel that includes header and sidebar (`import Default from '../../../Layouts/Default.vue'`)

2. **Theme Colors**: Apply dynamic GuacPanel theme colors throughout the page. The theme colors should be retrieved from CSS variables and applied to all relevant elements.

3. **Design Source**: 
   - Get the page structure and content from: `/home/geek/projects/hakathons/projects/guacpanel-tailwind-1.14/design_files/vue_files_tailwind/Admin role/[FOLDER_NAME]/`
   - Reference design images from: `/home/geek/projects/hakathons/projects/guacpanel-tailwind-1.14/design_files/figma_images/Admin/`
   - Use the exact content structure from the design files BUT replace all static colors with GuacPanel theme colors

4. **Theme Color Implementation**:
   ```javascript
   // Add this to script setup
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

5. **Color Application Rules**:
   - Primary text/links: `:style="{ color: themeColor.primary }"`
   - Buttons: `:style="{ background: \`linear-gradient(135deg, \${themeColor.gradientFrom}, \${themeColor.gradientTo})\` }"`
   - Status badges/pills: Use theme color as background with appropriate opacity
   - Focus states: `:style="{ '--tw-ring-color': themeColor.primary }"`
   - Active/selected states: Use theme primary color
   - Icons in action buttons: Apply theme color
   - Table headers with important data: Apply theme color
   - Progress bars/indicators: Use theme gradient

6. **Component Structure**:
   ```vue
   <template>
       <Head title="[Page Title]" />
       <Default>
           <div class="container mx-auto px-4 py-8" :style="themeStyles">
               <!-- Page content here -->
           </div>
       </Default>
   </template>
   ```

7. **Reusable Components**: Always use existing components when available:
   - FilePondUploader for file uploads
   - RichTextEditor for text editing (TipTap)
   - Existing table components with theme colors
   - Modal/dialog components from the codebase

8. **Form Styling with Theme**:
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

9. **Dark Mode Support**: Ensure all elements have dark mode variants:
   - `text-gray-900 dark:text-white` for main text
   - `bg-white dark:bg-gray-800` for cards/containers
   - `border-gray-300 dark:border-gray-600` for borders
   - `text-gray-600 dark:text-gray-400` for secondary text

10. **Consistent Patterns**:
    - Page headers with title and action button
    - Cards with white/dark background and shadow
    - Tables with alternating row colors in dark mode
    - Form groups with proper spacing
    - Action buttons in consistent positions

Please ensure the page is fully functional with real data, not mock data, and all CRUD operations work correctly."

---

## Example Usage

For a new "Sponsors" page:
1. Replace `[PAGE_NAME]` with "Sponsors"
2. Replace `[FOLDER_NAME]` with "sponsors"
3. Replace `[Page Title]` with "Sponsors Management"

## Key Files to Reference

- **Ideas Page** (Good example of theme integration): `/resources/js/Pages/SystemAdmin/Ideas/Index.vue`
- **Editions Pages** (Complete CRUD with theme): `/resources/js/Pages/SystemAdmin/Editions/`
- **News Pages** (Media handling with theme): `/resources/js/Pages/SystemAdmin/News/`

## Common Theme Color Applications

### Buttons
```vue
<!-- Primary Action Button -->
<button class="..." :style="{
    background: `linear-gradient(135deg, ${themeColor.gradientFrom}, ${themeColor.gradientTo})`
}">
    Action
</button>

<!-- Secondary Button -->
<button class="..." :style="{
    color: themeColor.primary,
    borderColor: themeColor.primary
}">
    Secondary
</button>
```

### Links and Text
```vue
<!-- Themed Link -->
<Link :style="{ color: themeColor.primary }" class="hover:underline">
    Link Text
</Link>

<!-- Important Data -->
<span :style="{ color: themeColor.primary }">
    {{ data }}
</span>
```

### Status Indicators
```vue
<!-- Active Status -->
<span class="px-2 py-1 rounded-full text-white text-xs" 
      :style="{ backgroundColor: themeColor.primary }">
    Active
</span>

<!-- With Opacity -->
<div :style="{ 
    backgroundColor: themeColor.primary + '20',
    color: themeColor.primary 
}">
    Status
</div>
```

### Table Elements
```vue
<!-- Table Header Cell -->
<th class="..." :style="{ color: themeColor.primary }">
    Column Name
</th>

<!-- Data Cell with Theme -->
<td :style="{ color: themeColor.primary }">
    {{ value }}
</td>
```

## Testing Checklist

After implementing a new page, verify:
- [ ] Theme colors are dynamically applied
- [ ] Dark mode works correctly
- [ ] Form focus states use theme colors
- [ ] Buttons have gradient backgrounds
- [ ] Links and important data use theme primary color
- [ ] Page uses Default layout with header/sidebar
- [ ] All CRUD operations are functional
- [ ] File uploads work if applicable
- [ ] Responsive design is maintained

## Notes

- Never use hardcoded colors like `text-blue-500` or `bg-green-100`
- Always use the theme color variables
- Ensure consistency with existing System Admin pages
- Test with different theme colors by changing GuacPanel settings