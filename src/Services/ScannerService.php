<?php

namespace Nabila\TranslationSync\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Collection;

class ScannerService
{
    protected array $config;

    public function __construct()
    {
        $this->config = config('translation-sync', []);
    }

    /**
     * Find missing translations across all configured paths
     *
     * @return array
     */
    public function findMissingTranslations(): array
    {
        $keys = [];
        $paths = $this->getScanPaths();

        foreach ($paths as $path) {
            if (!is_dir($path)) {
                continue;
            }

            $files = $this->getTargetFiles($path);

            foreach ($files as $file) {
                $content = file_get_contents($file);
                $foundKeys = $this->extractTranslationKeys($content);

                foreach ($foundKeys as $key) {
                    if (!$this->translationExists($key)) {
                        $keys[$key] = [
                            'key' => $key,
                            'files' => array_merge($keys[$key]['files'] ?? [], [$file]),
                            'contexts' => array_merge($keys[$key]['contexts'] ?? [], [$this->getKeyContext($content, $key)]),
                        ];
                    }
                }
            }
        }

        return $keys;
    }

    /**
     * Get scan paths from configuration
     *
     * @return array
     */
    protected function getScanPaths(): array
    {
        $basePaths = $this->config['scan_paths'] ?? ['app', 'resources', 'routes'];
        $paths = [];

        foreach ($basePaths as $path) {
            $fullPath = base_path($path);
            if (is_dir($fullPath)) {
                $paths[] = $fullPath;
            }
        }

        return $paths;
    }

    /**
     * Get target files for scanning
     *
     * @param string $path
     * @return Collection
     */
    protected function getTargetFiles(string $path): Collection
    {
        $extensions = $this->config['file_extensions'] ?? ['php', 'blade.php'];

        return collect(File::allFiles($path))
            ->filter(function ($file) use ($extensions) {
                return in_array($file->getExtension(), $extensions) ||
                    str_ends_with($file->getFilename(), '.blade.php');
            })
            ->map(fn($file) => $file->getPathname());
    }

    /**
     * Extract translation keys from content
     *
     * @param string $content
     * @return array
     */
    protected function extractTranslationKeys(string $content): array
    {
        $patterns = $this->config['patterns'] ?? [
            "/__\(['\"](.+?)['\"]\)/",
            "/@lang\(['\"](.+?)['\"]\)/",
            "/trans\(['\"](.+?)['\"]\)/",
            "/Lang::get\(['\"](.+?)['\"]\)/"
        ];

        $keys = [];

        foreach ($patterns as $pattern) {
            preg_match_all($pattern, $content, $matches);
            if (!empty($matches[1])) {
                $keys = array_merge($keys, $matches[1]);
            }
        }

        // Remove duplicates and filter out keys with variables
        return array_unique(array_filter($keys, function ($key) {
            // Skip keys that contain variables or complex expressions
            return !preg_match('/[\$\{\}]/', $key) && strlen($key) > 0;
        }));
    }

    /**
     * Get context around a translation key
     *
     * @param string $content
     * @param string $key
     * @return string
     */
    protected function getKeyContext(string $content, string $key): string
    {
        $lines = explode("\n", $content);
        $keyLine = '';

        foreach ($lines as $lineNumber => $line) {
            if (strpos($line, $key) !== false) {
                $start = max(0, $lineNumber - 1);
                $end = min(count($lines) - 1, $lineNumber + 1);

                $context = [];
                for ($i = $start; $i <= $end; $i++) {
                    $marker = $i === $lineNumber ? '>>> ' : '    ';
                    $context[] = $marker . trim($lines[$i]);
                }

                return implode("\n", $context);
            }
        }

        return '';
    }

    /**
     * Check if translation exists
     *
     * @param string $key
     * @return bool
     */
    protected function translationExists(string $key): bool
    {
        // Check if translation exists in current locale
        $translated = __($key);

        // If the translated value is the same as the key, it means no translation exists
        return $translated !== $key;
    }

    /**
     * Get statistics about translation coverage
     *
     * @return array
     */
    public function getStatistics(): array
    {
        $allKeys = [];
        $paths = $this->getScanPaths();

        foreach ($paths as $path) {
            if (!is_dir($path)) {
                continue;
            }

            $files = $this->getTargetFiles($path);

            foreach ($files as $file) {
                $content = file_get_contents($file);
                $foundKeys = $this->extractTranslationKeys($content);
                $allKeys = array_merge($allKeys, $foundKeys);
            }
        }

        $allKeys = array_unique($allKeys);
        $missing = $this->findMissingTranslations();

        return [
            'total_keys' => count($allKeys),
            'missing_keys' => count($missing),
            'translated_keys' => count($allKeys) - count($missing),
            'coverage_percentage' => count($allKeys) > 0 ? round((count($allKeys) - count($missing)) / count($allKeys) * 100, 2) : 100,
        ];
    }
}
