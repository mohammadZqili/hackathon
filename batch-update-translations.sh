#!/bin/bash

# Function to add localization import if not present
add_localization_import() {
    local file="$1"
    
    # Check if localization is already imported
    if ! grep -q "useLocalization" "$file"; then
        # Add the import at the beginning of script setup
        sed -i '/<script setup>/a import { useLocalization } from '"'"'@/composables/useLocalization'"'"'\n\nconst { t, isRTL, direction, locale } = useLocalization()' "$file"
        echo "  ✓ Added localization import to $file"
    else
        echo "  ⚠ Localization already imported in $file"
    fi
}

# Update Settings Index page
echo "Updating Settings pages..."
file="resources/js/Pages/SystemAdmin/Settings/Index.vue"
if [ -f "$file" ]; then
    add_localization_import "$file"
    # Update hardcoded strings with translation keys
    sed -i 's/<Head title="System Settings"/<Head :title="t('"'"'admin.settings.title'"'"')"/' "$file"
    sed -i 's/>System Settings</>{{ t('"'"'admin.settings.title'"'"') }}</' "$file"
    sed -i 's/>General Settings</>{{ t('"'"'admin.settings.general'"'"') }}</' "$file"
    sed -i 's/>SMTP Settings</>{{ t('"'"'admin.settings.smtp'"'"') }}</' "$file"
    sed -i 's/>Branding</>{{ t('"'"'admin.settings.branding'"'"') }}</' "$file"
    sed -i 's/>Notifications</>{{ t('"'"'admin.settings.notifications'"'"') }}</' "$file"
    echo "  ✓ Updated Settings/Index.vue"
fi

# Update Teams Index page
echo "Updating Teams pages..."
file="resources/js/Pages/SystemAdmin/Teams/Index.vue"
if [ -f "$file" ]; then
    add_localization_import "$file"
    sed -i 's/<Head title="Teams Management"/<Head :title="t('"'"'admin.teams.title'"'"')"/' "$file"
    sed -i 's/>Teams Management</>{{ t('"'"'admin.teams.title'"'"') }}</' "$file"
    sed -i 's/>Create Team</>{{ t('"'"'admin.teams.create'"'"') }}</' "$file"
    sed -i 's/>Team Name</>{{ t('"'"'admin.teams.name'"'"') }}</' "$file"
    sed -i 's/>Team Leader</>{{ t('"'"'admin.teams.leader'"'"') }}</' "$file"
    sed -i 's/>Members</>{{ t('"'"'admin.teams.members'"'"') }}</' "$file"
    sed -i 's/>Status</>{{ t('"'"'admin.teams.status'"'"') }}</' "$file"
    sed -i 's/>Actions</>{{ t('"'"'admin.teams.actions'"'"') }}</' "$file"
    echo "  ✓ Updated Teams/Index.vue"
fi

# Update Users Index page
echo "Updating Users pages..."
file="resources/js/Pages/SystemAdmin/Users/Index.vue"
if [ -f "$file" ]; then
    add_localization_import "$file"
    sed -i 's/<Head title="Users Management"/<Head :title="t('"'"'admin.users.title'"'"')"/' "$file"
    sed -i 's/>Users Management</>{{ t('"'"'admin.users.title'"'"') }}</' "$file"
    sed -i 's/>Create User</>{{ t('"'"'admin.users.create'"'"') }}</' "$file"
    sed -i 's/>Name</>{{ t('"'"'admin.users.name'"'"') }}</' "$file"
    sed -i 's/>Email</>{{ t('"'"'admin.users.email'"'"') }}</' "$file"
    sed -i 's/>Role</>{{ t('"'"'admin.users.role'"'"') }}</' "$file"
    sed -i 's/>Status</>{{ t('"'"'admin.users.status'"'"') }}</' "$file"
    sed -i 's/>Actions</>{{ t('"'"'admin.users.actions'"'"') }}</' "$file"
    echo "  ✓ Updated Users/Index.vue"
fi

# Update Workshops Index page  
echo "Updating Workshops pages..."
file="resources/js/Pages/SystemAdmin/Workshops/Index.vue"
if [ -f "$file" ]; then
    add_localization_import "$file"
    sed -i 's/<Head title="Workshops Management"/<Head :title="t('"'"'admin.workshops.title'"'"')"/' "$file"
    sed -i 's/>Workshops Management</>{{ t('"'"'admin.workshops.title'"'"') }}</' "$file"
    sed -i 's/>Create Workshop</>{{ t('"'"'admin.workshops.create'"'"') }}</' "$file"
    sed -i 's/>Workshop Name</>{{ t('"'"'admin.workshops.name'"'"') }}</' "$file"
    sed -i 's/>Speaker</>{{ t('"'"'admin.workshops.speaker'"'"') }}</' "$file"
    sed -i 's/>Date</>{{ t('"'"'admin.workshops.date'"'"') }}</' "$file"
    sed -i 's/>Location</>{{ t('"'"'admin.workshops.location'"'"') }}</' "$file"
    sed -i 's/>Capacity</>{{ t('"'"'admin.workshops.capacity'"'"') }}</' "$file"
    echo "  ✓ Updated Workshops/Index.vue"
