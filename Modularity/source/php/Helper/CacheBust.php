<?php

namespace Modularity\Helper;

class CacheBust
{
    /**
     * Returns the revved/cache-busted file name of an asset.
     *
     * @param string $name Asset name (array key) from rev-mainfest.json
     * @return string filename of the asset (including directory above)
     */
    public static function name($name)
    {
        $jsonPath = MODULARITY_PATH . apply_filters(
            'Modularity/Helper/CacheBust/RevManifestPath',
            '/dist/manifest.json'
        );

        $revManifest = [];

        if (file_exists($jsonPath)) {
            $revManifest = json_decode(file_get_contents($jsonPath), true);
        } else {
            echo '<div style="color:red">Error: Assets not built!
               Go to ' . MODULARITY_PATH . ' and run `npm run build`. See '
               . MODULARITY_PATH . '/README.md for more info.</div>';
        }

        return $revManifest[$name] ?? $name;
    }
}
