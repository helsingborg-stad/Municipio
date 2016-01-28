<?php

namespace Municipio\Helper;

class Acf
{
    public function __construct()
    {
        add_filter('acf/settings/load_json', array($this, 'jsonLoadPath'));

        if (!file_exists(WP_CONTENT_DIR . '/mu-plugins/AcfImportCleaner.php')) {
            require_once MUNICIPIO_PATH . 'library/Helper/AcfImportCleaner.php';
        }
    }

    /**
     * Add search paths for Acf export files
     * @param  array $paths Original paths
     * @return array        Paths to search
     */
    public function jsonLoadPath($paths)
    {
        $paths[] = get_stylesheet_directory() . '/acf-exports';
        $paths[] = get_template_directory() . '/acf-exports';

        return $paths;
    }
}
