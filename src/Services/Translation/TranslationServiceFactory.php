<?php

namespace Nabila\TranslationSync\Services\Translation;

use Nabila\TranslationSync\Contracts\TranslationServiceInterface;
use InvalidArgumentException;

class TranslationServiceFactory
{
    /**
     * Create a translation service instance
     *
     * @param string $service
     * @param array $config
     * @return TranslationServiceInterface
     * @throws InvalidArgumentException
     */
    public static function create(string $service, array $config = []): TranslationServiceInterface
    {
        switch ($service) {
            case 'google':
                return new GoogleTranslationService($config);

            case 'libretranslate':
                return new LibreTranslateService($config);

            case 'mymemory':
                return new MyMemoryService($config);

            case 'dummy':
                return new DummyTranslationService($config);

            default:
                throw new InvalidArgumentException("Unsupported translation service: {$service}");
        }
    }

    /**
     * Get available translation services
     *
     * @return array
     */
    public static function getAvailableServices(): array
    {
        return [
            'libretranslate' => 'LibreTranslate (Free, Open Source)',
            'mymemory' => 'MyMemory (Free, Community-driven)',
            'google' => 'Google Translate (Paid API)',
            'dummy' => 'Dummy Service (for testing)',
        ];
    }

    /**
     * Get free translation services
     *
     * @return array
     */
    public static function getFreeServices(): array
    {
        return [
            'libretranslate' => 'LibreTranslate (Free, Open Source)',
            'mymemory' => 'MyMemory (Free, Community-driven)',
        ];
    }
}
