<?php

/**
 * Laravel Translation Sync - Usage Examples
 * 
 * This file contains practical examples of how to use the translation sync package
 * in your Laravel application.
 */

// ============================================================================
// 1. BASIC USAGE EXAMPLES
// ============================================================================

// In your Blade templates (resources/views/welcome.blade.php)
?>
<h1>{{ __('Welcome to our website') }}</h1>
<p>{{ __('This is a new feature we\'re excited to share') }}</p>
<nav>
    <a href="#">{{ __('Home') }}</a>
    <a href="#">{{ __('About Us') }}</a>
    <a href="#">{{ __('Contact') }}</a>
</nav>

<?php
// In your controllers (app/Http/Controllers/HomeController.php)
class HomeController extends Controller
{
    public function index()
    {
        return view('home', [
            'title' => __('Dashboard'),
            'welcome_message' => __('Welcome back, :name!'),
        ]);
    }

    public function store(Request $request)
    {
        // Process data...
        
        return response()->json([
            'message' => __('Data saved successfully'),
            'status' => 'success'
        ]);
    }
}

// In your models (app/Models/User.php)
class User extends Model
{
    public function getStatusMessageAttribute()
    {
        return match($this->status) {
            'active' => __('Account is active'),
            'inactive' => __('Account is inactive'),
            'suspended' => __('Account is suspended'),
            default => __('Unknown status'),
        };
    }
}

// ============================================================================
// 2. ADVANCED USAGE WITH PLACEHOLDERS
// ============================================================================

// Complex translations with multiple placeholders
__('Hello :name, you have :count unread messages', [
    'name' => $user->name,
    'count' => $user->unread_messages_count
]);

// Pluralization (these will be detected separately)
trans_choice('You have :count item|You have :count items', $count, ['count' => $count]);

// Conditional translations
$message = $user->isAdmin() 
    ? __('Welcome, Administrator :name') 
    : __('Welcome, :name');

// ============================================================================
// 3. BLADE DIRECTIVE EXAMPLES
// ============================================================================
?>

{{-- Using @lang directive --}}
@lang('Please confirm your email address')

{{-- In conditional statements --}}
@if($user->isVerified())
    <p>@lang('Your account is verified')</p>
@else
    <p>@lang('Please verify your account')</p>
@endif

{{-- In loops --}}
@foreach($notifications as $notification)
    <div class="alert">
        {{ __($notification->message) }}
        <small>{{ __('Received at :time', ['time' => $notification->created_at]) }}</small>
    </div>
@endforeach

<?php
// ============================================================================
// 4. COMMAND LINE USAGE EXAMPLES
// ============================================================================

/*
# Basic scanning and manual approval
php artisan translations:sync

# Automatic translation with Google Translate
php artisan translations:sync --translate --auto

# Dry run to see what would be translated
php artisan translations:sync --translate --dry-run

# Initialize translation files for specific locales
php artisan translations:init --locales=es,fr,de,it

# Get translation statistics
php artisan translations:sync --stats

# Use specific translation service
php artisan translations:sync --translate --service=google

# Specify source and target languages
php artisan translations:sync --translate --source=en --targets=es,fr,de

# Use PHP file format instead of JSON
php artisan translations:sync --translate --format=php
*/

// ============================================================================
// 5. CONFIGURATION EXAMPLES
// ============================================================================

// config/translation-sync.php
return [
    // Use Google Translate for automatic translations
    'service' => 'google',
    
    // Source language (your primary language)
    'source_language' => 'en',
    
    // Target languages (will translate to these)
    'target_languages' => ['es', 'fr', 'de', 'it', 'pt'],
    
    // Enable automatic translation
    'auto_translate' => true,
    
    // Scan additional directories
    'scan_paths' => [
        'app',
        'resources',
        'routes',
        'packages/custom-package/src', // Custom package
    ],
    
    // Additional file extensions to scan
    'file_extensions' => ['php', 'blade.php', 'vue'],
    
    // Custom translation patterns
    'patterns' => [
        "/__\(['\"](.+?)['\"]\)/",
        "/@lang\(['\"](.+?)['\"]\)/",
        "/trans\(['\"](.+?)['\"]\)/",
        "/Lang::get\(['\"](.+?)['\"]\)/",
        "/MyHelper::translate\(['\"](.+?)['\"]\)/", // Custom helper
    ],
];

// ============================================================================
// 6. ENVIRONMENT CONFIGURATION
// ============================================================================

/*
# .env file configuration
TRANSLATION_SERVICE=google
TRANSLATION_SOURCE_LANG=en
TRANSLATION_TARGET_LANGS=es,fr,de,it
TRANSLATION_AUTO_TRANSLATE=true
TRANSLATION_FILE_FORMAT=json

# Google Translate API
GOOGLE_TRANSLATE_API_KEY=your-google-api-key-here

# DeepL API
DEEPL_API_KEY=your-deepl-api-key-here
DEEPL_API_URL=https://api-free.deepl.com
*/

// ============================================================================
// 7. PROGRAMMATIC USAGE
// ============================================================================

use Nabila\TranslationSync\Services\ScannerService;
use Nabila\TranslationSync\Services\TranslationWriter;

// Scan for missing translations programmatically
$scanner = new ScannerService();
$missing = $scanner->findMissingTranslations();

foreach ($missing as $key => $data) {
    echo "Missing: {$key}\n";
    echo "Found in: " . implode(', ', $data['files']) . "\n\n";
}

// Add translations programmatically
$writer = new TranslationWriter();
$results = $writer->addToFiles('New translation key', null, [
    'auto_translate' => true,
]);

foreach ($results as $language => $result) {
    if ($result['success']) {
        echo "Added to {$language}: {$result['value']}\n";
    } else {
        echo "Failed for {$language}: {$result['error']}\n";
    }
}

// Get translation statistics
$stats = $scanner->getStatistics();
echo "Coverage: {$stats['coverage_percentage']}%\n";
echo "Missing: {$stats['missing_keys']} out of {$stats['total_keys']}\n";

// ============================================================================
// 8. BEST PRACTICES
// ============================================================================

// ✅ Good: Use descriptive translation keys
__('welcome.title')
__('user.profile.updated')
__('validation.email.required')

// ❌ Avoid: Dynamic or concatenated keys (won't be detected)
__($dynamicKey)
__('user.' . $type . '.message')

// ✅ Good: Use consistent placeholder naming
__('Hello :name, welcome to :site')

// ✅ Good: Keep translations short and contextual
__('Save Changes')
__('Delete Item')
__('Confirm Action')

// ✅ Good: Use namespacing for organization
__('auth.login.title')
__('auth.register.success')
__('dashboard.stats.users')

?>