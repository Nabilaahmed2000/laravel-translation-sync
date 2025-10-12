# Laravel Translation Sync

A powerful Laravel package that automatically detects untranslated strings in your application and provides automatic translation capabilities using **free** translation services.

## ✨ Features

- 🔍 **Smart Detection**: Scans your entire Laravel application for untranslated strings
- 🌐 **Free Auto Translation**: Uses LibreTranslate and MyMemory (completely free, no API keys needed!)
- 📊 **Translation Statistics**: Get detailed insights about your translation coverage
- 🔧 **Flexible Configuration**: Customizable scan paths, file formats, and translation patterns
- 🎯 **Multiple File Formats**: Support for both JSON and PHP translation files
- 📱 **Rich Console Interface**: Beautiful command-line interface with progress indicators
- 🛡️ **Safe Operation**: Dry-run mode to preview changes before applying them

## 🚀 Quick Start (5 minutes!)

### 1. Install the Package

```bash
composer require nabila/laravel-translation-sync
```

### 2. Publish Configuration

```bash
php artisan vendor:publish --tag=translation-sync-config
```

### 3. Start Translating! (No API keys needed!)

```bash
# Scan and auto-translate using FREE services
php artisan translations:sync --translate --auto

# Check your translation coverage
php artisan translations:sync --stats
```

That's it! 🎉 Your Laravel app now has automatic translation capabilities using completely free services.

## Installation

Install the package via Composer:

```bash
composer require nabila/laravel-translation-sync
```

Publish the configuration file:

```bash
php artisan vendor:publish --tag=translation-sync-config
```

## ⚡ Zero-Configuration Setup

The package comes pre-configured to work with **free translation services**. No API keys, no billing setup required!

```env
# Your .env file (these are the defaults)
TRANSLATION_SERVICE=libretranslate  # Free service!
TRANSLATION_AUTO_TRANSLATE=true
```

Start using it immediately:

```bash
php artisan translations:sync --translate --auto
```

## Configuration

The package comes with a comprehensive configuration file. Here are the key settings:

```php
// config/translation-sync.php

return [
    // Translation service: 'google', 'deepl', 'azure', 'aws', 'dummy'
    'service' => env('TRANSLATION_SERVICE', 'google'),

    // Source language (language of your translation keys)
    'source_language' => env('TRANSLATION_SOURCE_LANG', 'en'),

    // Enable automatic translation
    'auto_translate' => env('TRANSLATION_AUTO_TRANSLATE', false),

    // Translation service configurations
    'services' => [
        'google' => [
            'api_key' => env('GOOGLE_TRANSLATE_API_KEY', null),
        ],
        // ... other services
    ],
];
```

### Environment Variables

Add these to your `.env` file:

```env
TRANSLATION_SERVICE=libretranslate  # FREE - No API key needed!
TRANSLATION_SOURCE_LANG=en
TRANSLATION_AUTO_TRANSLATE=true
# No API key needed for free services!
```

> 🎉 **No billing, no API keys, no setup required!** The package uses free translation services by default.

> 📋 **Need help getting a Google Translate API key?** Check out our [Quick Setup Guide](GOOGLE_TRANSLATE_SETUP.md) for step-by-step instructions!

## Usage

### Basic Scanning

Scan for missing translations:

```bash
php artisan translations:sync
```

### Automatic Translation

Enable automatic translation with the `--translate` flag:

```bash
php artisan translations:sync --translate
```

### Advanced Options

```bash
# Auto-approve all translations without confirmation
php artisan translations:sync --auto --translate

# Use a specific translation service
php artisan translations:sync --translate --service=google

# Set source and target languages
php artisan translations:sync --translate --source=en --targets=es,fr,de

# Dry run (preview changes without making them)
php artisan translations:sync --dry-run

# Show translation statistics
php artisan translations:sync --stats

# Use PHP files instead of JSON
php artisan translations:sync --format=php
```

## Translation Services

### 🚀 **Free Services (No API Keys Required!)**

#### LibreTranslate (Default - Recommended)
**Completely free, open-source translation service**

```env
TRANSLATION_SERVICE=libretranslate
```

- ✅ **No API key required**
- ✅ **No billing needed**
- ✅ **Open source and self-hosted**
- ✅ **Supports 50+ languages**
- ✅ **Fast and reliable**

#### MyMemory
**Community-driven translation service**

```env
TRANSLATION_SERVICE=mymemory
```

- ✅ **No API key required**
- ✅ **No billing needed**
- ✅ **Community translations**
- ✅ **Supports 80+ languages**

### 💰 **Paid Services**

#### Google Translate
**Professional translation service**

