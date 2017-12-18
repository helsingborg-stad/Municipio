<?php

namespace Municipio\Helper;

class CacheBust
{
    /**
     * Returns the revved/cache-busted file name of an asset.
     * @param string $name Asset name (array key) from rev-mainfest.json
     * @param boolean $childTheme Set child or parent theme path (defaults to parent)
     * @param boolean $returnName Returns $name if set to true while in dev mode
     * @return string filename of the asset (including directory above)
     */
    public static function name($name, $childTheme = false, $returnName = false)
    {
        if ($returnName == true && defined('DEV_MODE') && DEV_MODE == true) {
            return $name;
        }

        static $revManifestParent;
        static $revManifestChild;

        $themePath = ($childTheme == true) ? get_stylesheet_directory() : get_template_directory();

        if ($childTheme == true && !isset($revManifestChild[$name])) {
            $revManifestChild = self::getRevManifest($childTheme);
        } elseif ($childTheme == false && !isset($revManifestParent[$name])) {
            $revManifestParent = self::getRevManifest($childTheme);
        }

        $revManifest = ($childTheme == true) ? $revManifestChild : $revManifestParent;

        if (!isset($revManifest[$name])) {
            return;
        }

        return $revManifest[$name];
    }

    /**
     * Decode assets json to array
     * @param boolean $childTheme Set child or parent theme path (defaults to parent)
     * @return array containg assets filenames
     */
    public static function getRevManifest($childTheme = false)
    {
        $themePath = ($childTheme == true) ? get_stylesheet_directory() : get_template_directory();
        $jsonPath = $themePath . apply_filters('Municipio/Helper/CacheBust/RevManifestPath', '/assets/dist/rev-manifest.json');

        if (file_exists($jsonPath)) {
            return json_decode(file_get_contents($jsonPath), true);
        } elseif (WP_DEBUG) {
            echo '<div style="color:red">Error: Assets not built. Go to ' . $themePath . ' and run gulp. See '. $themePath . '/README.md for more info.</div>';
        }
    }
}
