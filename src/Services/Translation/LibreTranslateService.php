<?php

namespace Nabila\TranslationSync\Services\Translation;

use Exception;

class LibreTranslateService extends BaseTranslationService
{
    protected string $baseUrl = 'https://libretranslate.com';

    public function translate(string $text, string $targetLanguage, ?string $sourceLanguage = null): string
    {
        try {
            $sourceLanguage = $sourceLanguage ?: config('translation-sync.source_language', 'en');

            $response = $this->makeRequest($text, $sourceLanguage, $targetLanguage);

            if (isset($response['translatedText'])) {
                $translatedText = $this->cleanText($response['translatedText']);
                return $this->restorePlaceholders($translatedText, $text);
            }

            throw new Exception('Invalid response from LibreTranslate API');
        } catch (Exception $e) {
            throw new Exception("LibreTranslate error: " . $e->getMessage());
        }
    }

    public function isConfigured(): bool
    {
        // LibreTranslate is free and doesn't require API keys
        return true;
    }

    public function getSupportedLanguages(): array
    {
        // LibreTranslate supports many languages
        return [
            'en',
            'es',
            'fr',
            'de',
            'it',
            'pt',
            'ru',
            'ja',
            'ko',
            'zh',
            'ar',
            'hi',
            'bn',
            'pa',
            'jv',
            'ms',
            'id',
            'te',
            'ta',
            'mr',
            'tr',
            'ur',
            'gu',
            'kn',
            'or',
            'ml',
            'as',
            'mai',
            'bho',
            'awa',
            'bh',
            'mag',
            'doi',
            'mwr',
            'hne',
            'bg',
            'cs',
            'da',
            'nl',
            'fi',
            'el',
            'hu',
            'lt',
            'lv',
            'no',
            'pl',
            'ro',
            'sk',
            'sl',
            'sv',
            'uk',
            'hr',
            'sr',
            'mk',
            'et',
            'lv',
            'sq',
            'bs',
            'is',
            'ga',
            'cy',
            'br',
            'gd',
            'yi',
            'he',
            'ar',
            'fa',
            'ur',
            'ps',
            'ku',
            'sd',
            'ne',
            'pi',
            'sa',
            'hi',
            'mr',
            'gu',
            'pa',
            'bn',
            'or',
            'as',
            'mni',
            'doi'
        ];
    }

    protected function makeRequest(string $text, string $source, string $target): array
    {
        $client = new \GuzzleHttp\Client([
            'timeout' => 30,
            'headers' => [
                'Content-Type' => 'application/json',
                'User-Agent' => 'Laravel-Translation-Sync/2.0'
            ]
        ]);

        $response = $client->post("{$this->baseUrl}/translate", [
            'json' => [
                'q' => $text,
                'source' => $this->normalizeLanguageCode($source),
                'target' => $this->normalizeLanguageCode($target),
                'format' => 'text'
            ]
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON response from LibreTranslate');
        }

        return $data;
    }

    /**
     * Set custom LibreTranslate instance URL
     */
    public function setBaseUrl(string $url): self
    {
        $this->baseUrl = rtrim($url, '/');
        return $this;
    }

    /**
     * Get available LibreTranslate instances (for load balancing)
     */
    public static function getAvailableInstances(): array
    {
        return [
            'https://libretranslate.com',
            'https://translate.astian.org',
            'https://translate.mentality.rip',
            'https://translate.api.skitzen.com',
        ];
    }
}
