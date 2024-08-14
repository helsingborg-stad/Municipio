<?php

namespace Municipio;

use AcfService\AcfService;
use HelsingborgStad\BladeService\BladeService;
use Municipio\AdminNotice\AdminNoticeLevels;
use Municipio\Api\RestApiEndpointsRegistry;
use Municipio\Content\ResourceFromApi\Api\ResourceFromApiRestController;
use Municipio\Content\ResourceFromApi\Modifiers\HooksAdder;
use Municipio\Content\ResourceFromApi\Modifiers\ModifiersHelper;
use Municipio\Content\ResourceFromApi\PostTypeFromResource;
use Municipio\Content\ResourceFromApi\ResourceType;
use Municipio\Content\ResourceFromApi\TaxonomyFromResource;
use Municipio\ExternalContent\Config\ISourceConfig;
use Municipio\ExternalContent\PostsResults\AddExternalContentToWpCache;
use Municipio\ExternalContent\PostsResults\PopulateWpQueryWithExternalContent;
use Municipio\ExternalContent\Sources\ISource;
use Municipio\ExternalContent\Sources\SourceRegistry;
use Municipio\ExternalContent\Sources\StaticSourceRegistry;
use Municipio\ExternalContent\Taxonomy\TaxonomyItem;
use Municipio\Helper\ResourceFromApiHelper;
use Municipio\HooksRegistrar\HooksRegistrarInterface;
use Predis\Command\Redis\TYPE;
use WP_Post_Type;
use WP_Query;
use Municipio\Helper\Listing;
use Municipio\IniService\IniService;
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
        private WpService $wpService,
        private AcfService $acfService,
        private HooksRegistrarInterface $hooksRegistrar,
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

        /**
         * External content
         */
        $this->trySetupExternalContent();
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
        /**
         * Feature enabled/disabled
         */
        if (
            $this->acfService->getField('mun_schemadata_enabled', 'options') !== true &&
            $this->acfService->getField('mun_schemadata_enabled', 'options') !== "1" &&
            $this->acfService->getField('mun_schemadata_enabled', 'options') !== 1
        ) {
            return;
        }

        /**
         * Shared dependencies.
         */
        $getSchemaPropertiesWithParamTypes = new \Municipio\SchemaData\Utils\GetSchemaPropertiesWithParamTypes();
        $getSchemaTypeFromPostType         = new \Municipio\SchemaData\Utils\GetSchemaTypeFromPostType($this->acfService);

        /**
         * Limit schema types and properties.
         */
        $enabledSchemaTypes = new \Municipio\SchemaData\Utils\GetEnabledSchemaTypes();
        $this->hooksRegistrar->register(new \Municipio\SchemaData\LimitSchemaTypesAndProperties($enabledSchemaTypes->getEnabledSchemaTypesAndProperties(), $this->wpService));

        /**
         * Register field group for schema.org data that shows up on admin post list pages.
         */
        $schemaTypes = new \Municipio\SchemaData\Acf\Utils\SchemaTypesFromSpatie();
        $this->hooksRegistrar->register(new \Municipio\SchemaData\Acf\RegisterFeatureSettingsFieldGroup($this->acfService, $schemaTypes, $this->wpService));

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
        $this->hooksRegistrar->register(new \Municipio\SchemaData\SchemaPropertiesForm\Register($this->acfService, $this->wpService, $acfFieldGroupFromSchemaType, $getSchemaTypeFromPostType));
    }

    private function trySetupExternalContent(): void
    {
        $sourceRegistry = new StaticSourceRegistry(
            [
                new \Municipio\ExternalContent\Config\Providers\JsonFileSourceConfig('job', 'JobPosting', __DIR__ . '/ExternalContent/Fixtures/JobPosting.json'),
                new \Municipio\ExternalContent\Config\Providers\JsonFileSourceConfig('foo', 'JobPosting', '/var/www/html/wp-content/shemaobjects.json'),
                new \Municipio\ExternalContent\Config\Providers\JsonFileSourceConfig('foo', 'JobPosting', '/var/www/html/wp-content/thingshemaobjects.json'),
                // new \Municipio\ExternalContent\Config\Providers\TypesenseSourceConfig('foo', 'JobPosting', TYPESENSE_API_KEY, TYPESENSE_HOST, 'jobpostings')
            ],
            new \Municipio\ExternalContent\Sources\SourceFactory(),
            $this->wpService
        );

        $taxonomyRegistrar = new \Municipio\ExternalContent\Taxonomy\TaxonomyRegistrar([
            new TaxonomyItem(
                'JobPosting',
                'relevantOccupation',
                'relevant_occupation',
                $this->wpService->__('Relevant occupation', 'municipio'),
                $this->wpService->__('Relevant occupations', 'municipio'),
                $this->wpService
            ),
            new TaxonomyItem(
                'JobPosting',
                'employmentType',
                'employment_type',
                $this->wpService->__('Employment type', 'municipio'),
                $this->wpService->__('Employment types', 'municipio'),
                $this->wpService
            ),
            new TaxonomyItem(
                'JobPosting',
                'hiringOrganization',
                'hiring_organization',
                $this->wpService->__('Hiring organization', 'municipio'),
                $this->wpService->__('Hiring organizations', 'municipio'),
                $this->wpService
            ),
            new TaxonomyItem(
                'JobPosting',
                'applicationContact',
                'application_contact',
                $this->wpService->__('Application contact', 'municipio'),
                $this->wpService->__('Application contacts', 'municipio'),
                $this->wpService
            )
        ], $sourceRegistry, $this->wpService);

        $this->hooksRegistrar->register($taxonomyRegistrar);

        add_action('init', function () {

            $postType = new WP_Post_Type('job', [
                'label'        => 'Jobs',
                'public'       => true,
                'show_in_rest' => true,
                'supports'     => false,
                'has_archive'  => true,
            ]);

            $postType->cap->create_posts = 'do_not_allow';
            $postType->cap->delete_post  = 'do_not_allow';
            $postType->cap->edit_post    = 'do_not_allow';

            register_post_type($postType->name, [
                'label'        => $postType->label,
                'public'       => $postType->public,
                'show_in_rest' => $postType->show_in_rest,
                'supports'     => $postType->supports,
                'has_archive'  => $postType->has_archive,
                'capabilities' => (array)$postType->cap,
            ]);
        });

        // TODO: Create terms from sources.
        $wpTermFactory = new \Municipio\ExternalContent\WpTermFactory\WpTermFactory();
        $wpTermFactory = new \Municipio\ExternalContent\WpTermFactory\WpTermUsingSchemaObjectName($wpTermFactory);

        $wpPostFactory = new \Municipio\ExternalContent\WpPostFactory\WpPostFactory();
        $wpPostFactory = new \Municipio\ExternalContent\WpPostFactory\DateDecorator($wpPostFactory);
        $wpPostFactory = new \Municipio\ExternalContent\WpPostFactory\IdDecorator($wpPostFactory, $this->wpService);
        $wpPostFactory = new \Municipio\ExternalContent\WpPostFactory\JobPostingDecorator($wpPostFactory);
        $wpPostFactory = new \Municipio\ExternalContent\WpPostFactory\SchemaDataDecorator($wpPostFactory);
        $wpPostFactory = new \Municipio\ExternalContent\WpPostFactory\OriginIdDecorator($wpPostFactory);
        $wpPostFactory = new \Municipio\ExternalContent\WpPostFactory\ThumbnailDecorator($wpPostFactory, $this->wpService);
        $wpPostFactory = new \Municipio\ExternalContent\WpPostFactory\SourceIdDecorator($wpPostFactory);
        $wpPostFactory = new \Municipio\ExternalContent\WpPostFactory\VersionDecorator($wpPostFactory);
        $wpPostFactory = new \Municipio\ExternalContent\WpPostFactory\TermsDecorator($taxonomyRegistrar, $wpTermFactory, $this->wpService, $wpPostFactory);

        $syncSourceToLocal = new \Municipio\ExternalContent\Sync\SyncAllFromSourceToLocal($sourceRegistry->getSources()[0], $wpPostFactory, $this->wpService);
        // $syncSourceToLocal->addHooks();

        $syncSingleSourceToLocalByPostId = new \Municipio\ExternalContent\Sync\SyncSingleFromSourceToLocalByPostId(187, $sourceRegistry, $wpPostFactory, $this->wpService);
        // $syncSingleSourceToLocalByPostId->addHooks();
    }
}
