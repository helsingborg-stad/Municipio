<?php

namespace Municipio;

use AcfService\AcfService;
use HelsingborgStad\BladeService\BladeService;
use Municipio\AcfFieldContentModifiers\AcfFieldContentModifierRegistrarInterface;
use Municipio\Api\RestApiEndpointsRegistry;
use Municipio\Config\ConfigInterface;
use Municipio\Content\ResourceFromApi\Api\ResourceFromApiRestController;
use Municipio\Content\ResourceFromApi\Modifiers\HooksAdder;
use Municipio\Content\ResourceFromApi\Modifiers\ModifiersHelper;
use Municipio\Content\ResourceFromApi\PostTypeFromResource;
use Municipio\Content\ResourceFromApi\ResourceType;
use Municipio\Content\ResourceFromApi\TaxonomyFromResource;
use Municipio\ExternalContent\AcfFieldContentModifiers\PopulateTaxonomySchemaPropertyFieldOptions;
use Municipio\ExternalContent\ModifyPostTypeArgs\DisableEditingOfPostTypeUsingExternalContentSource;
use Municipio\ExternalContent\Sources\StaticSourceRegistry;
use Municipio\Helper\ResourceFromApiHelper;
use Municipio\HooksRegistrar\HooksRegistrarInterface;
use Municipio\Helper\Listing;
use Municipio\SchemaData\AcfFieldContentModifiers\PopulateSchemaTypeFieldOptions;
use Municipio\SchemaData\SchemaObjectFromPost\SchemaObjectFromPostInterface;
use WP_Post;
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
    public function __construct(
        private ConfigInterface $config,
        private WpService $wpService,
        private AcfService $acfService,
        private HooksRegistrarInterface $hooksRegistrar,
        private AcfFieldContentModifierRegistrarInterface $acfFieldContentModifierRegistrar,
        private SchemaObjectFromPostInterface $schemaObjectFromPost
    ) {
        /**
         * Upgrade
         */
        new \Municipio\Upgrade($this->wpService, $this->acfService);

        /**
         * Template
         */
        new \Municipio\Template($this->acfService);

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
        new \Municipio\Theme\Navigation(new \Municipio\SchemaData\Utils\GetEnabledSchemaTypes());
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
         * Post decorators
         */
        $this->wpService->addFilter('Municipio/Helper/Post/postObject', function (WP_Post $post) {

            $decorator = new \Municipio\PostDecorators\ApplySchemaObject($this->schemaObjectFromPost);
            $decorator = new \Municipio\PostDecorators\ApplyOpenStreetMapData($decorator);
            $decorator = new \Municipio\PostDecorators\ApplyBookingLinkToPlace($this->acfService, $decorator);
            $decorator = new \Municipio\PostDecorators\ApplyInfoListToPlace($this->acfService, new Listing(), $decorator);

            return $decorator->apply($post);
        }, 10, 1);

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

        new \Municipio\Admin\Options\Theme();
        new \Municipio\Admin\Options\Timestamp();
        new \Municipio\Admin\Options\GoogleTranslate();
        new \Municipio\Admin\Options\ContentEditor();
        new \Municipio\Admin\Options\AttachmentConsent();

        new \Municipio\Admin\Acf\PrefillIconChoice();
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

        /**
         * Imported post type design
         */
        $this->setupPostTypeDesign();

        /**
         * Branded emails
         */
        $this->trySetupBrandedEmails();

        /**
         * Apply schema.org data to posts
         */
        $this->setupSchemaDataFeature();
    }

    /**
     * Sets up the post type design.
     *
     * This method initializes the post type design by creating instances of the
     * SaveDesigns and SetDesigns classes and passing the option name and the
     * WordPress service instance.
     *
     * @return void
     */
    private function setupPostTypeDesign(): void
    {
        $optionName  = 'post_type_design';
        $apiUrl      = 'https://customizer.municipio.tech/id/';
        $saveDesigns =  new \Municipio\PostTypeDesign\SaveDesigns(
            $optionName,
            $this->wpService,
            new \Municipio\PostTypeDesign\ConfigFromPageId($this->wpService, $apiUrl)
        );

        $setDesigns = new \Municipio\PostTypeDesign\SetDesigns($optionName, $this->wpService);

        $this->hooksRegistrar->register($saveDesigns);
        $this->hooksRegistrar->register($setDesigns);
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

        $setMailContentType    = new \Municipio\BrandedEmails\SetMailContentType('text/html', $this->wpService);
        $convertMessageToHtml  = new \Municipio\BrandedEmails\ConvertMessageToHtml($this->wpService);
        $bladeService          = new BladeService([__DIR__ . '/BrandedEmails/HtmlTemplate/views']);
        $htmlTemplateConfig    = new \Municipio\BrandedEmails\HtmlTemplate\Config\HtmlTemplateConfigService($this->wpService);
        $emailHtmlTemplate     = new \Municipio\BrandedEmails\HtmlTemplate\DefaultHtmlTemplate($htmlTemplateConfig, $this->wpService, $bladeService);
        $applyMailHtmlTemplate = new \Municipio\BrandedEmails\ApplyMailHtmlTemplate($emailHtmlTemplate, $this->wpService);

        $this->hooksRegistrar->register($setMailContentType);
        $this->hooksRegistrar->register($convertMessageToHtml);
        $this->hooksRegistrar->register($applyMailHtmlTemplate);
    }

    private function setupSchemaDataFeature(): void
    {
        if (!$this->config->getSchemaDataConfig()->featureIsEnabled()) {
            return;
        }

        /**
         * Register options page
         */
        $this->hooksRegistrar->register(new \Municipio\SchemaData\OptionsPage($this->wpService, $this->acfService));

        /**
         * Shared dependencies.
         */
        $getSchemaPropertiesWithParamTypes = new \Municipio\SchemaData\Utils\GetSchemaPropertiesWithParamTypes();

        /**
         * Limit schema types and properties.
         */
        $enabledSchemaTypes = new \Municipio\SchemaData\Utils\GetEnabledSchemaTypes();
        $this->hooksRegistrar->register(new \Municipio\SchemaData\LimitSchemaTypesAndProperties($enabledSchemaTypes->getEnabledSchemaTypesAndProperties(), $this->wpService));

        /**
         * Output schemadata in head of single posts.
         */
        $this->hooksRegistrar->register(new \Municipio\SchemaData\Utils\OutputPostSchemaJsonInSingleHead($this->schemaObjectFromPost, $this->wpService));

        /**
         * Register form for schema properties on posts.
         */
        $formFieldFactory                  = new \Municipio\SchemaData\SchemaPropertiesForm\FormFieldFromSchemaProperty\FieldWithIdentifiers();
        $formFieldFactory                  = new \Municipio\SchemaData\SchemaPropertiesForm\FormFieldFromSchemaProperty\StringField($formFieldFactory);
        $formFieldFactory                  = new \Municipio\SchemaData\SchemaPropertiesForm\FormFieldFromSchemaProperty\GeoCoordinatesField($formFieldFactory);
        $acfFormFieldsFromSchemaProperties = new \Municipio\SchemaData\SchemaPropertiesForm\GetFormFieldsBySchemaProperties($this->wpService, $formFieldFactory);
        $acfFieldGroupFromSchemaType       = new \Municipio\SchemaData\SchemaPropertiesForm\GetAcfFieldGroupBySchemaType($this->wpService, $getSchemaPropertiesWithParamTypes, $acfFormFieldsFromSchemaProperties);
        $this->hooksRegistrar->register(new \Municipio\SchemaData\SchemaPropertiesForm\Register($this->acfService, $this->wpService, $acfFieldGroupFromSchemaType, $this->config->getSchemaDataConfig()));

        /**
         * External content
         */
        $this->setupExternalContent();
    }

    private function setupExternalContent(): void
    {
        if (!$this->config->getSchemaDataConfig()->featureIsEnabled()) {
            return;
        }

        /**
         * Populate acf select with schema properties.
         */
        $this->acfFieldContentModifierRegistrar->registerModifier(
            new PopulateTaxonomySchemaPropertyFieldOptions(
                'field_66c6ca5972ba2',
                new \Municipio\SchemaData\Utils\GetEnabledSchemaTypes()
            )
        );

        $this->wpService->addAction('manage_posts_extra_tablenav', function ($which) {
            $this->wpService->submitButton(
                __('Sync all posts from remote source'),
                'primary',
                \Municipio\ExternalContent\Sync\Triggers\TriggerSyncFromGetParams::GET_PARAM_TRIGGER,
                false
            );
        });

        $this->wpService->addAction('post_row_actions', function (array $actions, WP_Post $post) {
            $urlParams = sprintf(
                '&%s&%s=%s',
                \Municipio\ExternalContent\Sync\Triggers\TriggerSyncFromGetParams::GET_PARAM_TRIGGER,
                \Municipio\ExternalContent\Sync\Triggers\TriggerSyncFromGetParams::GET_PARAM_POST_ID,
                $post->ID
            );

            $actions[\Municipio\ExternalContent\Sync\Triggers\TriggerSyncFromGetParams::GET_PARAM_TRIGGER] = sprintf(
                '<a href="%s">%s</a>',
                $urlParams,
                __('Sync this post from remote source', 'municipio')
            );

            return $actions;
        });

        /**
         * Trigger sync of external content.
         */
        $this->hooksRegistrar->register(new \Municipio\ExternalContent\Sync\Triggers\TriggerSyncFromGetParams($this->wpService));

        /**
         * Build sources
         */
        $sources = (new \Municipio\ExternalContent\Sources\SourceFactory($this->config->getExternalContentConfig()))->createSources();

        /**
         * Register taxonomies.
         */
        $taxonomyItemsRegistrar = new \Municipio\ExternalContent\Taxonomy\TaxonomyItemsFactory($this->config->getExternalContentConfig(), $this->wpService);
        $taxonomyItems          = $taxonomyItemsRegistrar->createTaxonomyItems();
        array_map(fn($item) => $item->register(), $taxonomyItems);

        /**
         * Start sync if event is triggered.
         */
        $syncEventListener = new \Municipio\ExternalContent\Sync\SyncEventListener($sources, $taxonomyItems, $this->wpService);
        $this->hooksRegistrar->register($syncEventListener);

        /**
         * Disable editing of post type using external content source.
         */
        $this->hooksRegistrar->register(
            new DisableEditingOfPostTypeUsingExternalContentSource(
                $this->config->getExternalContentConfig(),
                $this->wpService
            )
        );
    }
}
