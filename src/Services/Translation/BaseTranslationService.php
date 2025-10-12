<?php

namespace Nabila\TranslationSync\Services\Translation;

use Nabila\TranslationSync\Contracts\TranslationServiceInterface;

abstract class BaseTranslationService implements TranslationServiceInterface
{
    protected array $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Normalize language code to service-specific format
     *
     * @param string $language
     * @return string
     */
    protected function normalizeLanguageCode(string $language): string
    {
        // Convert common Laravel locale codes to service-specific codes
        $mapping = [
            'en' => 'en',
            'es' => 'es',
            'fr' => 'fr',
            'de' => 'de',
            'it' => 'it',
            'pt' => 'pt',
            'ru' => 'ru',
            'ja' => 'ja',
            'ko' => 'ko',
            'zh' => 'zh',
            'ar' => 'ar',
            'hi' => 'hi',
            'tr' => 'tr',
            'pl' => 'pl',
            'nl' => 'nl',
            'sv' => 'sv',
            'da' => 'da',
            'no' => 'no',
            'fi' => 'fi',
        ];

        return $mapping[$language] ?? $language;
    }

    /**
     * Clean text for translation
     *
     * @param string $text
     * @return string
     */
    protected function cleanText(string $text): string
    {
        // Remove Laravel translation placeholders temporarily
        $text = preg_replace('/:\w+/', '[PLACEHOLDER]', $text);

        return $text;
    }

    /**
     * Restore placeholders after translation
     *
     * @param string $translatedText
     * @param string $originalText
     * @return string
     */
    protected function restorePlaceholders(string $translatedText, string $originalText): string
    {
        // Extract placeholders from original text
        preg_match_all('/:\w+/', $originalText, $matches);
        $placeholders = $matches[0];

        // Replace [PLACEHOLDER] back with actual placeholders
        $index = 0;
        return preg_replace_callback('/\[PLACEHOLDER\]/', function () use ($placeholders, &$index) {
            return $placeholders[$index++] ?? '[PLACEHOLDER]';
        }, $translatedText);
    }
}
