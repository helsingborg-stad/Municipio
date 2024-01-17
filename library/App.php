<?php

namespace Municipio;

use Municipio\Api\RestApiEndpointsRegistry;
use Municipio\Content\ResourceFromApi\Api\ResourceFromApiRestController;
use Municipio\Content\ResourceFromApi\Modifiers\HooksAdder;
use Municipio\Content\ResourceFromApi\Modifiers\ModifiersHelper;
use Municipio\Content\ResourceFromApi\PostTypeFromResource;
use Municipio\Content\ResourceFromApi\ResourceType;
use Municipio\Content\ResourceFromApi\TaxonomyFromResource;
use Municipio\Helper\ResourceFromApiHelper;

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

        // Register the actual post type to be used for resources.
        $resourcePostType = new \Municipio\Content\ResourceFromApi\ResourcePostType();
        $resourcePostType->addHooks();

        // Set up registry.

        $resourceRegistry = new \Municipio\Content\ResourceFromApi\ResourceRegistry();

        add_action('init', function () use ($resourceRegistry) {

            $resourceRegistry->registerResources();

            foreach ($resourceRegistry->getByType(ResourceType::POST_TYPE) as $resource) {
                $registrar = new PostTypeFromResource($resource);
                $registrar->register();
            }

            foreach ($resourceRegistry->getByType(ResourceType::TAXONOMY) as $resource) {
                $registrar = new TaxonomyFromResource($resource);
                $registrar->register();
            }
        });

        // Make resources available to the helper class.
        ResourceFromApiHelper::initialize($resourceRegistry);

        // Add hooks for modifiers. Modifiers are used to modify the output of resources through filters and actions.
        $modifiersHelper = new ModifiersHelper($resourceRegistry);
        $hooksAdder      = new HooksAdder($resourceRegistry, $modifiersHelper);
        $hooksAdder->addHooks();

        // Add REST API endpoints for resources.
        add_action('rest_api_init', function () use ($resourceRegistry) {
            $resources = $resourceRegistry->getByType(ResourceType::POST_TYPE);

            if (empty($resources)) {
                return;
            }

            foreach ($resourceRegistry->getByType(ResourceType::POST_TYPE) as $resource) {
                $controller = new ResourceFromApiRestController($resource->getName());
                $controller->register_routes();
            }
        });

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
        new \Municipio\Admin\Acf\ImageAltTextValidation();

        new \Municipio\Admin\Roles\General();
        new \Municipio\Admin\Roles\Editor();

        new \Municipio\Admin\UI\BackEnd();
        new \Municipio\Admin\UI\FrontEnd();
        new \Municipio\Admin\UI\Editor();

        new \Municipio\Admin\TinyMce\LoadPlugins();

        $uploads = new \Municipio\Admin\Uploads();
        $uploads->addHooks();

        /**
         * Api
         */
        RestApiEndpointsRegistry::add(new \Municipio\Api\Media\Sideload());
        RestApiEndpointsRegistry::add(new \Municipio\Api\Navigation\Children());
        RestApiEndpointsRegistry::add(new \Municipio\Api\Navigation\ChildrenRender());
        RestApiEndpointsRegistry::add(new \Municipio\Api\View\Render());

        $pdfHelper    = new \Municipio\Api\Pdf\PdfHelper();
        $pdfGenerator = new \Municipio\Api\Pdf\PdfGenerator($pdfHelper);
        $pdfGenerator->addHooks();


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
    }
}
