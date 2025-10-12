<?php

namespace Nabila\TranslationSync\Services;

use Nabila\TranslationSync\Services\Translation\TranslationServiceFactory;
use Nabila\TranslationSync\Contracts\TranslationServiceInterface;
use Illuminate\Support\Facades\File;
use Exception;

class TranslationWriter
{
    protected array $config;
    protected ?TranslationServiceInterface $translationService = null;

    public function __construct()
    {
        $this->config = config('translation-sync', []);
        $this->initializeTranslationService();
    }

    /**
     * Add translations to files with automatic translation
     *
     * @param string $key
     * @param string|null $sourceValue
     * @param array $options
     * @return array
     */
    public function addToFiles(string $key, ?string $sourceValue = null, array $options = []): array
    {
        $sourceValue = $sourceValue ?: $key;
        $sourceLanguage = $this->config['source_language'] ?? 'en';
        $targetLanguages = $this->getTargetLanguages();
        $results = [];

        foreach ($targetLanguages as $language) {
            try {
                $translatedValue = $this->getTranslation($sourceValue, $language, $sourceLanguage, $options);
                $this->writeToFile($key, $translatedValue, $language);

                $results[$language] = [
                    'success' => true,
                    'value' => $translatedValue,
                    'method' => $this->getTranslationMethod($language, $sourceLanguage)
                ];
            } catch (Exception $e) {
                $fallbackValue = $this->getFallbackValue($key, $sourceValue, $options);
                $this->writeToFile($key, $fallbackValue, $language);

                $results[$language] = [
                    'success' => false,
                    'value' => $fallbackValue,
                    'error' => $e->getMessage(),
                    'method' => 'fallback'
                ];
            }
        }

        return $results;
    }

    /**
     * Add multiple translations in batch
     *
     * @param array $keys
     * @param array $options
     * @return array
     */
    public function addMultipleToFiles(array $keys, array $options = []): array
    {
        $results = [];

        foreach ($keys as $key => $data) {
            $sourceValue = is_array($data) ? ($data['key'] ?? $key) : $data;
            $results[$key] = $this->addToFiles($key, $sourceValue, $options);
        }

        return $results;
    }

    /**
     * Get translation for a key
     *
     * @param string $text
     * @param string $targetLanguage
     * @param string $sourceLanguage
     * @param array $options
     * @return string
     */
    protected function getTranslation(string $text, string $targetLanguage, string $sourceLanguage, array $options = []): string
    {
        // If target language is the same as source language, return original text
        if ($targetLanguage === $sourceLanguage) {
            return $text;
        }

        // Check if auto-translation is disabled
        if (!($options['auto_translate'] ?? $this->config['auto_translate'] ?? false)) {
            return $text;
        }

        // Use translation service if available
        if ($this->translationService && $this->translationService->isConfigured()) {
            return $this->translationService->translate($text, $targetLanguage, $sourceLanguage);
        }

        // Return original text if no translation service available
        return $text;
    }

    /**
     * Get fallback value when translation fails
     *
     * @param string $key
     * @param string $sourceValue
     * @param array $options
     * @return string
     */
    protected function getFallbackValue(string $key, string $sourceValue, array $options = []): string
    {
        $strategy = $options['fallback_strategy'] ?? $this->config['fallback_strategy'] ?? 'key';

        return match ($strategy) {
            'empty' => '',
            'source' => $sourceValue,
            'key' => $key,
            default => $key
        };
    }

    /**
     * Write translation to file
     *
     * @param string $key
     * @param string $value
     * @param string $language
     */
    protected function writeToFile(string $key, string $value, string $language): void
    {
        $format = $this->config['file_format'] ?? 'json';

        if ($format === 'json') {
            $this->writeToJsonFile($key, $value, $language);
        } else {
            $this->writeToPhpFile($key, $value, $language);
        }
    }

    /**
     * Write to JSON translation file
     *
     * @param string $key
     * @param string $value
     * @param string $language
     */
    protected function writeToJsonFile(string $key, string $value, string $language): void
    {
        $path = resource_path("lang/{$language}.json");

        // Create directory if it doesn't exist
        $directory = dirname($path);
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        // Create file with empty object if it doesn't exist
        if (!File::exists($path)) {
            File::put($path, json_encode(new \stdClass, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }

        // Read existing translations
        $content = File::get($path);
        $translations = json_decode($content, true) ?? [];

        // Add new translation
        $translations[$key] = $value;

        // Sort translations alphabetically
        ksort($translations);

        // Write back to file
        File::put(
            $path,
            json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }

    /**
     * Write to PHP translation file
     *
     * @param string $key
     * @param string $value
     * @param string $language
     */
    protected function writeToPhpFile(string $key, string $value, string $language): void
    {
        $path = resource_path("lang/{$language}/messages.php");

        // Create directory if it doesn't exist
        $directory = dirname($path);
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        // Create file with empty array if it doesn't exist
        if (!File::exists($path)) {
            File::put($path, "<?php\n\nreturn [\n];\n");
        }

        // Read existing translations
        $translations = File::exists($path) ? include($path) : [];

        // Add new translation
        $translations[$key] = $value;

        // Sort translations alphabetically
        ksort($translations);

        // Generate PHP array string
        $content = "<?php\n\nreturn [\n";
        foreach ($translations as $k => $v) {
            $escapedKey = addslashes($k);
            $escapedValue = addslashes($v);
            $content .= "    '{$escapedKey}' => '{$escapedValue}',\n";
        }
        $content .= "];\n";

        // Write back to file
        File::put($path, $content);
    }

    /**
     * Get target languages for translation
     *
     * @return array
     */
    protected function getTargetLanguages(): array
    {
        $sourceLanguage = $this->config['source_language'] ?? 'en';

        // Get from config or use app locales
        $targetLanguages = $this->config['target_languages'];

        if (is_string($targetLanguages)) {
            $targetLanguages = explode(',', $targetLanguages);
        }

        if (empty($targetLanguages)) {
            // Fallback to app locales
            $appLocales = config('app.locales', [config('app.locale', 'en')]);
            $targetLanguages = is_array($appLocales) ? array_keys($appLocales) : [$appLocales];
        }

        // Remove source language from targets
        return array_filter($targetLanguages, fn($lang) => trim($lang) !== $sourceLanguage);
    }

    /**
     * Initialize translation service
     */
    protected function initializeTranslationService(): void
    {
        try {
            $serviceName = $this->config['service'] ?? 'dummy';
            $serviceConfig = $this->config['services'][$serviceName] ?? [];

            $this->translationService = TranslationServiceFactory::create($serviceName, $serviceConfig);
        } catch (Exception $e) {
            // Fallback to dummy service
            $this->translationService = TranslationServiceFactory::create('dummy');
        }
    }

    /**
     * Get translation method used
     *
     * @param string $targetLanguage
     * @param string $sourceLanguage
     * @return string
     */
    protected function getTranslationMethod(string $targetLanguage, string $sourceLanguage): string
    {
        if ($targetLanguage === $sourceLanguage) {
            return 'same_language';
        }

        if ($this->translationService && $this->translationService->isConfigured()) {
            return get_class($this->translationService);
        }

        return 'no_translation';
    }

    /**
     * Get translation service
     *
     * @return TranslationServiceInterface|null
     */
    public function getTranslationService(): ?TranslationServiceInterface
    {
        return $this->translationService;
    }

    /**
     * Check if translation service is available and configured
     *
     * @return bool
     */
    public function isTranslationServiceAvailable(): bool
    {
        return $this->translationService && $this->translationService->isConfigured();
    }
}
