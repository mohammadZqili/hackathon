#!/bin/bash

# Script to update all SystemAdmin pages with translation keys

# List of SystemAdmin pages to update
pages=(
    "Ideas/Index.vue"
    "Ideas/Review.vue"
    "Ideas/Show.vue"
    "News/Index.vue"
    "News/Create.vue"
    "News/Edit.vue"
    "News/MediaCenter.vue"
    "Teams/Index.vue"
    "Teams/Create.vue"
    "Teams/Edit.vue"
    "Teams/Show.vue"
    "Users/Index.vue"
    "Users/Create.vue"
    "Users/Edit.vue"
    "Users/Show.vue"
    "Workshops/Index.vue"
    "Workshops/Create.vue"
    "Workshops/Edit.vue"
    "Organizations/Index.vue"
    "Organizations/Create.vue"
    "Organizations/Edit.vue"
    "Speakers/Index.vue"
    "Speakers/Create.vue"
    "Speakers/Edit.vue"
    "Tracks/Index.vue"
    "Reports/Index.vue"
    "Checkins/Index.vue"
    "QRScanner.vue"
    "Settings/Index.vue"
    "Settings/Branding.vue"
    "Settings/Smtp.vue"
    "Settings/Twitter.vue"
)

echo "SystemAdmin pages that need translation updates:"
for page in "${pages[@]}"; do
    echo "  - resources/js/Pages/SystemAdmin/$page"
done

echo ""
echo "These pages need to have their hardcoded strings replaced with translation keys using the t() function."
echo "Translation keys are defined in:"
echo "  - resources/lang/en/admin.php"
echo "  - resources/lang/ar/admin.php"
