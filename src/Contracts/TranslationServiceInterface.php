<?php

namespace Nabila\TranslationSync\Contracts;

interface TranslationServiceInterface
{
    /**
     * Translate text from source language to target language
     *
     * @param string $text
     * @param string $targetLanguage
     * @param string|null $sourceLanguage
     * @return string
     * @throws \Exception
     */
    public function translate(string $text, string $targetLanguage, ?string $sourceLanguage = null): string;

    /**
     * Check if the service is properly configured
     *
     * @return bool
     */
    public function isConfigured(): bool;

    /**
     * Get supported languages
     *
     * @return array
     */
    public function getSupportedLanguages(): array;
}
