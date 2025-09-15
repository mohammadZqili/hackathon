<?php
// Simple test to check if translation files exist and are readable

$enAdminPath = __DIR__ . '/resources/lang/en/admin.php';
$arAdminPath = __DIR__ . '/resources/lang/ar/admin.php';

echo "=== Translation Files Check ===\n\n";

// Check English admin translations
if (file_exists($enAdminPath)) {
    echo "✓ English admin.php exists\n";
    $enTranslations = include $enAdminPath;
    echo "  - Total sections: " . count($enTranslations) . "\n";
    echo "  - Sample keys: " . implode(', ', array_slice(array_keys($enTranslations), 0, 5)) . "\n";
    
    if (isset($enTranslations['dashboard']['title'])) {
        echo "  - dashboard.title: " . $enTranslations['dashboard']['title'] . "\n";
    }
} else {
    echo "✗ English admin.php NOT FOUND\n";
}

echo "\n";

// Check Arabic admin translations
if (file_exists($arAdminPath)) {
    echo "✓ Arabic admin.php exists\n";
    $arTranslations = include $arAdminPath;
    echo "  - Total sections: " . count($arTranslations) . "\n";
    echo "  - Sample keys: " . implode(', ', array_slice(array_keys($arTranslations), 0, 5)) . "\n";
    
    if (isset($arTranslations['dashboard']['title'])) {
        echo "  - dashboard.title: " . $arTranslations['dashboard']['title'] . "\n";
    }
} else {
    echo "✗ Arabic admin.php NOT FOUND\n";
}

echo "\n=== Flatten Test ===\n";

function flattenArray($array, $prefix = '') {
    $result = [];
    foreach ($array as $key => $value) {
        $newKey = $prefix ? "{$prefix}.{$key}" : $key;
        if (is_array($value)) {
            $result = array_merge($result, flattenArray($value, $newKey));
        } else {
            $result[$newKey] = $value;
        }
    }
    return $result;
}

if (isset($enTranslations)) {
    $flattened = flattenArray($enTranslations, 'admin');
    echo "Flattened English keys (first 10):\n";
    $keys = array_slice(array_keys($flattened), 0, 10);
    foreach ($keys as $key) {
        echo "  - $key: " . $flattened[$key] . "\n";
    }
}
