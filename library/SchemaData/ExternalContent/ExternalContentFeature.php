<?php

namespace Municipio\SchemaData\ExternalContent;

use AcfService\AcfService;
use Municipio\AcfFieldContentModifiers\AcfFieldContentModifierRegistrarInterface;
use Municipio\AcfFieldContentModifiers\Modifiers\ModifyFieldChoices;
use Municipio\ProgressReporter\HttpHeader\HttpHeader;
use Municipio\ProgressReporter\OutputBuffer\OutputBuffer;
use Municipio\SchemaData\Config\SchemaDataConfigInterface;
use Municipio\SchemaData\ExternalContent\Config\SourceConfigFactory as ConfigSourceConfigFactory;
use Municipio\SchemaData\ExternalContent\Cron\AllowCronToEditPosts;
use Municipio\SchemaData\ExternalContent\ModifyPostTypeArgs\DisableEditingOfPostTypeUsingExternalContentSource;
use Municipio\SchemaData\ExternalContent\SyncHandler\LocalImageObjectIdGenerator\LocalImageObjectIdGenerator;
use Municipio\SchemaData\ExternalContent\SyncHandler\SchemaObjectProcessor\ImageSideloadSchemaObjectProcessor;
use Municipio\SchemaData\ExternalContent\SyncHandler\SyncHandler;
use Municipio\SchemaData\ExternalContent\UI\HideSyncedMediaFromAdminMediaLibrary;
use Municipio\SchemaData\ExternalContent\WpPostArgsFromSchemaObject\ThumbnailDecorator;
use WpCronService\WpCronJobManager;
use WpService\WpService;

/**
 * Enables the External Content feature in WordPress.
 */
class ExternalContentFeature
{
    /**
     * Constructor.
     *
     * @param WpService $wpService The WordPress service instance.
     * @param AcfService $acfService The ACF service instance.
     * @param AcfFieldContentModifierRegistrarInterface $acfFieldContentModifierRegistrar The ACF field content modifier registrar.
     * @param SchemaDataConfigInterface $schemaDataConfig The schema data config.
     */
    public function __construct(
        private WpService $wpService,
        private AcfService $acfService,
        private AcfFieldContentModifierRegistrarInterface $acfFieldContentModifierRegistrar,
        private SchemaDataConfigInterface $schemaDataConfig
    ) {
    }

    /**
     * Enable the External Content feature.
     *
     * This method sets up the necessary hooks and filters to enable the External Content functionality.
     */
    public function enable(): void
    {
        $this->setupBasicFunctionality();
        $this->setupAdminOnlyFeatures();
    }

    /**
     * Setup basic external content functionality.
     */
    private function setupBasicFunctionality(): void
    {
        $postTypeSyncInProgress = new \Municipio\SchemaData\ExternalContent\SyncHandler\SyncInProgress\PostTypeSyncInProgress($this->wpService);
        $sourceConfigs          = (new ConfigSourceConfigFactory($this->schemaDataConfig, $this->wpService))->create();

        $this->setupCronPermissions();
        $this->setupCronJobs($sourceConfigs);
        $this->setupPostTypeDisabling($sourceConfigs);
        $this->setupSyncHandler($sourceConfigs);
        $this->setupAjaxSync($sourceConfigs, $postTypeSyncInProgress);
    }

    /**
     * Setup admin-only features.
     */
    private function setupAdminOnlyFeatures(): void
    {
        if (!$this->wpService->isAdmin()) {
            return;
        }

        $sourceConfigs          = (new ConfigSourceConfigFactory($this->schemaDataConfig, $this->wpService))->create();
        $postTypeSyncInProgress = new \Municipio\SchemaData\ExternalContent\SyncHandler\SyncInProgress\PostTypeSyncInProgress($this->wpService);

        $this->setupAcfExport();
        $this->setupOptionsPage();
        $this->setupFieldModifiers();
        $this->setupUIButtons($sourceConfigs);
        $this->setupSyncTriggers($sourceConfigs, $postTypeSyncInProgress);
        $this->setupMediaHiding();
    }

    /**
     * Setup cron permissions.
     */
    private function setupCronPermissions(): void
    {
        (new AllowCronToEditPosts($this->wpService))->addHooks();
    }

    /**
     * Setup cron jobs for sync.
     */
    private function setupCronJobs(array $sourceConfigs): void
    {
        (new \Municipio\SchemaData\ExternalContent\Cron\SetupCronJobsOnConfigChange(
            $sourceConfigs,
            new WpCronJobManager('municipio_external_content_sync_', $this->wpService),
            $this->wpService
        ))->addHooks();
    }

