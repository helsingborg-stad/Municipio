<?php

namespace Municipio\ExternalContent\SyncHandler;

use Municipio\ExternalContent\Config\SourceConfigInterface;
use Municipio\ExternalContent\Config\SourceConfigWithCustomFilterDefinition;
use Municipio\ExternalContent\Filter\FilterDefinition\FilterDefinition;
use Municipio\ExternalContent\Filter\FilterDefinition\Rule;
use Municipio\ExternalContent\Filter\FilterDefinition\RuleSet;
use Municipio\ExternalContent\SourceReaders\SourceReaderInterface;
use Municipio\ExternalContent\WpPostArgsFromSchemaObject\WpPostArgsFromSchemaObjectInterface;
use Municipio\HooksRegistrar\Hookable;
use Municipio\ProgressReporter\ProgressReporterInterface;
use Municipio\Schema\BaseType;
use WpService\WpService;

/**
 * Class SyncHandler
 */
class SyncHandler implements Hookable
{
    public const FILTER_BEFORE = 'Municipio/ExternalContent/Sync/Filter/Before';
    public const ACTION_AFTER  = 'Municipio/ExternalContent/Sync/After';

    /**
     * Constructor for the SyncHandler class.
     *
     * @param SourceConfigInterface[] $sourceConfigs
     */
    public function __construct(
        private array $sourceConfigs,
        private WpService $wpService,
        private ProgressReporterInterface $progressService
    ) {
    }

    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        $this->wpService->addAction('Municipio/ExternalContent/Sync', [$this, 'sync'], 10, 2);
    }

    /**
     * @inheritDoc
     */
    public function sync(string $postType, ?int $postId = null): void
    {
        // allow sync to take longer than current max_execution_time
        set_time_limit(0);

        $sourceConfig = $this->getSourceConfigByPostType($postType);

        if (is_int($postId)) {
            $sourceConfig = $this->addIdFilterToSourceConfig($sourceConfig, $postId);
        }

        // Cleanup only if a sync is triggered to trigger whole collection.
        if (is_null($postId)) {
            $this->setupCleanup($postType);
        }

        // Apply filters before sync.
        $this->applyFiltersBeforeSync($sourceConfig);


        $this->progressService->setMessage($this->wpService->__('Fetching source data...', 'municipio'));
        $schemaObjects = $this->getSourceReader($sourceConfig)->getSourceData();
        $schemaObjects = array_values($schemaObjects); // Reset array keys to avoid issues with missing keys.
        $count         = count($schemaObjects);

        $this->progressService->setMessage(sprintf($this->wpService->__("Converting %s schema objects to WP_Post objects.", 'municipio'), $count));

        $wpPostArgsArray = [];
        $totalObjects    = count($schemaObjects);
        for ($i = 0; $i < count($schemaObjects); $i++) {
            $iPlusOne = $i + 1;
            $this->progressService->setMessage(sprintf($this->wpService->__("Converting post %s of %s...", 'municipio'), $iPlusOne, $totalObjects));
            $this->progressService->setPercentage(($iPlusOne / $totalObjects) * 100);
            if ($schemaObjects[$i] === null) {
                continue;
            }

            if (!empty($schemaObjects[$i])) {
                $wpPostArgsArray[] = $this->getPostFactory($sourceConfig)->transform($schemaObjects[$i]);
            }
        }


        $schemaObjects = $this->wpService->applyFiltersRefArray(self::FILTER_BEFORE, [$schemaObjects]);

        $this->progressService->setMessage($this->wpService->__("Inserting posts...", 'municipio'));
        $this->progressService->setPercentage(0);

        for ($i = 0; $i < count($wpPostArgsArray); $i++) {
            $this->wpService->wpInsertPost($wpPostArgsArray[$i]);
            $iPlusOne = $i + 1;
            $this->progressService->setPercentage(($iPlusOne / $count) * 100);
        }

        $this->progressService->setMessage($this->wpService->__("Cleaning up...", 'municipio'));

        /**
         * Action after sync.
         *
         * @param BaseType[] $schemaObjects
         */
        $this->wpService->doActionRefArray(self::ACTION_AFTER, [$schemaObjects]);
    }

    /**
     * Retrieves the source reader based on the provided source configuration.
     *
     * @param SourceConfigInterface $sourceConfig The configuration for the source.
     * @return SourceReaderInterface The source reader instance.
     */
    private function getSourceReader(SourceConfigInterface $sourceConfig): SourceReaderInterface
    {
        return (new \Municipio\ExternalContent\SourceReaders\Factories\SourceReaderFromConfig())->create($sourceConfig);
    }

    /**
     * Retrieves the post factory instance based on the provided source configuration.
     *
     * @param SourceConfigInterface $sourceConfig The source configuration object.
     * @return WpPostArgsFromSchemaObjectInterface The post factory instance.
     */
    private function getPostFactory(SourceConfigInterface $sourceConfig): WpPostArgsFromSchemaObjectInterface
    {
        return (new \Municipio\ExternalContent\WpPostArgsFromSchemaObject\Factory\Factory($sourceConfig))->create();
    }

    /**
     * Sets up the cleanup process for the given source configuration and post type.
     *
     * @param string $postType The post type to clean up.
     * @return void
     */
    private function setupCleanup(string $postType): void
    {
        (new \Municipio\ExternalContent\SyncHandler\Cleanup\CleanupPostsNoLongerInSource($postType, $this->wpService))->addHooks();
        (new \Municipio\ExternalContent\SyncHandler\Cleanup\CleanupAttachmentsNoLongerInUse($this->wpService, $GLOBALS['wpdb']))->addHooks();
    }

    /**
     * Adds an ID filter to the source configuration.
     *
     * @param SourceConfigInterface $sourceConfig The source configuration to which the ID filter will be added.
     * @param int $postId The ID of the post to filter by.
     * @return SourceConfigInterface The modified source configuration with the ID filter applied.
     */
    private function addIdFilterToSourceConfig(SourceConfigInterface $sourceConfig, int $postId): SourceConfigInterface
    {
        $originId = $this->wpService->getPostMeta($postId, 'originId', true);

        if (empty($originId)) {
            return $sourceConfig;
        }

        $filterDefinition = new FilterDefinition([new RuleSet([new Rule('@id', $originId)])]);
        return new SourceConfigWithCustomFilterDefinition($filterDefinition, $sourceConfig);
    }

    /**
     * Applies necessary filters before synchronization.
     *
     * This method is responsible for applying any filters or transformations
     * to the data before the synchronization process begins.
     *
     * @return void
     */
    private function applyFiltersBeforeSync(): void
    {
        (new \Municipio\ExternalContent\SyncHandler\FilterBeforeSync\FilterOutDuplicateObjectById())->addHooks();
    }

    /**
     * Retrieves the source configuration for a given post type.
     *
     * @param string $postType The post type to get the source configuration for.
     * @return SourceConfigInterface The source configuration for the specified post type.
     */
    private function getSourceConfigByPostType(string $postType): SourceConfigInterface
    {
        $filtered = array_filter($this->sourceConfigs, fn($config) => $config->getPostType() === $postType);
        return reset($filtered);
    }
}
