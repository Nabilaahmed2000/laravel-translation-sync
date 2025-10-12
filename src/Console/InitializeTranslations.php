<?php

namespace Nabila\TranslationSync\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InitializeTranslations extends Command
{
    protected $signature = 'translations:init 
                            {--locales= : Comma-separated list of locale codes to initialize}
                            {--force : Overwrite existing translation files}';

    protected $description = 'Initialize translation files for specified locales';

    public function handle(): int
    {
        $locales = $this->getLocales();

        if (empty($locales)) {
            $this->error('No locales specified. Use --locales=en,es,fr or configure app.locales');
            return Command::FAILURE;
        }

        $this->info('🚀 Initializing translation files for: ' . implode(', ', $locales));
        $this->newLine();

        $created = 0;
        $skipped = 0;

        foreach ($locales as $locale) {
            $result = $this->createTranslationFile($locale);

            if ($result) {
                $created++;
                $this->info("✅ Created translation file for: {$locale}");
            } else {
                $skipped++;
                $this->line("⏭️  Skipped existing file for: {$locale}");
            }
        }

        $this->newLine();
        $this->info("📊 Summary: {$created} created, {$skipped} skipped");

        if ($created > 0) {
            $this->info('🎉 Translation files initialized successfully!');
            $this->info('💡 Run "php artisan translations:sync --translate" to start translating');
        }

        return Command::SUCCESS;
    }

    /**
     * Get locales from option or config
     */
    protected function getLocales(): array
    {
        if ($locales = $this->option('locales')) {
            return array_map('trim', explode(',', $locales));
        }

        // Try to get from app config
        $appLocales = config('app.locales', []);

        if (!empty($appLocales)) {
            return is_array($appLocales) ? array_keys($appLocales) : [$appLocales];
        }

        // Fallback to app.locale
        return [config('app.locale', 'en')];
    }

    /**
     * Create translation file for locale
     */
    protected function createTranslationFile(string $locale): bool
    {
        $format = config('translation-sync.file_format', 'json');

        if ($format === 'json') {
            return $this->createJsonFile($locale);
        } else {
            return $this->createPhpFile($locale);
        }
    }

    /**
     * Create JSON translation file
     */
    protected function createJsonFile(string $locale): bool
    {
        $path = resource_path("lang/{$locale}.json");

        if (File::exists($path) && !$this->option('force')) {
            return false;
        }

        // Create directory if needed
        $directory = dirname($path);
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        // Create file with basic structure
        $content = json_encode([
            'Welcome' => 'Welcome',
            'Hello' => 'Hello',
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        File::put($path, $content);

        return true;
    }

    /**
     * Create PHP translation file
     */
    protected function createPhpFile(string $locale): bool
    {
        $directory = resource_path("lang/{$locale}");
        $path = "{$directory}/messages.php";

        if (File::exists($path) && !$this->option('force')) {
            return false;
        }

        // Create directory if needed
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        // Create file with basic structure
        $content = <<<PHP
<?php

return [
    'Welcome' => 'Welcome',
    'Hello' => 'Hello',
];
PHP;

        File::put($path, $content);

        return true;
    }
}
