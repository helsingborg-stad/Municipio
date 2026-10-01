<?php

namespace Municipio\Helper;

/**
 * Backwards-compatible entry point for the theme's default asset manifest.
 */
class CacheBust
{
    public static function getManifest(
        string $manifestPath = '/assets/dist/manifest.json',
        ?string $directory = null,
    ): ?array
    {
        return (new ManifestCacheBust($manifestPath, $directory))->getManifest();
    }

    public static function name(
        string $name,
        string $manifestPath = '/assets/dist/manifest.json',
        ?string $directory = null,
    ): string
    {
        return (new ManifestCacheBust($manifestPath, $directory))->name($name);
    }
}
