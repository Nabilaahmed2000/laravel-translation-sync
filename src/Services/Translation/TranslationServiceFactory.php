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
            'google' => 'Google Translate',
            'dummy' => 'Dummy Service (for testing)',
        ];
    }
}
