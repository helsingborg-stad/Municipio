<?php

namespace Municipio\Theme;

class BladerunnerSettings
{
    public function __construct()
    {
        $this->createCacheFolder();

        add_filter('bladerunner/template_types', array($this, 'templateTypes'));
        add_filter('bladerunner/cache_path', array($this, 'cachePath'));
        add_filter('bladerunner/cache', array($this, 'cachePath'));
    }

    /**
     * Filter of template types
     */
    public function templateTypes()
    {
        return array(
            'index'      => 'index.blade.php',
            'home'       => 'index.blade.php',
            'single'     => 'single.blade.php',
            'page'       => 'page.blade.php',
            '404'        => '404.blade.php',
            'archive'    => 'archive.blade.php',
            'author'     => 'author.blade.php',
            'category'   => 'category.blade.php',
            'tag'        => 'tag.blade.php',
            'taxonomy'   => 'taxonomy.blade.php',
            'date'       => 'date.blade.php',
            'front-page' => 'front-page.blade.php',
            'paged'      => 'paged.blade.php',
            'search'     => 'search.blade.php',
            'single'     => 'single.blade.php',
            'singular'   => 'singular.blade.php',
            'attachment' => 'attachment.blade.php',
        );
    }

    /**
     * Get the cache path
     * @return string
     */
    public function cachePath()
    {
        return WP_CONTENT_DIR . '/uploads/blade-cache';
    }

    /**
     * Creates the template cache folder if missing
     * @return void
     */
    public function createCacheFolder()
    {
        if (file_exists($this->cachePath())) {
            return false;
        }

        mkdir($this->cachePath(), 0777, true);
        chmod($this->cachePath(), 0777);

        return true;
    }
}