fi

# Update Organizations Index page
echo "Updating Organizations pages..."
file="resources/js/Pages/SystemAdmin/Organizations/Index.vue"
if [ -f "$file" ]; then
    add_localization_import "$file"
    sed -i 's/<Head title="Organizations Management"/<Head :title="t('"'"'admin.organizations.title'"'"')"/' "$file"
    sed -i 's/>Organizations Management</>{{ t('"'"'admin.organizations.title'"'"') }}</' "$file"
    sed -i 's/>Create Organization</>{{ t('"'"'admin.organizations.create'"'"') }}</' "$file"
    sed -i 's/>Organization Name</>{{ t('"'"'admin.organizations.name'"'"') }}</' "$file"
    echo "  ✓ Updated Organizations/Index.vue"
fi

# Update Speakers Index page
echo "Updating Speakers pages..."
file="resources/js/Pages/SystemAdmin/Speakers/Index.vue"
if [ -f "$file" ]; then
    add_localization_import "$file"
    sed -i 's/<Head title="Speakers Management"/<Head :title="t('"'"'admin.speakers.title'"'"')"/' "$file"
    sed -i 's/>Speakers Management</>{{ t('"'"'admin.speakers.title'"'"') }}</' "$file"
    sed -i 's/>Add Speaker</>{{ t('"'"'admin.speakers.create'"'"') }}</' "$file"
    echo "  ✓ Updated Speakers/Index.vue"
fi

# Update News Index page
echo "Updating News pages..."
file="resources/js/Pages/SystemAdmin/News/Index.vue"
if [ -f "$file" ]; then
    add_localization_import "$file"
    sed -i 's/<Head title="News Management"/<Head :title="t('"'"'admin.news.title'"'"')"/' "$file"
    sed -i 's/>News Management</>{{ t('"'"'admin.news.title'"'"') }}</' "$file"
    sed -i 's/>Create News</>{{ t('"'"'admin.news.create'"'"') }}</' "$file"
    sed -i 's/>Headline</>{{ t('"'"'admin.news.headline'"'"') }}</' "$file"
    sed -i 's/>Author</>{{ t('"'"'admin.news.author'"'"') }}</' "$file"
    sed -i 's/>Published</>{{ t('"'"'admin.news.published'"'"') }}</' "$file"
    echo "  ✓ Updated News/Index.vue"
fi

# Update Tracks Index page  
echo "Updating Tracks page..."
file="resources/js/Pages/SystemAdmin/Tracks/Index.vue"
if [ -f "$file" ]; then
    add_localization_import "$file"
    sed -i 's/<Head title="Tracks Management"/<Head :title="t('"'"'admin.tracks.title'"'"')"/' "$file"
    sed -i 's/>Tracks Management</>{{ t('"'"'admin.tracks.title'"'"') }}</' "$file"
    sed -i 's/>Create Track</>{{ t('"'"'admin.tracks.create'"'"') }}</' "$file"
    sed -i 's/>Track Name</>{{ t('"'"'admin.tracks.name'"'"') }}</' "$file"
    echo "  ✓ Updated Tracks/Index.vue"
fi

# Update Reports Index page
echo "Updating Reports page..."
file="resources/js/Pages/SystemAdmin/Reports/Index.vue"
if [ -f "$file" ]; then
    add_localization_import "$file"
    sed -i 's/<Head title="Reports"/<Head :title="t('"'"'admin.reports.title'"'"')"/' "$file"
    sed -i 's/>Reports</>{{ t('"'"'admin.reports.title'"'"') }}</' "$file"
    sed -i 's/>Generate Report</>{{ t('"'"'admin.reports.generate'"'"') }}</' "$file"
    echo "  ✓ Updated Reports/Index.vue"
fi

# Update Checkins Index page
echo "Updating Check-ins page..."
file="resources/js/Pages/SystemAdmin/Checkins/Index.vue"
if [ -f "$file" ]; then
    add_localization_import "$file"
    sed -i 's/<Head title="Check-ins"/<Head :title="t('"'"'admin.checkins.title'"'"')"/' "$file"
    sed -i 's/>Check-ins</>{{ t('"'"'admin.checkins.title'"'"') }}</' "$file"
    sed -i 's/>Scan QR Code</>{{ t('"'"'admin.checkins.scan_qr'"'"') }}</' "$file"
    echo "  ✓ Updated Checkins/Index.vue"
fi

# Update QRScanner page
echo "Updating QR Scanner page..."
file="resources/js/Pages/SystemAdmin/QRScanner.vue"
if [ -f "$file" ]; then
    add_localization_import "$file"
    sed -i 's/<Head title="QR Scanner"/<Head :title="t('"'"'admin.qr_scanner.title'"'"')"/' "$file"
    sed -i 's/>QR Scanner</>{{ t('"'"'admin.qr_scanner.title'"'"') }}</' "$file"
    echo "  ✓ Updated QRScanner.vue"
fi

echo ""
echo "✅ Translation updates completed!"
echo ""
echo "Note: This script performs basic string replacements."
echo "Complex templates may need manual review for proper translation implementation."
