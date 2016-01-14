<?php

namespace Municipio\Theme;

class Navigation
{
    public function __construct()
    {
        $this->registerMenus();
    }

    public function registerMenus()
    {
        register_nav_menus(array(
            'main-menu' => __('Main menu', 'municipio')
        ));
    }
}
