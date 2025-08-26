<?php

namespace Municipio\Helper;

class CacheBust
{
    private static $manifestPath = '/assets/dist/manifest.json';

    /**
     * Return the manifest array from manifest.json file.
     * Caches the manifest in a static variable and in WP object cache.
     */
    public static function getManifest(): ?array
    {
        static $revManifest;
        if (!isset($revManifest)) {
            $revManifest = wp_cache_get('municipio-rev-manifest', false);
            if ($revManifest === false) {
                $revManifestPath = get_stylesheet_directory() . self::$manifestPath;

                if (file_exists($revManifestPath)) {
                    $revManifest = json_decode(file_get_contents($revManifestPath), true);
                    wp_cache_set('municipio-rev-manifest', $revManifest);
                } elseif (WP_DEBUG) {
                    echo sprintf(
                        'Error: Assets not built. Go to %s and run "npm run build". See %s/README.md for more info.',
                        get_stylesheet_directory(),
                        get_stylesheet_directory()
                    );
                }
            }
        }

        return $revManifest ?: null;
    }

    /**
     * Returns the revved/cache-busted file name of an asset.
     * @param string $name Asset name (array key) from rev-mainfest.json
     */
    public static function name(string $name): string
    {
        $manifest = self::getManifest();
        if(isset($manifest[$name])) {
            return $manifest[$name];
        }
        return $name;
    }
}
