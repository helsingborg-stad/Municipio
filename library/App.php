<?php

namespace Municipio;

use AcfService\AcfService;
use HelsingborgStad\BladeService\BladeService;
use Municipio\AcfFieldContentModifiers\AcfFieldContentModifierRegistrarInterface;
use Municipio\AcfFieldContentModifiers\Modifiers\ModifyFieldChoices;
use Municipio\Api\RestApiEndpointsRegistry;
use Municipio\Config\Features\ExternalContent\ExternalContentPostTypeSettings\ExternalContentPostTypeSettingsFactory;
use Municipio\Config\Features\ExternalContent\SourceConfig\SourceConfigFactory;
use Municipio\Config\Features\SchemaData\SchemaDataConfigInterface;
use Municipio\Content\ResourceFromApi\Api\ResourceFromApiRestController;
use Municipio\Content\ResourceFromApi\Modifiers\HooksAdder;
use Municipio\Content\ResourceFromApi\Modifiers\ModifiersHelper;
use Municipio\Content\ResourceFromApi\PostTypeFromResource;
use Municipio\Content\ResourceFromApi\ResourceType;
use Municipio\Content\ResourceFromApi\TaxonomyFromResource;
use Municipio\ExternalContent\Config\ExternalContentConfigArray;
use Municipio\ExternalContent\Config\SourceConfigFactory as ConfigSourceConfigFactory;
use Municipio\ExternalContent\ModifyPostTypeArgs\DisableEditingOfPostTypeUsingExternalContentSource;
use Municipio\Helper\ResourceFromApiHelper;
use Municipio\HooksRegistrar\HooksRegistrarInterface;
use Municipio\Helper\Listing;
use Municipio\SchemaData\SchemaObjectFromPost\SchemaObjectFromPostInterface;
use WP_Post;
use WpCronService\WpCronJobManager;
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
        private WpService $wpService,
        private AcfService $acfService,
        private HooksRegistrarInterface $hooksRegistrar,
        private AcfFieldContentModifierRegistrarInterface $acfFieldContentModifierRegistrar
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

            $decorator = new \Municipio\PostDecorators\ApplyOpenStreetMapData();
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

        return;
        // if ($configService->isEnabled() === false) {
        //     return;
        // }

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
        $this->wpService->addFilter('Municipio/AcfExportManager/autoExport', function (array $autoExportIds) {
            $autoExportIds['post-type-schema-settings'] = 'group_66d94a4867cec';
            return $autoExportIds;
        });

        $this->wpService->addAction('init', function () {
            $this->acfService->addOptionsSubPage([
                'page_title'  => 'Post type schema settings',
                'menu_title'  => 'Post type schema settings',
                'menu_slug'   => 'mun-post-type-schema-settings',
                'capability'  => 'manage_options',
                'parent_slug' => 'options-general.php',
            ]);
        });

        $schemaDataConfig = new \Municipio\Config\Features\SchemaData\SchemaDataConfigService($this->wpService);

        if (!$schemaDataConfig->featureIsEnabled()) {
            return;
        }

        $getEnabledSchemaTypes             = new \Municipio\SchemaData\Utils\GetEnabledSchemaTypes();
        $schemaPropertyValueSanitizer      = new \Municipio\SchemaData\SchemaPropertyValueSanitizer\NullSanitizer();
        $schemaPropertyValueSanitizer      = new \Municipio\SchemaData\SchemaPropertyValueSanitizer\StringSanitizer($schemaPropertyValueSanitizer);
        $schemaPropertyValueSanitizer      = new \Municipio\SchemaData\SchemaPropertyValueSanitizer\BooleanSanitizer($schemaPropertyValueSanitizer);
        $schemaPropertyValueSanitizer      = new \Municipio\SchemaData\SchemaPropertyValueSanitizer\DateTimeSanitizer($schemaPropertyValueSanitizer);
        $schemaPropertyValueSanitizer      = new \Municipio\SchemaData\SchemaPropertyValueSanitizer\GeoCoordinatesFromAcfGoogleMapsFieldSanitizer($schemaPropertyValueSanitizer);
        $getSchemaPropertiesWithParamTypes = new \Municipio\SchemaData\Utils\GetSchemaPropertiesWithParamTypes();

        $schemaObjectFromPost = new \Municipio\SchemaData\SchemaObjectFromPost\SchemaObjectFromPost($schemaDataConfig);
        $schemaObjectFromPost = new \Municipio\SchemaData\SchemaObjectFromPost\SchemaObjectWithNameFromTitle($schemaObjectFromPost);
        $schemaObjectFromPost = new \Municipio\SchemaData\SchemaObjectFromPost\SchemaObjectWithImageFromFeaturedImage($schemaObjectFromPost, $this->wpService);
        $schemaObjectFromPost = new \Municipio\SchemaData\SchemaObjectFromPost\SchemaObjectWithPropertiesFromMetadata($getSchemaPropertiesWithParamTypes, $this->wpService, $schemaPropertyValueSanitizer, $schemaObjectFromPost);
        $schemaObjectFromPost = new \Municipio\SchemaData\SchemaObjectFromPost\SchemaObjectWithPropertiesFromExternalContent($this->wpService, $getEnabledSchemaTypes, $schemaObjectFromPost);

        $this->wpService->addFilter('Municipio/Helper/Post/postObject', function (WP_Post $post) use ($schemaObjectFromPost) {
            return (new \Municipio\PostDecorators\ApplySchemaObject($schemaObjectFromPost))->apply($post);
        }, 10, 1);

        /**
         * Limit schema types and properties.
         */
        $enabledSchemaTypes       = new \Municipio\SchemaData\Utils\GetEnabledSchemaTypes();
        $schemaTypesAndProperties = $enabledSchemaTypes->getEnabledSchemaTypesAndProperties();
        $this->hooksRegistrar->register(new \Municipio\SchemaData\LimitSchemaTypesAndProperties($enabledSchemaTypes->getEnabledSchemaTypesAndProperties(), $this->wpService));

        /**
         * Register schema types in acf select.
         */
        $schemaTypes = array_keys($schemaTypesAndProperties);
        $schemaTypes = array_combine($schemaTypes, $schemaTypes);
        $this->acfFieldContentModifierRegistrar->registerModifier('field_66da9e4dffa66', new ModifyFieldChoices($schemaTypes));

        /**
         * Shared dependencies.
         */
        $getSchemaPropertiesWithParamTypes = new \Municipio\SchemaData\Utils\GetSchemaPropertiesWithParamTypes();

        /**
         * Output schemadata in head of single posts.
         */
        $this->hooksRegistrar->register(new \Municipio\SchemaData\Utils\OutputPostSchemaJsonInSingleHead($schemaObjectFromPost, $this->wpService));

        /**
         * Register form for schema properties on posts.
         */
        $formFieldFactory                  = new \Municipio\SchemaData\SchemaPropertiesForm\FormFieldFromSchemaProperty\FieldWithIdentifiers();
        $formFieldFactory                  = new \Municipio\SchemaData\SchemaPropertiesForm\FormFieldFromSchemaProperty\StringField($formFieldFactory);
        $formFieldFactory                  = new \Municipio\SchemaData\SchemaPropertiesForm\FormFieldFromSchemaProperty\GeoCoordinatesField($formFieldFactory);
        $acfFormFieldsFromSchemaProperties = new \Municipio\SchemaData\SchemaPropertiesForm\GetFormFieldsBySchemaProperties($this->wpService, $formFieldFactory);
        $acfFieldGroupFromSchemaType       = new \Municipio\SchemaData\SchemaPropertiesForm\GetAcfFieldGroupBySchemaType($this->wpService, $getSchemaPropertiesWithParamTypes, $acfFormFieldsFromSchemaProperties);
        $this->hooksRegistrar->register(new \Municipio\SchemaData\SchemaPropertiesForm\Register($this->acfService, $this->wpService, $acfFieldGroupFromSchemaType, $schemaDataConfig));

        /**
         * External content
         */
        $this->setupExternalContent($schemaDataConfig);
    }

    private function setupExternalContent(SchemaDataConfigInterface $schemaDataConfig): void
    {
        /**
         * Feature config
         */
        $sourceConfigs = (new ConfigSourceConfigFactory($schemaDataConfig, $this->wpService))->create();

        /**
         * Register field group for external content settings.
         */
        $this->wpService->addFilter('Municipio/AcfExportManager/autoExport', function (array $autoExportIds) {
            $autoExportIds['external-content-settings'] = 'group_66d94ae935cfb';
            return $autoExportIds;
        });

        /**
         * Register options page
         */
        $this->wpService->addAction('init', function () {
            $this->acfService->addOptionsSubPage([
                'page_title'  => 'External Content Settings',
                'menu_title'  => 'External Content',
                'menu_slug'   => 'mun-external-content-settings',
                'capability'  => 'manage_options',
                'parent_slug' => 'options-general.php',
            ]);
        });

        /**
         * Populate post type field options.
         */
        $postTypesAsOptions = array_combine($schemaDataConfig->getEnabledPostTypes(), $schemaDataConfig->getEnabledPostTypes());
        $this->acfFieldContentModifierRegistrar->registerModifier('field_66da926c03553', new ModifyFieldChoices($postTypesAsOptions));

        /**
         * Populate cron_schedule field options.
         */
        $scheduleOptions = array_map(fn($schedule) => $schedule['display'], $this->wpService->getSchedules());
        array_unshift($scheduleOptions, __('Never', 'municipio'));
        $this->acfFieldContentModifierRegistrar->registerModifier('field_66da9961f781e', new ModifyFieldChoices($scheduleOptions));

        /**
         * Populate taxonomy schema property field options.
         */
        $enabledSchemaTypes                     = new \Municipio\SchemaData\Utils\GetEnabledSchemaTypes();
        $schemaTypesAndProperties               = $enabledSchemaTypes->getEnabledSchemaTypesAndProperties();
        $schemaTypesAndPropertiesAsFieldChoices = array_map(fn($props) => array_combine($props, $props), $schemaTypesAndProperties);
        $this->acfFieldContentModifierRegistrar->registerModifier('field_66da99ea96265', new ModifyFieldChoices($schemaTypesAndPropertiesAsFieldChoices));

        /**
         * Add sync button to post list.
         */
        $this->wpService->addAction('manage_posts_extra_tablenav', function ($which) {
            $this->wpService->submitButton(
                __('Sync all posts from remote source'),
                'primary',
                \Municipio\ExternalContent\Sync\Triggers\TriggerSyncFromGetParams::GET_PARAM_TRIGGER,
                false
            );
        });

        /**
         * Add sync button to post row.
         */
        $this->wpService->addAction('page_row_actions', function (array $actions, WP_Post $post) {
            $urlParams = sprintf(
                '&%s&%s=%s',
                \Municipio\ExternalContent\Sync\Triggers\TriggerSyncFromGetParams::GET_PARAM_TRIGGER,
                \Municipio\ExternalContent\Sync\Triggers\TriggerSyncFromGetParams::GET_PARAM_POST_ID,
                $post->ID
            );

            $actions[\Municipio\ExternalContent\Sync\Triggers\TriggerSyncFromGetParams::GET_PARAM_TRIGGER] = sprintf(
                '<a href="%s">%s</a>',
                $_SERVER['REQUEST_URI'] . $urlParams,
                __('Sync this post from remote source', 'municipio')
            );

            return $actions;
        }, 100, 2);

        /**
         * Trigger sync of external content.
         */
        $this->hooksRegistrar->register(new \Municipio\ExternalContent\Sync\Triggers\TriggerSyncFromGetParams($this->wpService));

        /**
         * Setup cron jobs on config change.
         */
        $this->hooksRegistrar->register(
            new \Municipio\ExternalContent\Cron\SetupCronJobsOnConfigChange(
                $sourceConfigs,
                new WpCronJobManager('municipio_external_content_sync_', $this->wpService),
                $this->wpService
            )
        );

        /**
         * Build sources to sync from.
         */
        $sources = (new \Municipio\ExternalContent\Sources\SourceFactory($sourceConfigs, $this->wpService))->createSources();

        /**
         * Register taxonomies for external content.
         */
        $taxonomyItemsRegistrar = new \Municipio\ExternalContent\Taxonomy\TaxonomyItemsFactory($sourceConfigs, $this->wpService);
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
        $this->hooksRegistrar->register(new DisableEditingOfPostTypeUsingExternalContentSource($sourceConfigs, $this->wpService));
    }
}
