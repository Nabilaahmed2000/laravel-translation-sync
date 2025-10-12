<?php

namespace Nabila\TranslationSync\Console;

use Illuminate\Console\Command;
use Nabila\TranslationSync\Services\ScannerService;
use Nabila\TranslationSync\Services\TranslationWriter;
use Nabila\TranslationSync\Services\Translation\TranslationServiceFactory;

class SyncTranslations extends Command
{
    protected $signature = 'translations:sync 
                            {--auto : Automatically add translations without confirmation}
                            {--translate : Enable automatic translation of missing keys}
                            {--service= : Translation service to use (google, dummy)}
                            {--source= : Source language code}
                            {--targets= : Comma-separated target language codes}
                            {--dry-run : Show what would be done without making changes}
                            {--stats : Show translation statistics}
                            {--format= : Output format (json, php)}';

    protected $description = 'Find untranslated strings and sync them to translation files with optional automatic translation.';

    protected ScannerService $scanner;
    protected TranslationWriter $writer;

    public function __construct()
    {
        parent::__construct();
        $this->scanner = new ScannerService();
        $this->writer = new TranslationWriter();
    }

    public function handle(): int
    {
        if ($this->option('stats')) {
            return $this->showStatistics();
        }

        $this->info('🔍 Scanning for missing translations...');

        $missing = $this->scanner->findMissingTranslations();

        if (empty($missing)) {
            $this->info('✅ No missing translations found!');
            return Command::SUCCESS;
        }

        $this->displayMissingTranslations($missing);

        if ($this->option('dry-run')) {
            $this->info('🔍 Dry run complete. No changes were made.');
            return Command::SUCCESS;
        }

        return $this->processMissingTranslations($missing);
    }

    /**
     * Display missing translations
     */
    protected function displayMissingTranslations(array $missing): void
    {
        $this->warn("Found " . count($missing) . " missing translation(s):");
        $this->newLine();

        foreach ($missing as $key => $data) {
            $this->line("🔸 <comment>{$key}</comment>");

            if (!empty($data['files'])) {
                $this->line("   📁 Found in: " . implode(', ', array_slice($data['files'], 0, 3)));
                if (count($data['files']) > 3) {
                    $this->line("   📁 ... and " . (count($data['files']) - 3) . " more file(s)");
                }
            }

            if (!empty($data['contexts'][0])) {
                $this->line("   📄 Context:");
                foreach (explode("\n", $data['contexts'][0]) as $contextLine) {
                    $this->line("      " . $contextLine);
                }
            }

            $this->newLine();
        }
    }

    /**
     * Process missing translations
     */
    protected function processMissingTranslations(array $missing): int
    {
        $options = $this->buildTranslationOptions();

        if ($options['auto_translate'] && !$this->writer->isTranslationServiceAvailable()) {
            $this->warn('⚠️  Auto-translation requested but no translation service is available or configured.');
            $this->info('💡 Available services: ' . implode(', ', array_keys(TranslationServiceFactory::getAvailableServices())));
            return Command::FAILURE;
        }

        $processed = 0;
        $errors = 0;

        foreach ($missing as $key => $data) {
            if ($this->shouldProcessKey($key, $options)) {
                $result = $this->processTranslationKey($key, $data, $options);

                if ($result['success']) {
                    $processed++;
                    $this->displayTranslationResult($key, $result);
                } else {
                    $errors++;
                    $this->error("❌ Failed to process: {$key}");
                }
            }
        }

        $this->displaySummary($processed, $errors);

        return $errors > 0 ? Command::FAILURE : Command::SUCCESS;
    }

    /**
     * Build translation options from command arguments
     */
    protected function buildTranslationOptions(): array
    {
        $options = [
            'auto_translate' => $this->option('translate'),
            'fallback_strategy' => 'key',
        ];

        if ($service = $this->option('service')) {
            config(['translation-sync.service' => $service]);
        }

        if ($source = $this->option('source')) {
            config(['translation-sync.source_language' => $source]);
        }

        if ($targets = $this->option('targets')) {
            config(['translation-sync.target_languages' => explode(',', $targets)]);
        }

        if ($format = $this->option('format')) {
            config(['translation-sync.file_format' => $format]);
        }

        return $options;
    }

    /**
     * Check if key should be processed
     */
    protected function shouldProcessKey(string $key, array $options): bool
    {
        if ($this->option('auto')) {
            return true;
        }

        return $this->confirm("Add '{$key}' to translation files?", true);
    }

    /**
     * Process a single translation key
     */
    protected function processTranslationKey(string $key, array $data, array $options): array
    {
        try {
            $sourceValue = $data['key'] ?? $key;
            $results = $this->writer->addToFiles($key, $sourceValue, $options);

            return [
                'success' => true,
                'results' => $results,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Display translation result
     */
    protected function displayTranslationResult(string $key, array $result): void
    {
        $this->info("✅ Added: <comment>{$key}</comment>");

        if (!empty($result['results'])) {
            foreach ($result['results'] as $language => $langResult) {
                $status = $langResult['success'] ? '✅' : '⚠️';
                $method = $this->getMethodDisplayName($langResult['method']);

                $this->line("   {$status} {$language}: {$langResult['value']} <info>({$method})</info>");

                if (!$langResult['success'] && !empty($langResult['error'])) {
                    $this->line("      <fg=red>Error: {$langResult['error']}</>");
                }
            }
        }
    }

    /**
     * Get display name for translation method
     */
    protected function getMethodDisplayName(string $method): string
    {
        return match ($method) {
            'same_language' => 'source',
            'no_translation' => 'manual',
            'fallback' => 'fallback',
            default => class_basename($method),
        };
    }

    /**
     * Display summary
     */
    protected function displaySummary(int $processed, int $errors): void
    {
        $this->newLine();

        if ($processed > 0) {
            $this->info("🎉 Successfully processed {$processed} translation(s)!");
        }

        if ($errors > 0) {
            $this->error("❌ {$errors} error(s) occurred during processing.");
        }

        if ($processed === 0 && $errors === 0) {
            $this->info("ℹ️  No translations were processed.");
        }
    }

    /**
     * Show translation statistics
     */
    protected function showStatistics(): int
    {
        $this->info('📊 Translation Statistics');
        $this->newLine();

        $stats = $this->scanner->getStatistics();

        $this->table([
            'Metric',
            'Value'
        ], [
            ['Total translation keys found', $stats['total_keys']],
            ['Missing translations', $stats['missing_keys']],
            ['Translated keys', $stats['translated_keys']],
            ['Coverage percentage', $stats['coverage_percentage'] . '%'],
        ]);

        // Check translation service status
        $this->newLine();
        $this->info('🔧 Translation Service Status');

        if ($this->writer->isTranslationServiceAvailable()) {
            $service = $this->writer->getTranslationService();
            $serviceName = class_basename(get_class($service));
            $this->info("✅ {$serviceName} is configured and ready");

            $supportedLanguages = $service->getSupportedLanguages();
            $this->line("📋 Supported languages: " . implode(', ', array_slice($supportedLanguages, 0, 10)));
            if (count($supportedLanguages) > 10) {
                $this->line("    ... and " . (count($supportedLanguages) - 10) . " more");
            }
        } else {
            $this->warn("⚠️  No translation service configured or available");
            $this->info("💡 Available services: " . implode(', ', array_keys(TranslationServiceFactory::getAvailableServices())));
        }

        return Command::SUCCESS;
    }
}
