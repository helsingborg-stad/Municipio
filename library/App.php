<?php

namespace Municipio;

class App
{
    public function __construct()
    {
        /**
         * Helpers
         */
        new \Municipio\Helper\Acf();

        /**
         * Template
         */
        new \Municipio\Template();

        /**
         * Theme
         */
        new \Municipio\Theme\Enqueue();
        new \Municipio\Theme\Support();
        new \Municipio\Theme\Sidebars();
        new \Municipio\Theme\Navigation();

        /**
         * Admin
         */
        new \Municipio\Admin\Options\Theme();
    }
}
