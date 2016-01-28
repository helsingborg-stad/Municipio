<?php

/*
Plugin Name: ACF Json Sync file doctor
Description: Checks if ACF json export files (that should be imported with Json Sync) is in correct format. If not, the plugin will try to fix any errors.
Version:     1.0
Author:      Kristoffer Svanmark
*/

namespace AcfImportCleaner;

class AcfImportCleaner
{
    public function __construct()
    {
        add_filter('acf/settings/load_json', array($this, 'acfLoadClean'), 99999999);
    }

    public function acfLoadClean($paths)
    {
        $paths = array_unique($paths);

        foreach ($paths as $path) {
            foreach (@glob($path . '/*.json') as $file) {
                $json = json_decode(file_get_contents($file));

                if (!is_array($json)) {
                    continue;
                }

                $content = file($file);

                if (trim($content[0]) == '[') {
                    array_shift($content);
                    array_pop($content);
                }

                file_put_contents($file, $content);
            }
        }

        return $paths;
    }
}

new \AcfImportCleaner\AcfImportCleaner();
