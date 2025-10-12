<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Translation Service
    |--------------------------------------------------------------------------
    |
    | Choose the translation service to use for automatic translations.
    | Available: 'libretranslate' (free), 'mymemory' (free), 'google' (paid), 'dummy' (testing)
    |
    */
    'service' => env('TRANSLATION_SERVICE', 'libretranslate'),

    /*
    |--------------------------------------------------------------------------
    | Translation API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for various translation services
    |
    */
    'services' => [
        'google' => [
            'api_key' => env('GOOGLE_TRANSLATE_API_KEY', null),
        ],
        'deepl' => [
            'api_key' => env('DEEPL_API_KEY', null),
            'url' => env('DEEPL_API_URL', 'https://api-free.deepl.com'),
        ],
        'azure' => [
            'api_key' => env('AZURE_TRANSLATOR_KEY', null),
            'region' => env('AZURE_TRANSLATOR_REGION', null),
            'endpoint' => env('AZURE_TRANSLATOR_ENDPOINT', 'https://api.cognitive.microsofttranslator.com'),
        ],
        'aws' => [
            'access_key_id' => env('AWS_ACCESS_KEY_ID', null),
            'secret_access_key' => env('AWS_SECRET_ACCESS_KEY', null),
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Source Language
    |--------------------------------------------------------------------------
    |
    | The source language for translations. This should be the language
    | your translation keys are written in.
    |
    */
    'source_language' => env('TRANSLATION_SOURCE_LANG', 'en'),

    /*
    |--------------------------------------------------------------------------
    | Target Languages
    |--------------------------------------------------------------------------
    |
    | The languages to translate into. If null, it will use all configured
    | app locales except the source language.
    |
    */
    'target_languages' => env('TRANSLATION_TARGET_LANGS', ['ar']),

    /*
    |--------------------------------------------------------------------------
    | Scan Paths
    |--------------------------------------------------------------------------
    |
    | Directories to scan for translation keys
    |
    */
    'scan_paths' => [
        'app',
        'resources',
        'routes',
    ],

    /*
    |--------------------------------------------------------------------------
    | File Extensions
    |--------------------------------------------------------------------------
    |
    | File extensions to scan for translation keys
    |
    */
    'file_extensions' => ['php', 'blade.php'],

    /*
    |--------------------------------------------------------------------------
    | Translation Patterns
    |--------------------------------------------------------------------------
    |
    | Regex patterns to find translation keys in your code
    |
    */
    'patterns' => [
        "/__\(['\"](.+?)['\"]\)/",
        "/@lang\(['\"](.+?)['\"]\)/",
        "/trans\(['\"](.+?)['\"]\)/",
        "/Lang::get\(['\"](.+?)['\"]\)/",
    ],

    /*
    |--------------------------------------------------------------------------
    | Auto Translation
    |--------------------------------------------------------------------------
    |
    | Whether to automatically translate missing keys or prompt for each one
    |
    */
    'auto_translate' => env('TRANSLATION_AUTO_TRANSLATE', false),

    /*
    |--------------------------------------------------------------------------
    | Fallback Translation
    |--------------------------------------------------------------------------
    |
    | What to use as translation when auto-translation fails
    | Options: 'key' (use the key itself), 'empty' (empty string), 'ask' (prompt user)
    |
    */
    'fallback_strategy' => env('TRANSLATION_FALLBACK_STRATEGY', 'key'),

    /*
    |--------------------------------------------------------------------------
    | Translation File Format
    |--------------------------------------------------------------------------
    |
    | Format for translation files: 'json' or 'php'
    |
    */
    'file_format' => env('TRANSLATION_FILE_FORMAT', 'php'),
];
