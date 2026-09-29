<?php

namespace Municipio\Helper;

class ManifestCacheBust implements CacheBustInterface
{
    private static array $manifests = [];

    public function __construct(
        private string $manifestPath = '/assets/dist/manifest.json',
        private ?string $directory = null,
    ) {}

    public function getManifest(): ?array
    {
        $directory = $this->directory ?? get_stylesheet_directory();
        $fullPath = $directory . $this->manifestPath;
        if (!isset(self::$manifests[$fullPath])) {
            // Keep the original object-cache key for existing consumers.
            $cacheKey = $this->manifestPath === '/assets/dist/manifest.json' && $directory === get_stylesheet_directory()
                ? 'municipio-rev-manifest'
                : 'municipio-rev-manifest-' . md5($fullPath);
            $manifest = wp_cache_get($cacheKey, false);

            if ($manifest === false && is_file($fullPath)) {
                $manifest = json_decode((string) file_get_contents($fullPath), true);
                if (is_array($manifest)) {
                    wp_cache_set($cacheKey, $manifest);
                }
            } elseif ($manifest === false && defined('WP_DEBUG') && WP_DEBUG) {
                echo sprintf(
                    'Error: Assets not built. Go to %s and run "npm run build". See %s/README.md for more info.',
                    $directory,
                    $directory,
                );
            }

            self::$manifests[$fullPath] = is_array($manifest) ? $manifest : null;
        }

        return self::$manifests[$fullPath] ?: null;
    }

    public function name(string $name): string
    {
        $manifest = $this->getManifest();
        return isset($manifest[$name]) ? $manifest[$name] : $name;
    }
}
