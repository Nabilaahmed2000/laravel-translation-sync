<?php

namespace Nabila\TranslationSync;

use Illuminate\Support\ServiceProvider;
use Nabila\TranslationSync\Console\SyncTranslations;
use Nabila\TranslationSync\Console\InitializeTranslations;

class TranslationSyncServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Merge configuration
        $this->mergeConfigFrom(
            __DIR__ . '/../config/translation-sync.php',
            'translation-sync'
        );
    }

    public function boot()
    {
        // Publish configuration
        $this->publishes([
            __DIR__ . '/../config/translation-sync.php' => config_path('translation-sync.php'),
        ], 'translation-sync-config');

        // Publish example environment file
        $this->publishes([
            __DIR__ . '/../.env.example' => base_path('.env.translation-sync.example'),
        ], 'translation-sync-env');

        // Register console commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                SyncTranslations::class,
                InitializeTranslations::class,
            ]);
        }
    }
}
