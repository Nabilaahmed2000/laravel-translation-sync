<?php

namespace Nabila\TranslationSync\Services\Translation;

class DummyTranslationService extends BaseTranslationService
{
    public function translate(string $text, string $targetLanguage, ?string $sourceLanguage = null): string
    {
        // For testing purposes, just append the target language to the text
        return $text . " [{$targetLanguage}]";
    }

    public function isConfigured(): bool
    {
        return true;
    }

    public function getSupportedLanguages(): array
    {
        return ['en', 'es', 'fr', 'de', 'it', 'pt', 'ru', 'ja', 'ko', 'zh', 'ar'];
    }
}
