<?php

namespace Municipio\SchemaData;

use AcfService\AcfService;
use Municipio\AcfFieldContentModifiers\AcfFieldContentModifierRegistrarInterface;
use Municipio\AcfFieldContentModifiers\Modifiers\ModifyFieldChoices;
use Municipio\Config\Features\SchemaData\SchemaDataConfigInterface;
use Municipio\HooksRegistrar\HooksRegistrarInterface;
use Municipio\PostObject\Factory\CreatePostObjectFromWpPost;
use Municipio\PostObject\Factory\PostObjectFromWpPostFactoryInterface;
use Municipio\SchemaData\SchemaObjectFromPost\SchemaObjectFromPostFactory;
use Municipio\SchemaData\SchemaObjectFromPost\SchemaObjectFromPostInterface;
use Municipio\SchemaData\SchemaPropertiesForm\DisableStandardFieldsOnPostsWithSchemaType\DisableStandardFieldsOnPostsWithSchemaType;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\FieldValue\RegisterFieldValue;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\FormFactory\FormFactory;
use Municipio\SchemaData\SchemaPropertiesForm\SetPostTitleFromSchemaTitle\SetPostTitleFromSchemaTitle;
use Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\FieldMapper\FieldMapper;
use Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\NonceValidation\UpdatePostNonceValidatorService;
use Municipio\SchemaData\SchemaPropertyValueSanitizer\SchemaPropertyValueSanitizer;
use Municipio\SchemaData\Taxonomy\TaxonomiesFromSchemaType\TaxonomiesFromSchemaType;
use Municipio\SchemaData\Taxonomy\TaxonomiesFromSchemaType\TaxonomyFactory;
use Municipio\SchemaData\Taxonomy\TermFactory;
use Municipio\SchemaData\Utils\SchemaToPostTypesResolver\SchemaToPostTypeResolver;
use Municipio\SchemaData\Utils\SchemaTypesInUse;
use wpdb;
use WpCronService\WpCronJob\WpCronJob;
use WpCronService\WpCronJobManager;
use WpService\WpService;

/**
 * Enables the Schema Data feature in WordPress.
 */
class SchemaDataFeature
{
    /**
     * Constructor.
     *
     * @param WpService $wpService The WordPress service instance.
     * @param AcfService $acfService The ACF service instance.
     * @param HooksRegistrarInterface $hooksRegistrar The hooks registrar.
     * @param AcfFieldContentModifierRegistrarInterface $acfFieldContentModifierRegistrar The ACF field content modifier registrar.
     * @param SchemaDataConfigInterface $schemaDataConfig The schema data config.
     * @param wpdb $wpdb The WordPress database instance.
     * @param callable|null $externalContentSetupCallback Optional callback for external content setup.
     */
    public function __construct(
        private WpService $wpService,
        private AcfService $acfService,
        private HooksRegistrarInterface $hooksRegistrar,
        private AcfFieldContentModifierRegistrarInterface $acfFieldContentModifierRegistrar,
        private SchemaDataConfigInterface $schemaDataConfig,
        private wpdb $wpdb,
        private $externalContentSetupCallback = null
    ) {
    }

    /**
     * Enable the Schema Data feature.
     *
     * This method sets up the necessary hooks and filters to enable the Schema Data functionality.
     */
    public function enable(): void
    {
        $this->setupAcfExport();
        $this->setupOptionsPage();
        $this->setupSchemaTypeModifiers();
        $this->setupSchemaOutput();
        $this->setupSchemaPropertiesForm();
        $this->setupStandardFieldsDisabling();
        $this->setupPostTitleFromSchema();
        $this->setupFormFieldStorage();
        $this->setupTaxonomies();
        $this->setupCronJobs();
        $this->setupExternalContent();
    }

    /**
     * Setup ACF export functionality.
     */
    private function setupAcfExport(): void
    {
        $this->wpService->addFilter('Municipio/AcfExportManager/autoExport', function (array $autoExportIds) {
            $autoExportIds['post-type-schema-settings'] = 'group_66d94a4867cec';
            return $autoExportIds;
        });
    }

    /**
     * Setup ACF options page.
     */
    private function setupOptionsPage(): void
    {
        $this->wpService->addAction('init', function () {
            $this->acfService->addOptionsSubPage([
                'page_title'  => 'Post type schema settings',
                'menu_title'  => 'Post type schema settings',
                'menu_slug'   => 'mun-post-type-schema-settings',
                'capability'  => 'manage_options',
                'parent_slug' => 'options-general.php',
            ]);
        });
    }

    /**
     * Setup schema type modifiers for ACF fields.
     */
    private function setupSchemaTypeModifiers(): void
    {
        $getAllSchemaTypes = new \Municipio\SchemaData\Utils\SchemaTypes();
        $allSchemaTypes    = array_combine($getAllSchemaTypes->getSchemaTypes(), $getAllSchemaTypes->getSchemaTypes());
        $this->acfFieldContentModifierRegistrar->registerModifier(
            'field_66da9e4dffa66',
            new ModifyFieldChoices($allSchemaTypes)
        );
    }

