<?php

namespace Municipio\Theme;

class BladerunnerSettings
{
    public function __construct()
    {
        $this->createCacheFolder();

        add_filter('bladerunner/cache_path', array($this, 'cachePath'));
        add_filter('bladerunner/cache', array($this, 'cachePath'));
    }

    public function cachePath()
    {
        return WP_CONTENT_DIR . '/uploads/cache';
    }

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
