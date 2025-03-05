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
use Spatie\SchemaOrg\BaseType;
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
        private WpService $wpService
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
    public function sync(string $postType, ?int $postId): void
    {
        $sourceConfig = $this->getSourceConfigByPostType($postType);

        if (is_int($postId)) {
            $sourceConfig = $this->addIdFilterToSourceConfig($sourceConfig, $postId);
        }

        // Cleanup only if a sync is triggered to trigger whole collection.
        if (is_null($postId)) {
            $this->setupCleanup($sourceConfig, $postType);
        }

        // Apply filters before sync.
        $this->applyFiltersBeforeSync($sourceConfig);

        $schemaObjects   = $this->getSourceReader($sourceConfig)->getSourceData();
        $wpPostArgsArray = array_map(fn($schemaObject) => $this->getPostFactory($sourceConfig)->transform($schemaObject), $schemaObjects);

        $schemaObjects = $this->wpService->applyFiltersRefArray(self::FILTER_BEFORE, [$schemaObjects]);

        foreach ($wpPostArgsArray as $postArgs) {
            $this->wpService->wpInsertPost($postArgs);
        }

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
     * @param SourceConfigInterface $sourceConfig The source configuration to use for cleanup.
     * @param string $postType The post type to clean up.
     * @return void
     */
    private function setupCleanup(SourceConfigInterface $sourceConfig, string $postType): void
    {
        (new \Municipio\ExternalContent\SyncHandler\Cleanup\CleanupPostsNoLongerInSource($postType, $this->wpService))->addHooks();
        (new \Municipio\ExternalContent\SyncHandler\Cleanup\CleanupAttachmentsNoLongerInUse($this->wpService, $GLOBALS['wpdb']))->addHooks();
        (new \Municipio\ExternalContent\SyncHandler\Cleanup\CleanupTermsNoLongerInUse($sourceConfig, $this->wpService))->addHooks();
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