    /**
     * Setup schema data output in post head.
     */
    private function setupSchemaOutput(): void
    {
        $this->hooksRegistrar->register(new \Municipio\SchemaData\Utils\OutputPostSchemaJsonInSingleHead(
            $this->getSchemaObjectFromPostFactory(),
            $this->wpService
        ));
    }

    /**
     * Setup schema properties form registration.
     */
    private function setupSchemaPropertiesForm(): void
    {
        (new \Municipio\SchemaData\SchemaPropertiesForm\Register(
            $this->acfService,
            $this->wpService,
            $this->schemaDataConfig,
            new FormFactory(new RegisterFieldValue($this->wpService), $this->wpService),
            $this->getPostObjectFromWpPostFactory(),
        ))->addHooks();
    }

    /**
     * Setup disabling of standard fields for certain schema types.
     */
    private function setupStandardFieldsDisabling(): void
    {
        (new DisableStandardFieldsOnPostsWithSchemaType(
            ['ExhibitionEvent'],
            ['title', 'editor'],
            $this->schemaDataConfig,
            $this->wpService
        ))->addHooks();
    }

    /**
     * Setup post title from schema title functionality.
     */
    private function setupPostTitleFromSchema(): void
    {
        (new SetPostTitleFromSchemaTitle($this->getSchemaObjectFromPostFactory(), $this->wpService))->addHooks();
    }

    /**
     * Setup form field storage functionality.
     */
    private function setupFormFieldStorage(): void
    {
        (new \Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\StoreFormFieldValues(
            $this->wpService,
            $this->schemaDataConfig,
            new UpdatePostNonceValidatorService($this->wpService),
            new FieldMapper($this->acfService),
            (new \Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\SchemaPropertiesFromMappedFields\SchemaPropertiesFromMappedFieldsFactory())->create(),
            $this->getPostObjectFromWpPostFactory()
        ))->addHooks();
    }

    /**
     * Setup taxonomies for schema types.
     */
    private function setupTaxonomies(): void
    {
        $taxonomiesFactory = new \Municipio\SchemaData\Taxonomy\TaxonomiesFromSchemaType\TaxonomiesFactory(
            new TaxonomiesFromSchemaType(new TaxonomyFactory(), new SchemaToPostTypeResolver($this->acfService, $this->wpService)),
            new SchemaTypesInUse($this->wpdb)
        );
        
        (new \Municipio\SchemaData\Taxonomy\RegisterTaxonomies($taxonomiesFactory, $this->wpService))->addHooks();
        (new \Municipio\SchemaData\Taxonomy\AddTermsToPostFromSchema($taxonomiesFactory, new TermFactory(), $this->wpService))->addHooks();
    }

    /**
     * Setup cron jobs for cleanup tasks.
     */
    private function setupCronJobs(): void
    {
        $taxonomiesFactory = new \Municipio\SchemaData\Taxonomy\TaxonomiesFromSchemaType\TaxonomiesFactory(
            new TaxonomiesFromSchemaType(new TaxonomyFactory(), new SchemaToPostTypeResolver($this->acfService, $this->wpService)),
            new SchemaTypesInUse($this->wpdb)
        );
        
        $cleanupUnusedTerms = new \Municipio\SchemaData\Taxonomy\CleanupUnusedTerms($taxonomiesFactory, $this->wpService);
        (new WpCronJobManager('municipio_schemadata_', $this->wpService))->register(
            new WpCronJob('cleanup_unused_terms', time(), 'hourly', [$cleanupUnusedTerms, 'cleanupUnusedTerms'], [])
        );
    }

    /**
     * Setup external content feature.
     * This will be replaced when ExternalContent is moved into SchemaData.
     */
    private function setupExternalContent(): void
    {
        if ($this->externalContentSetupCallback !== null) {
            call_user_func($this->externalContentSetupCallback);
        }
    }

    /**
     * Get the schema object from post factory.
     *
     * @return SchemaObjectFromPostInterface
     */
    private function getSchemaObjectFromPostFactory(): SchemaObjectFromPostInterface
    {
        $getSchemaPropertiesWithParamTypes = new \Municipio\SchemaData\Utils\GetSchemaPropertiesWithParamTypes();

        return (new SchemaObjectFromPostFactory(
            $this->schemaDataConfig,
            $this->wpService,
            $getSchemaPropertiesWithParamTypes,
            new SchemaPropertyValueSanitizer()
        ))->create();
    }

    /**
     * Get the post object from WP post factory.
     *
     * @return PostObjectFromWpPostFactoryInterface
     */
    private function getPostObjectFromWpPostFactory(): PostObjectFromWpPostFactoryInterface
    {
        return new CreatePostObjectFromWpPost(
            $this->wpService,
            $this->acfService,
            $this->getSchemaObjectFromPostFactory()
        );
    }
}