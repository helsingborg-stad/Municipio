<?php

namespace Municipio\Helper;

class CacheBust
{
    /**
     * Returns the revved/cache-busted file name of an asset.
     */
    static function name($name) {
        static $revManifest;
        if (!isset($revManifest)) {
            $revManifestPath = get_template_directory(). '/assets/dist/rev-manifest.json';
            if (file_exists($revManifestPath)) {
                $revManifest = json_decode(file_get_contents($revManifestPath), true);
            }
            elseif(WP_DEBUG) {
                echo '<div style="color:red">Error: Assets not built. Go to ' . get_template_directory() . ' and run gulp. See '. get_template_directory() . '/README.md for more info.</div>';
            }
        }
        return $revManifest[$name];
    }
}
