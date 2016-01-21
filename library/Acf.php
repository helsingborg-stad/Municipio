<?php

namespace Municipio;

class Acf
{
    public function __construct()
    {
        add_filter('acf/settings/path', array($this, 'settingsPath'));
        add_filter('acf/settings/dir', array($this, 'settingsDir'));
        add_filter('acf/settings/show_admin', '__return_false');
    }

    public function settingsPath($dir)
    {
        return get_stylesheet_directory() . '/vendor/acf/';
    }

    public function settingsDir($dir)
    {
        return get_stylesheet_directory() . '/vendor/acf/';
    }
}