```env
TRANSLATION_SERVICE=google
GOOGLE_TRANSLATE_API_KEY=your-api-key
```

- ✅ **High quality translations**
- ✅ **Supports all major languages**
- ⚠️ **Requires billing setup**
- ⚠️ **API key required**

> 📋 **Need help with Google Translate API?** Check out our [Quick Setup Guide](GOOGLE_TRANSLATE_SETUP.md)

Follow these steps to obtain your Google Translate API key:

1. **Go to Google Cloud Console**
   - Visit [Google Cloud Console](https://console.cloud.google.com/)
   - Sign in with your Google account

2. **Create or Select a Project**
   - Click on the project dropdown at the top
   - Either select an existing project or click "New Project"
   - If creating new: Enter project name and click "Create"

3. **Enable the Cloud Translation API**
   - In the left sidebar, go to "APIs & Services" > "Library"
   - Search for "Cloud Translation API"
   - Click on "Cloud Translation API" from the results
   - Click the "Enable" button

4. **Create API Credentials**
   - Go to "APIs & Services" > "Credentials"
   - Click "Create Credentials" > "API key"
   - Your API key will be generated and displayed
   - **Important**: Copy and save this key securely

5. **Secure Your API Key (Recommended)**
   - Click on the API key name to edit it
   - Under "API restrictions", select "Restrict key"
   - Choose "Cloud Translation API" from the list
   - Under "Application restrictions", you can:
     - Set HTTP referrers for web apps
     - Set IP addresses for server apps
     - Or leave unrestricted for development

6. **Set Up Billing (Required)**
   - Google Translate API requires a billing account
   - Go to "Billing" in the left sidebar
   - Link a billing account or create a new one
   - **Note**: Google provides free credits for new accounts

7. **Add to Your Environment**
   ```env
   GOOGLE_TRANSLATE_API_KEY=AIzaSyD...your-actual-key-here
   ```

#### Pricing Information
- **Free Tier**: $300 in free credits for new Google Cloud accounts
- **Cost**: $20 per 1 million characters translated
- **Monthly Free Usage**: First 500,000 characters per month are free
- Check current pricing at [Google Cloud Pricing](https://cloud.google.com/translate/pricing)

#### API Limits
- **Default Quota**: 1,000,000 characters per 100 seconds
- **Character Limit**: 30,000 characters per request
- **Rate Limit**: Can be increased by requesting quota increases

### 🧪 **Testing Service**

#### Dummy Service
**For development and testing**

```env
TRANSLATION_SERVICE=dummy
```

The dummy service is useful for testing and development. It simply appends the target language code to the original text.

## Supported Translation Patterns

The package automatically detects these translation patterns in your code:

- `__('key')` - Laravel's `__()` helper
- `@lang('key')` - Blade directive
- `trans('key')` - Laravel's `trans()` helper
- `Lang::get('key')` - Facade usage

## File Formats

### JSON Format (default)

```json
{
    "Welcome": "Bienvenido",
    "Hello World": "Hola Mundo"
}
```

### PHP Format

```php
<?php

return [
    'Welcome' => 'Bienvenido',
    'Hello World' => 'Hola Mundo',
];
```

## Advanced Features

### Translation Statistics

Get detailed statistics about your translation coverage:

```bash
php artisan translations:sync --stats
```

This shows:
- Total translation keys found
- Missing translations
- Coverage percentage
- Translation service status

### Context-Aware Detection

The package provides context for each missing translation, showing:
- Files where the translation key was found
- Code context around the usage
- Multiple occurrences across your application

### Placeholder Preservation

The package intelligently handles Laravel translation placeholders:

```php
// Original
__('Welcome :name to our site')

// After translation (Spanish)
'Welcome :name to our site' => 'Bienvenido :name a nuestro sitio'
```

## Examples

### Basic Usage

```php
// In your Blade template
<h1>{{ __('Welcome to our site') }}</h1>
<p>{{ __('This is a new feature') }}</p>

// In your controller
return response()->json([
    'message' => __('Data saved successfully')
]);
```

Run the sync command:

```bash
php artisan translations:sync --translate --auto
```

This will:
1. Detect the missing translations
2. Automatically translate them to your configured target languages
3. Save them to your translation files

### Configuration Example

```php
// config/translation-sync.php

return [
    'service' => 'google',
    'source_language' => 'en',
    'target_languages' => ['es', 'fr', 'de', 'it'],
    'auto_translate' => true,
    
    'scan_paths' => [
        'app',
        'resources',
        'routes',
        'custom-modules', // Add custom paths
    ],
    
    'patterns' => [
        "/__\(['\"](.+?)['\"]\)/",
        "/@lang\(['\"](.+?)['\"]\)/",
        "/trans\(['\"](.+?)['\"]\)/",
        "/Lang::get\(['\"](.+?)['\"]\)/",
        "/CustomHelper::translate\(['\"](.+?)['\"]\)/", // Custom pattern
    ],
];
```

## Troubleshooting

### Common Issues and Solutions

#### 1. Google Translate API Key Issues

**Problem**: "Google Translate service is not properly configured"

**Solutions**:
- Verify your API key is correct in `.env` file
- Ensure the Cloud Translation API is enabled in Google Cloud Console
- Check that billing is set up for your Google Cloud project
- Verify your API key has the correct restrictions (if any)

**Test your API key**:
```bash
# Test if your API key works
curl -X POST \
  "https://translation.googleapis.com/language/translate/v2?key=YOUR_API_KEY" \
  -d "q=Hello World&target=es&source=en"
```

#### 2. Permission Issues

**Problem**: "Failed to create translation file" or permission errors

**Solutions**:
```bash
# Make sure Laravel can write to the lang directory
chmod -R 755 resources/lang/
chown -R www-data:www-data resources/lang/  # On Linux servers
```

#### 3. Missing Translations Not Detected

**Problem**: Known untranslated strings are not being found

**Solutions**:
- Check if your translation patterns are in the scan paths
- Verify file extensions are included in configuration
- Use custom patterns for non-standard translation methods:

```php
// config/translation-sync.php
'patterns' => [
    "/__\(['\"](.+?)['\"]\)/",
    "/@lang\(['\"](.+?)['\"]\)/",
    "/YourCustomHelper::trans\(['\"](.+?)['\"]\)/", // Add custom patterns
],
```

#### 4. Quota Exceeded Errors

**Problem**: "Quota exceeded" or rate limit errors

**Solutions**:
- Check your Google Cloud Console quotas
- Request quota increases if needed
- Use smaller batches with the `--dry-run` flag first
- Consider using the dummy service for development:

```env
TRANSLATION_SERVICE=dummy  # For development/testing
```

#### 5. Composer Dependencies

**Problem**: Package installation fails

**Solutions**:
```bash
# Install required dependencies
composer require guzzlehttp/guzzle
composer require stichoza/google-translate-php

# If you get version conflicts, try:
composer update --with-dependencies
```

#### 6. Configuration Not Loading

**Problem**: Configuration changes not taking effect

**Solutions**:
```bash
# Clear config cache
php artisan config:clear
php artisan config:cache

# Re-publish config if needed
php artisan vendor:publish --tag=translation-sync-config --force
```

### Debugging Commands

```bash
# Check translation statistics
php artisan translations:sync --stats

# See what would be translated without making changes
php artisan translations:sync --dry-run

# Test with dummy service
php artisan translations:sync --service=dummy --translate

# Check service status
php artisan tinker
# Then run: app(Nabila\TranslationSync\Services\TranslationWriter::class)->isTranslationServiceAvailable()
```

### Environment Validation

Create a simple test to validate your setup:

```php
// In tinker or a test route
use Nabila\TranslationSync\Services\Translation\TranslationServiceFactory;

$service = TranslationServiceFactory::create('google');
if ($service->isConfigured()) {
    echo "✅ Google Translate is configured correctly\n";
    $result = $service->translate('Hello', 'es');
    echo "Test translation: {$result}\n";
} else {
    echo "❌ Google Translate is not configured\n";
}
```

## Error Handling

The package includes comprehensive error handling:

- **Translation Service Errors**: Falls back to original text or configured fallback strategy
- **File Permission Issues**: Clear error messages with suggested solutions
- **API Quota Limits**: Graceful handling with retry suggestions
- **Network Issues**: Timeout handling and fallback options

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This package is open-sourced software licensed under the [MIT license](LICENSE.md).

## Support

If you discover any security vulnerabilities or bugs, please send an e-mail to the package maintainer.

## Changelog

### v2.0.0 (Enhanced Version)
- ✨ **FREE Translation Services**: Added LibreTranslate and MyMemory (no API keys needed!)
- 🌐 **Zero-Configuration**: Works out-of-the-box with free services
- 📦 **Packagist Ready**: Proper versioning and composer configuration
- 🔧 **Enhanced Configuration**: LibreTranslate as default free service
- 🎯 **Improved Documentation**: Clear installation and usage guides
- 🧪 **Better Testing**: Support for free service testing

### v1.0.0 (Original Version)
- 🔍 Basic translation key detection
- 📁 Simple file writing capabilities
- 🎯 Basic console command