<?php

namespace Nabila\TranslationSync\Services\Translation;

use Stichoza\GoogleTranslate\GoogleTranslate;
use Exception;

class GoogleTranslationService extends BaseTranslationService
{
    protected ?GoogleTranslate $translator = null;

    public function translate(string $text, string $targetLanguage, ?string $sourceLanguage = null): string
    {
        if (!$this->isConfigured()) {
            throw new Exception('Google Translate service is not properly configured');
        }

        try {
            $translator = $this->getTranslator();

            $sourceLanguage = $sourceLanguage ?: config('translation-sync.source_language', 'en');

            $translator->setSource($this->normalizeLanguageCode($sourceLanguage));
            $translator->setTarget($this->normalizeLanguageCode($targetLanguage));

            $cleanText = $this->cleanText($text);
            $translatedText = $translator->translate($cleanText);

            return $this->restorePlaceholders($translatedText, $text);
        } catch (Exception $e) {
            throw new Exception("Google Translate error: " . $e->getMessage());
        }
    }

    public function isConfigured(): bool
    {
        return !empty($this->config['api_key']) || !empty(config('translation-sync.services.google.api_key'));
    }

    public function getSupportedLanguages(): array
    {
        return [
            'af',
            'sq',
            'am',
            'ar',
            'hy',
            'az',
            'eu',
            'be',
            'bn',
            'bs',
            'bg',
            'ca',
            'ceb',
            'zh',
            'co',
            'hr',
            'cs',
            'da',
            'nl',
            'en',
            'eo',
            'et',
            'fi',
            'fr',
            'fy',
            'gl',
            'ka',
            'de',
            'el',
            'gu',
            'ht',
            'ha',
            'haw',
            'he',
            'hi',
            'hmn',
            'hu',
            'is',
            'ig',
            'id',
            'ga',
            'it',
            'ja',
            'jw',
            'kn',
            'kk',
            'km',
            'rw',
            'ko',
            'ku',
            'ky',
            'lo',
            'la',
            'lv',
            'lt',
            'lb',
            'mk',
            'mg',
            'ms',
            'ml',
            'mt',
            'mi',
            'mr',
            'mn',
            'my',
            'ne',
            'no',
            'ny',
            'or',
            'ps',
            'fa',
            'pl',
            'pt',
            'pa',
            'ro',
            'ru',
            'sm',
            'gd',
            'sr',
            'st',
            'sn',
            'sd',
            'si',
            'sk',
            'sl',
            'so',
            'es',
            'su',
            'sw',
            'sv',
            'tl',
            'tg',
            'ta',
            'tt',
            'te',
            'th',
            'tr',
            'tk',
            'uk',
            'ur',
            'ug',
            'uz',
            'vi',
            'cy',
            'xh',
            'yi',
            'yo',
            'zu'
        ];
    }

    protected function getTranslator(): GoogleTranslate
    {
        if ($this->translator === null) {
            $apiKey = $this->config['api_key'] ?? config('translation-sync.services.google.api_key');

            if ($apiKey) {
                $this->translator = new GoogleTranslate();
                $this->translator->setApiKey($apiKey);
            } else {
                // Use free version (has limitations)
                $this->translator = new GoogleTranslate();
            }
        }

        return $this->translator;
    }
}
