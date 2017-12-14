<?php

namespace Municipio;

class App
{
    public function __construct()
    {
        /**
         * Helpers
         */
        new \Municipio\Helper\GravityForm();

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
        new \Municipio\Theme\ImageSizeFilter();
        new \Municipio\Theme\CustomCodeInput();
        new \Municipio\Theme\Blog();
        new \Municipio\Theme\FileUploads();
        new \Municipio\Theme\Archive();
        new \Municipio\Theme\EventArchive();
        new \Municipio\Theme\CustomTemplates();
        new \Municipio\Theme\Font();
        new \Municipio\Theme\ColorScheme();
        new \Municipio\Theme\FixedActionBar();

        new \Municipio\Search\General();

        /**
         * Content
         */
        new \Municipio\Content\CustomPostType();
        new \Municipio\Content\CustomTaxonomy();
        new \Municipio\Content\PostFilters();
        new \Municipio\Content\ShortCode();
        new \Municipio\Content\Cache();

        /**
         * Widget
         */
        new \Municipio\Widget\RichEditor();
        new \Municipio\Widget\Contact();

        /**
         * Comments
         */
        new \Municipio\Comment\HoneyPot();
        new \Municipio\Comment\LikeButton();
        new \Municipio\Comment\CommentsFilters();

        add_action('widgets_init', function () {
            register_widget('\Municipio\Widget\Contact');
        });

        /**
         * Admin
         */
        new \Municipio\Admin\General();

        new \Municipio\Admin\Options\Theme();
        new \Municipio\Admin\Options\Timestamp();
        new \Municipio\Admin\Options\Favicon();
        new \Municipio\Admin\Options\GoogleTranslate();
        new \Municipio\Admin\Options\Archives();
        new \Municipio\Admin\Options\ContentEditor();

        new \Municipio\Admin\Roles\General();
        new \Municipio\Admin\Roles\Editor();

        new \Municipio\Admin\UI\VarnishPurge();
        new \Municipio\Admin\UI\BackEnd();
        new \Municipio\Admin\UI\FrontEnd();
        new \Municipio\Admin\UI\Editor();

        new \Municipio\Admin\TinyMce\LoadPlugins();

        /**
         * Api
         */
        new \Municipio\Api\Navigation();

        add_filter('Modularity/CoreTemplatesSearchPaths', function ($paths) {
            $paths[] = get_stylesheet_directory() . '/views';
            $paths[] = get_template_directory() . '/views';
            return $paths;
        });
    }
}
