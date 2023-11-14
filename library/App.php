<?php

namespace Municipio;

use Municipio\Api\RestApiEndpointsRegistry;

/**
 * Class App
 * @package Municipio
 */
class App
{
    /**
     * App constructor.
     */
    public function __construct()
    {
        /**
         * Upgrade
         */
        new \Municipio\Upgrade();

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
        new \Municipio\Theme\General();
        new \Municipio\Theme\OnTheFlyImages();
        new \Municipio\Theme\SharpenThumbnails();
        new \Municipio\Theme\ImageSizeFilter();
        new \Municipio\Theme\CustomCodeInput();
        new \Municipio\Theme\Blog();
        new \Municipio\Theme\FileUploads();
        new \Municipio\Theme\Archive();
        new \Municipio\Theme\CustomTemplates();
        new \Municipio\Theme\Navigation();
        new \Municipio\Theme\Icon();
        new \Municipio\Theme\Forms();


        new \Municipio\Search\General();
        new \Municipio\Search\Algolia();

        /**
         * Content
         */
        new \Municipio\Content\CustomPostType();
        new \Municipio\Content\CustomTaxonomy();
        new \Municipio\Content\PostFilters();
        new \Municipio\Content\ShortCode();
        new \Municipio\Content\Images();
        new \Municipio\Content\Cache();
        new \Municipio\Content\IframePosterImage();


        /**
         * Resources from API
         */
        $resourcePostType = new \Municipio\Content\ResourceFromApi\ResourcePostType();
        $resourcePostType->addHooks();

        $resourceRegistry = new \Municipio\Content\ResourceFromApi\ResourceRegistry();
        $resourceRegistry->addHooks();

        $postTypeQueriesModifier = new \Municipio\Content\ResourceFromApi\PostType\PostTypeQueriesModifier($resourceRegistry);
        $postTypeQueriesModifier->addHooks();

        $taxonomyQueriesModifier = new \Municipio\Content\ResourceFromApi\Taxonomy\TaxonomyQueriesModifier($resourceRegistry);
        $taxonomyQueriesModifier->addHooks();

        /**
         * Oembed
         */
        new \Municipio\Oembed\OembedFilters();

        /**
         * Language
         */

        new \Municipio\Language();

        /**
         * Widget
         */
        new \Municipio\Widget\Widgets();

        /**
         * Comments
         */
        new \Municipio\Comment\HoneyPot();
        new \Municipio\Comment\Likes();
        new \Municipio\Comment\Filters();
        new \Municipio\Comment\Form();

        /**
         * Admin
         */
        new \Municipio\Admin\Gutenberg\Gutenberg();
        new \Municipio\Admin\General();
        new \Municipio\Admin\LoginTracking();

        new \Municipio\Admin\Gutenberg\Blocks\BlockManager();

        new \Municipio\Admin\ExternalDeptendents();

        new \Municipio\Admin\Options\Theme();
        new \Municipio\Admin\Options\Timestamp();
        new \Municipio\Admin\Options\Favicon();
        new \Municipio\Admin\Options\GoogleTranslate();
        new \Municipio\Admin\Options\ContentEditor();
        new \Municipio\Admin\Options\AttachmentConsent();

        // TODO Move Content Type settings to the customizer
        new \Municipio\Admin\Options\ContentType();

        new \Municipio\Admin\Acf\PrefillIconChoice();
        new \Municipio\Admin\Acf\LocationRules();

        new \Municipio\Admin\Roles\General();
        new \Municipio\Admin\Roles\Editor();

        new \Municipio\Admin\UI\BackEnd();
        new \Municipio\Admin\UI\FrontEnd();
        new \Municipio\Admin\UI\Editor();

        new \Municipio\Admin\TinyMce\LoadPlugins();

        /**
         * Api
         */
        RestApiEndpointsRegistry::add(new \Municipio\Api\Media\Sideload());
        RestApiEndpointsRegistry::add(new \Municipio\Api\Navigation\Children());
        RestApiEndpointsRegistry::add(new \Municipio\Api\Navigation\ChildrenRender());
        RestApiEndpointsRegistry::add(new \Municipio\Api\View\Render());

        /**
         * Customizer
         */
        new \Municipio\Customizer();

        /**
         * Block customizations
         */
        new \Municipio\Blocks\Columns();

        add_filter('Modularity/CoreTemplatesSearchPaths', function ($paths) {
            $paths[] = get_stylesheet_directory() . '/views/v3';
            $paths[] = get_template_directory() . '/views/v3';
            return $paths;
        });

        // add_action('wp_ajax_nopriv_generate_pdf', array($this, 'generate'));
        // add_action('wp_ajax_generate_pdf', array($this, 'generate')); 
        add_action('rest_api_init', function () {
            register_rest_route('pdf/v2', '/id=(?P<id>\d+(?:,\d+)*)', array(
                'methods' => 'GET ',
                'callback' => array($this, 'idToPdf'),
            ));
        });        
    }

    public function idToPdf($request) {
        $ids = $request->get_param('id');
        if (!empty($ids) && is_string($ids)) {
            $idArr = explode(',', $ids);
            $pdf = new \Municipio\Content\PdfGenerator();
            return $pdf->renderView($idArr);
        }
    }
}
