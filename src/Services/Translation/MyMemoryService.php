<?php

namespace Nabila\TranslationSync\Services\Translation;

use Exception;

class MyMemoryService extends BaseTranslationService
{
    protected string $baseUrl = 'https://api.mymemory.translated.net';

    public function translate(string $text, string $targetLanguage, ?string $sourceLanguage = null): string
    {
        try {
            $sourceLanguage = $sourceLanguage ?: config('translation-sync.source_language', 'en');

            $response = $this->makeRequest($text, $sourceLanguage, $targetLanguage);

            if (isset($response['responseData']['translatedText'])) {
                $translatedText = $this->cleanText($response['responseData']['translatedText']);
                return $this->restorePlaceholders($translatedText, $text);
            }

            throw new Exception('Invalid response from MyMemory API');
        } catch (Exception $e) {
            throw new Exception("MyMemory error: " . $e->getMessage());
        }
    }

    public function isConfigured(): bool
    {
        // MyMemory is free and doesn't require API keys
        return true;
    }

    public function getSupportedLanguages(): array
    {
        // MyMemory supports many languages
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
            'sq',
            'bs',
            'is',
            'ga',
            'cy',
            'he',
            'fa',
            'ps',
            'ku',
            'sd',
            'ne',
            'af',
            'xh',
            'zu',
            'st',
            'tn',
            'ts',
            'ss',
            've',
            'nr',
            'sw',
            'rw',
            'yo',
            'ig',
            'ha',
            'am',
            'ti',
            'om',
            'so',
            'aa',
            'kr',
            'mg',
            'ceb',
            'ilo',
            'war',
            'tl',
            'su',
            'jv',
            'mad',
            'min',
            'tet',
            'tpi',
            'ht',
            'bi',
            'sm',
            'gil',
            'mh',
            'na'
        ];
    }

    protected function makeRequest(string $text, string $source, string $target): array
    {
        $client = new \GuzzleHttp\Client([
            'timeout' => 30,
            'headers' => [
                'User-Agent' => 'Laravel-Translation-Sync/2.0'
            ]
        ]);

        $params = [
            'q' => $text,
            'langpair' => $this->normalizeLanguageCode($source) . '|' . $this->normalizeLanguageCode($target),
            'de' => 'your-email@example.com' // Optional: helps with rate limiting
        ];

        $response = $client->get("{$this->baseUrl}/get", [
            'query' => $params
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON response from MyMemory');
        }

        // Check for API errors
        if (isset($data['responseStatus']) && $data['responseStatus'] !== 200) {
            throw new Exception('MyMemory API error: ' . ($data['responseDetails'] ?? 'Unknown error'));
        }

        return $data;
    }

    /**
     * Set custom email for MyMemory API (helps with rate limiting)
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }
}
