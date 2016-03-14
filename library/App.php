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
        new \Municipio\Theme\General();
        new \Municipio\Theme\OnTheFlyImages();

        /**
         * Content
         */
        new \Municipio\Content\CustomPostType();
        new \Municipio\Content\CustomTaxonomy();

        /**
         * Widget
         */
        new \Municipio\Widget\RichEditor();
        new \Municipio\Widget\Contact();

        add_action('widgets_init', function () {
            register_widget('\Municipio\Widget\Contact');
        });

        /**
         * Admin
         */
        new \Municipio\Admin\Options\Theme();
        new \Municipio\Admin\Options\Timestamp();
        new \Municipio\Admin\Options\Favicon();
        new \Municipio\Admin\Options\GoogleTranslate();

        new \Municipio\Admin\Roles\General();
        new \Municipio\Admin\Roles\Editor();

        add_filter('Modularity/CoreTemplatesSearchPaths', function ($paths) {
            $paths[] = get_stylesheet_directory() . '/views';
            $paths[] = get_template_directory() . '/views';
            return $paths;
        });
    }
}
