<?php

declare(strict_types=1);

namespace Municipio\Styleguide\ComponentAssets;

/** Discovers component and utility styles from one validated build manifest. */
class StylesheetManifest
{
    private ?array $styles = null;

    public function __construct(
        private string $directory,
    ) {}

    /** @return array<string, string> Source paths mapped to hashed build paths. */
    public function getStyles(): array
    {
        if ($this->styles !== null) {
            return $this->styles;
        }
        $manifestPath = $this->directory . '/manifest.json';
        $contents = is_file($manifestPath) ? file_get_contents($manifestPath) : false;
        $manifest = $contents === false ? null : json_decode($contents, true);
        return $this->styles = array_filter(is_array($manifest) ? $manifest : [], [$this, 'isStylesheet'], ARRAY_FILTER_USE_BOTH);
    }

    private function isStylesheet(mixed $builtPath, mixed $sourcePath): bool
    {
        if (!is_string($sourcePath) || !is_string($builtPath) || !str_ends_with($builtPath, '.css')) {
            return false;
        }
        if (!str_starts_with($sourcePath, 'css/components/') && !str_starts_with($sourcePath, 'css/utilities/')) {
            return false;
        }
        $directory = realpath($this->directory);
        $resolvedPath = realpath($this->directory . '/' . $builtPath);
        return $directory !== false && $resolvedPath !== false && str_starts_with($resolvedPath, $directory . '/') && is_file($resolvedPath);
    }
}
