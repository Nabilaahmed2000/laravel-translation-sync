<?php

namespace Nabila\TranslationSync\Services\Translation;

use Nabila\TranslationSync\Contracts\TranslationServiceInterface;
use Exception;
use GuzzleHttp\Client;


class FreeTranslateApiService implements TranslationServiceInterface
{
    protected $client;
    protected $url;

    public function __construct(array $config = [])
    {
        $this->url = $config['url'] ?? 'http://localhost:5000'; // Default to local
        $this->client = new \GuzzleHttp\Client([
            'base_uri' => $this->url,
            'timeout'  => 10.0,
        ]);
    }

    /**
     * Translate text using free-translate-api
     * @param string $text
     * @param string $targetLanguage
     * @param string|null $sourceLanguage
     * @return string
     * @throws Exception
     */
    public function translate(string $text, string $targetLanguage, ?string $sourceLanguage = null): string
    {
        try {
            $response = $this->client->post('/translate', [
                'json' => [
                    'q' => $text,
                    'source' => $sourceLanguage ?? 'auto',
                    'target' => $targetLanguage,
                ]
            ]);
            $data = json_decode($response->getBody(), true);
            if (isset($data['translatedText'])) {
                return $data['translatedText'];
            }
            throw new Exception('Translation failed: ' . json_encode($data));
        } catch (Exception $e) {
            throw new Exception('FreeTranslateApi error: ' . $e->getMessage());
        }
    }

    /**
     * Check if the service is properly configured
     * @return bool
     */
    public function isConfigured(): bool
    {
        return !empty($this->url);
    }

    /**
     * Get supported languages
     * @return array
     */
    public function getSupportedLanguages(): array
    {
        // The API supports many languages, but you may want to fetch from /languages endpoint
        // For now, return a common set
        return [
            'en',
            'es',
            'fr',
            'de',
            'it',
            'pt',
            'ru',
            'zh',
            'ja',
            'ko',
            'ar',
            'tr',
            'pl',
            'nl',
            'sv',
            'cs',
            'ro',
            'hu',
            'el',
            'da',
            'fi',
            'he',
            'id',
            'ms',
            'th',
            'vi',
            'uk',
            'hi',
            'bg',
            'hr',
            'lt',
            'sk',
            'sl',
            'et',
            'lv',
            'fa',
            'sr',
            'no',
            'ca',
            'eu',
            'gl',
            'mt',
            'is',
            'sq',
            'bs',
            'mk',
            'af',
            'sw',
            'zu',
            'xh',
            'yo',
            'ig',
            'ha',
            'am',
            'so',
            'rw',
            'ny',
            'sn',
            'st',
            'tn',
            'ts',
            've',
            'ss',
            'kg',
            'ln',
            'lu',
            'mg',
            'rn',
            'rw',
            'sg',
            'sh',
            'si',
            'ta',
            'te',
            'ur',
            'uz',
            'vi',
            'cy',
            'ga',
            'gd',
            'kw',
            'gv',
            'mi',
            'mo',
            'qu',
            'rm',
            'sa',
            'sd',
            'sm',
            'su',
            'tl',
            'to',
            'tt',
            'tw',
            'ug',
            'yi',
            'yo',
            'za',
            'zu'
        ];
    }
}
