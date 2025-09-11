<?php
// Test translation loading

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Set Arabic locale
App::setLocale('ar');

// Load admin translations
$adminTranslations = include resource_path("lang/ar/admin.php");

// Flatten function
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

$flattened = flattenArray($adminTranslations, 'admin');

echo "Sample Arabic translations:\n";
echo "admin.dashboard.title: " . ($flattened['admin.dashboard.title'] ?? 'NOT FOUND') . "\n";
echo "admin.ideas.title: " . ($flattened['admin.ideas.title'] ?? 'NOT FOUND') . "\n";
echo "admin.teams.title: " . ($flattened['admin.teams.title'] ?? 'NOT FOUND') . "\n";
echo "\nTotal translations: " . count($flattened) . "\n";
