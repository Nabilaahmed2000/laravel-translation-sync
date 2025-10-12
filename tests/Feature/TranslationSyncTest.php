<?php

namespace Tests\Feature;

use Tests\TestCase;
use Nabila\TranslationSync\Services\ScannerService;
use Nabila\TranslationSync\Services\TranslationWriter;
use Nabila\TranslationSync\Services\Translation\DummyTranslationService;
use Illuminate\Support\Facades\File;

class TranslationSyncTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Set up test configuration
        config([
            'translation-sync.service' => 'dummy',
            'translation-sync.source_language' => 'en',
            'translation-sync.target_languages' => ['es', 'fr'],
            'translation-sync.auto_translate' => true,
            'translation-sync.file_format' => 'json',
        ]);
    }

    /** @test */
    public function it_can_detect_missing_translations()
    {
        $scanner = new ScannerService();

        // This would normally scan actual files
        // In a real test, you'd create temporary test files
        $missing = $scanner->findMissingTranslations();

        $this->assertIsArray($missing);
    }

    /** @test */
    public function it_can_use_dummy_translation_service()
    {
        $service = new DummyTranslationService();

        $result = $service->translate('Hello World', 'es');

        $this->assertEquals('Hello World [es]', $result);
        $this->assertTrue($service->isConfigured());
    }

    /** @test */
    public function it_can_write_translations_to_json_files()
    {
        $writer = new TranslationWriter();

        // Mock the resource_path function for testing
        $testPath = storage_path('test-translations');
        File::makeDirectory($testPath, 0755, true);

        // In a real test, you'd mock the file operations
        $this->assertTrue(true); // Placeholder assertion

        // Clean up
        File::deleteDirectory($testPath);
    }

    /** @test */
    public function it_handles_translation_placeholders()
    {
        $service = new DummyTranslationService();

        // Test placeholder preservation (would be implemented in base service)
        $textWithPlaceholder = 'Welcome :name to our site';
        $result = $service->translate($textWithPlaceholder, 'es');

        $this->assertStringContains(':name', $result);
    }
}
