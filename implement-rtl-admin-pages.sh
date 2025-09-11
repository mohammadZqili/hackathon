#!/bin/bash

# Script to implement RTL/LTR support in all System Admin pages

echo "🌐 Implementing RTL/LTR support in System Admin pages..."

# Function to add RTL support to a Vue file
add_rtl_support() {
    local file="$1"
    echo "Processing: $file"
    
    # Check if file already has useLocalization
    if grep -q "useLocalization" "$file"; then
        echo "  ✓ Already has localization support"
        return
    fi
    
    # Add import for useLocalization
    if grep -q "^<script setup>" "$file"; then
        # Add the import after script setup
        sed -i "/^<script setup>/a\\
import { useLocalization } from '@/composables/useLocalization'" "$file"
        
        # Add the useLocalization hook
        sed -i "/^import.*useLocalization/a\\
\\
const { t, isRTL, direction, locale } = useLocalization()" "$file"
        
        echo "  ✓ Added localization support"
    else
        echo "  ⚠ No script setup found, skipping"
    fi
}

# Find all Vue files in SystemAdmin directory
for file in resources/js/Pages/SystemAdmin/**/*.vue resources/js/Pages/SystemAdmin/*.vue; do
    if [ -f "$file" ]; then
        add_rtl_support "$file"
    fi
done

echo "✅ RTL/LTR implementation complete!"
echo "📝 Next steps:"
echo "  1. Update translation files with admin content"
echo "  2. Test each page with Arabic/English switching"
echo "  3. Verify form inputs and data tables work correctly in RTL"