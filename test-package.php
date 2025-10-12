<?php

/**
 * Laravel Translation Sync - Quick Test Script
 *
 * Run this to verify your package installation works with free services.
 */

require_once __DIR__ . '/vendor/autoload.php';

echo "🧪 Testing Laravel Translation Sync Package\n";
echo "==========================================\n\n";

// Test 1: Check if classes can be loaded
echo "1. Testing class loading...\n";
try {
    $scanner = new Nabila\TranslationSync\Services\ScannerService();
    echo "   ✅ ScannerService loaded successfully\n";
} catch (Exception $e) {
    echo "   ❌ ScannerService failed: " . $e->getMessage() . "\n";
}

try {
    $writer = new Nabila\TranslationSync\Services\TranslationWriter();
    echo "   ✅ TranslationWriter loaded successfully\n";
} catch (Exception $e) {
    echo "   ❌ TranslationWriter failed: " . $e->getMessage() . "\n";
}

// Test 2: Check free translation services
echo "\n2. Testing free translation services...\n";

$services = [
    'libretranslate' => 'LibreTranslate',
    'mymemory' => 'MyMemory',
    'dummy' => 'Dummy'
];

foreach ($services as $serviceName => $displayName) {
    try {
        $service = Nabila\TranslationSync\Services\Translation\TranslationServiceFactory::create($serviceName);
        $isConfigured = $service->isConfigured();

        if ($isConfigured) {
            echo "   ✅ {$displayName} is configured and ready\n";

            // Test a simple translation
            try {
                $result = $service->translate('Hello World', 'es', 'en');
                echo "   📝 Test translation: 'Hello World' → '{$result}'\n";
            } catch (Exception $e) {
                echo "   ⚠️  Translation test failed: " . $e->getMessage() . "\n";
            }
        } else {
            echo "   ⚠️  {$displayName} is not configured\n";
        }
    } catch (Exception $e) {
        echo "   ❌ {$displayName} failed: " . $e->getMessage() . "\n";
    }
}

// Test 3: Check available services
echo "\n3. Available services:\n";
$availableServices = Nabila\TranslationSync\Services\Translation\TranslationServiceFactory::getAvailableServices();
foreach ($availableServices as $key => $name) {
    echo "   • {$key}: {$name}\n";
}

echo "\n4. Free services:\n";
$freeServices = Nabila\TranslationSync\Services\Translation\TranslationServiceFactory::getFreeServices();
foreach ($freeServices as $key => $name) {
    echo "   • {$key}: {$name}\n";
}

echo "\n🎉 Package test completed!\n";
echo "\n💡 Next steps:\n";
echo "   1. Run: php artisan translations:sync --stats\n";
echo "   2. Run: php artisan translations:sync --translate --auto\n";
echo "   3. Check your lang/ directory for new translation files!\n";
