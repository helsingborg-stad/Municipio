<?php

namespace Municipio;

use AcfService\AcfService;
use HelsingborgStad\BladeService\BladeService;
use Municipio\Admin\Acf\ContentType\FieldOptions as ContentTypeSchemaFieldOptions;
use Municipio\Api\RestApiEndpointsRegistry;
use Municipio\Content\ResourceFromApi\Api\ResourceFromApiRestController;
use Municipio\Content\ResourceFromApi\Modifiers\HooksAdder;
use Municipio\Content\ResourceFromApi\Modifiers\ModifiersHelper;
use Municipio\Content\ResourceFromApi\PostTypeFromResource;
use Municipio\Content\ResourceFromApi\ResourceType;
use Municipio\Content\ResourceFromApi\TaxonomyFromResource;
use Municipio\Helper\ResourceFromApiHelper;
use PHPMailer\PHPMailer\PHPMailer;
use WpService\WpService;

/**
 * Class App
 * @package Municipio
 */
class App
{
    /**
     * App constructor.
     */
    public function __construct(private WpService $wpService, private AcfService $acfService)
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
        new \Municipio\Theme\ThemeMods();


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

        $resourceRegistry = new \Municipio\Content\ResourceFromApi\ResourceRegistry\ResourceRegistry();

        add_action('init', function () use ($resourceRegistry) {

            $resourceRegistry->registerResources();

            $postTypeResources       = $resourceRegistry->getByType(ResourceType::POST_TYPE);
            $sortedPostTypeResources = $resourceRegistry->sortByParentPostType($postTypeResources);

            foreach ($sortedPostTypeResources as $resource) {
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

        new \Municipio\Admin\Acf\PrefillIconChoice();
        new \Municipio\Admin\Acf\LocationRules();
        new \Municipio\Admin\Acf\ImageAltTextValidation();

        // Register Content Type Schema fields
        $prepareContentTypeSchemaMetaFields = new \Municipio\Admin\Acf\ContentType\PrepareField(
            ContentTypeSchemaFieldOptions::FIELD_KEY,
            ContentTypeSchemaFieldOptions::GROUP_NAME
        );

        $prepareContentTypeSchemaMetaFields->addHooks();
        $saveContentTypeSchemaMetaFields = new \Municipio\Admin\Acf\ContentType\SavePost();
        $saveContentTypeSchemaMetaFields->addHooks();

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

        /**
         * Branded emails
         */
        $this->trySetupBrandedEmails();
    }

    /**
     * Branded emails setup
     *
     * Enables branded html emails if enabled from theme options page.
     * Uses theme appearance to apply branding to all outgoing emails.
     */
    private function trySetupBrandedEmails(): void
    {

        $configService = new \Municipio\BrandedEmails\Config\BrandedEmailsConfigService($this->acfService);

        if ($configService->isEnabled() === false) {
            return;
        }

        $setMailContentType = new \Municipio\BrandedEmails\SetMailContentType('text/html', $this->wpService);
        $setMailContentType->addHooks();

        $setMailFrom = new \Municipio\BrandedEmails\SetMailFrom($configService, $this->wpService);
        $setMailFrom->addHooks();

        $setMailFromName = new \Municipio\BrandedEmails\SetMailFromName($configService, $this->wpService);
        $setMailFromName->addHooks();

        $bladeService          = new BladeService([__DIR__ . '/BrandedEmails/HtmlTemplate/views']);
        $htmlTemplateConfig    = new \Municipio\BrandedEmails\HtmlTemplate\Config\HtmlTemplateConfigService($this->wpService);
        $emailHtmlTemplate     = new \Municipio\BrandedEmails\HtmlTemplate\DefaultHtmlTemplate($htmlTemplateConfig, $this->wpService, $bladeService);
        $applyMailHtmlTemplate = new \Municipio\BrandedEmails\ApplyMailHtmlTemplate($emailHtmlTemplate, $this->wpService);
        $applyMailHtmlTemplate->addHooks();
    }
}