    /**
     * Setup post type editing disabling.
     */
    private function setupPostTypeDisabling(array $sourceConfigs): void
    {
        (new DisableEditingOfPostTypeUsingExternalContentSource($sourceConfigs, $this->wpService))->addHooks();
    }

    /**
     * Setup sync handler.
     */
    private function setupSyncHandler(array $sourceConfigs): void
    {
        (new \Municipio\SchemaData\ExternalContent\SyncHandler\SyncHandler(
            $sourceConfigs,
            $this->wpService,
            new \Municipio\ProgressReporter\NullProgressReporterService(),
            $this->getSchemaObjectProcessors()
        ))->addHooks();
    }

    /**
     * Setup AJAX sync functionality.
     */
    private function setupAjaxSync(array $sourceConfigs, $postTypeSyncInProgress): void
    {
        $progressReporter = new \Municipio\ProgressReporter\SseProgressReporterService(new HttpHeader(), new OutputBuffer());

        (new \Municipio\SchemaData\ExternalContent\Rest\AjaxSync(
            $sourceConfigs,
            $postTypeSyncInProgress,
            $progressReporter,
            new SyncHandler($sourceConfigs, $this->wpService, $progressReporter, $this->getSchemaObjectProcessors()),
            $this->wpService
        ))->addHooks();
    }

    /**
     * Get the schema object processors.
     *
     * @return \Municipio\SchemaData\ExternalContent\SyncHandler\SchemaObjectProcessor\SchemaObjectProcessorInterface[]
     */
    private function getSchemaObjectProcessors(): array
    {
        return [
            new \Municipio\SchemaData\ExternalContent\SyncHandler\SchemaObjectProcessor\NoOpSchemaObjectProcessor(),
            new \Municipio\SchemaData\ExternalContent\SyncHandler\SchemaObjectProcessor\ImageSideloadSchemaObjectProcessor($this->wpService, $GLOBALS['wpdb']),
        ];
    }

    /**
     * Setup ACF export functionality.
     */
    private function setupAcfExport(): void
    {
        $this->wpService->addFilter('Municipio/AcfExportManager/autoExport', function (array $autoExportIds) {
            $autoExportIds['external-content-settings'] = 'group_66d94ae935cfb';
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
                'page_title'  => $this->wpService->__('External Content Settings', 'municipio'),
                'menu_title'  => $this->wpService->__('External Content', 'municipio'),
                'menu_slug'   => 'mun-external-content-settings',
                'capability'  => 'manage_options',
                'parent_slug' => 'options-general.php',
            ]);
        });
    }

    /**
     * Setup field modifiers for ACF fields.
     */
    private function setupFieldModifiers(): void
    {
        // Populate post type field options
        $postTypesAsOptions = array_combine(
            $this->schemaDataConfig->getEnabledPostTypes(),
            $this->schemaDataConfig->getEnabledPostTypes()
        );
        $this->acfFieldContentModifierRegistrar->registerModifier(
            'field_66da926c03553',
            new ModifyFieldChoices($postTypesAsOptions)
        );

        // Populate cron_schedule field options
        $scheduleOptions = array_map(fn($schedule) => $schedule['display'], $this->wpService->wpGetSchedules());
        array_unshift($scheduleOptions, __('Never', 'municipio'));
        $this->acfFieldContentModifierRegistrar->registerModifier(
            'field_66da9961f781e',
            new ModifyFieldChoices($scheduleOptions)
        );
    }

    /**
     * Setup UI buttons for sync functionality.
     */
    private function setupUIButtons(array $sourceConfigs): void
    {
        (new \Municipio\SchemaData\ExternalContent\UI\PostTableSyncButton($sourceConfigs, $this->wpService))->addHooks();
        (new \Municipio\SchemaData\ExternalContent\UI\PageRowActionsSyncButton($sourceConfigs, $this->wpService))->addHooks();
    }

    /**
     * Setup sync triggers.
     */
    private function setupSyncTriggers(array $sourceConfigs, $postTypeSyncInProgress): void
    {
        $triggerSync = new \Municipio\SchemaData\ExternalContent\SyncHandler\Triggers\TriggerSync($this->wpService);
        $triggerSync = new \Municipio\SchemaData\ExternalContent\SyncHandler\Triggers\TriggerSyncIfNotInProgress($postTypeSyncInProgress, $triggerSync);
        (new \Municipio\SchemaData\ExternalContent\SyncHandler\Triggers\TriggerSyncFromGetParams($this->wpService, $triggerSync))->addHooks();
    }

    /**
     * Setup media hiding functionality.
     */
    private function setupMediaHiding(): void
    {
        (new HideSyncedMediaFromAdminMediaLibrary(ImageSideloadSchemaObjectProcessor::META_KEY_IMAGE_ID, $this->wpService))->addHooks();
    }
}
