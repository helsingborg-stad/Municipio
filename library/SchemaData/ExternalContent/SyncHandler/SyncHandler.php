<?php

namespace Municipio\SchemaData\ExternalContent\SyncHandler;

use Municipio\HooksRegistrar\Hookable;
use Municipio\ProgressReporter\ProgressReporterInterface;
use Municipio\Schema\BaseType;
use Municipio\SchemaData\ExternalContent\Config\SourceConfigInterface;
use Municipio\SchemaData\ExternalContent\Config\SourceConfigWithCustomFilterDefinition;
use Municipio\SchemaData\ExternalContent\Exception\ExternalContentException;
use Municipio\SchemaData\ExternalContent\Filter\FilterDefinition\FilterDefinition;
use Municipio\SchemaData\ExternalContent\Filter\FilterDefinition\Rule;
use Municipio\SchemaData\ExternalContent\Filter\FilterDefinition\RuleSet;
use Municipio\SchemaData\ExternalContent\SourceReaders\SourceReaderInterface;
use Municipio\SchemaData\ExternalContent\SyncHandler\MetaDataFromSchema\MetaDataItemsFromSchema;
use Municipio\SchemaData\ExternalContent\WpPostArgsFromSchemaObject\WpPostArgsFromSchemaObjectInterface;
use WpService\WpService;

/**
 * Class SyncHandler
 */
class SyncHandler implements Hookable, SyncHandlerInterface
{
    /**
     * Constructor for the SyncHandler class.
     *
     * @param SourceConfigInterface[] $sourceConfigs
     * @param WpService $wpService
     * @param ProgressReporterInterface $progressService
     * @param SchemaObjectProcessorInterface[] $schemaObjectsProcessors
     * @param MetaDataItemsFromSchema $metaDataItemsFromSchema
     */
    public function __construct(
        private array $sourceConfigs,
        private WpService $wpService,
        private ProgressReporterInterface $progressService,
        private array $schemaObjectProcessors = [],
        private MetaDataItemsFromSchema $metaDataItemsFromSchema = new MetaDataItemsFromSchema(),
    ) {}

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
    public function sync(string $postType, null|int $postId = null): void
    {
        // allow sync to take longer than current max_execution_time
        set_time_limit(0);

        $sourceConfig = $this->getSourceConfigByPostType($postType);

        if (is_int($postId)) {
            $sourceConfig = $this->addIdFilterToSourceConfig($sourceConfig, $postId);
        }

        $this->progressService->setMessage($this->wpService->__('Fetching source data...', 'municipio'));
        $schemaObjects = $this->getSourceReader($sourceConfig)->getSourceData();

        if (empty($schemaObjects)) {
            return;
        }

        $schemaObjects = (new \Municipio\SchemaData\ExternalContent\SyncHandler\FilterBeforeSync\FilterOutDuplicateObjectById())->filter($schemaObjects);
        $schemaObjects = (new \Municipio\SchemaData\ExternalContent\SyncHandler\FilterBeforeSync\ConvertImagePropsToImageObjects($this->wpService))->convert($schemaObjects);
        $newOrChangedSchemaObjects = (new \Municipio\SchemaData\ExternalContent\SyncHandler\FilterBeforeSync\FilterOutObjectsThatHaveNotChanged($GLOBALS['wpdb'], $postType))->filter($schemaObjects);

        $schemaObjects = array_values(array_filter($schemaObjects));
        $newOrChangedSchemaObjects = array_values(array_filter($newOrChangedSchemaObjects));

        if (empty($schemaObjects)) {
            return;
        }

        $totalObjects = count($newOrChangedSchemaObjects);
        $insertedWpPostsCount = 0;

        foreach ($newOrChangedSchemaObjects as $i => $schemaObject) {
            $iPlusOne = $i + 1;
            $this->progressService->setMessage(sprintf($this->wpService->__('Processing %s of %s...', 'municipio'), $iPlusOne, $totalObjects));
            $this->progressService->setPercentage(($iPlusOne / $totalObjects) * 100);

            foreach ($this->schemaObjectProcessors as $processor) {
                $schemaObject = $processor->process($schemaObject);
            }

            $args = $this->getPostFactory($sourceConfig)->transform($schemaObject);
            $postInserted = $this->wpService->wpInsertPost($args);

            if ($this->wpService->isWpError($postInserted)) {
                error_log('Failed to insert/update post for post type: ' . $postType . '. Error: ' . $postInserted->get_error_message());
                continue;
            }

            $this->setMetaDataFromSchema($schemaObject, $postInserted);

            $insertedWpPostsCount++;
        }

        $this->progressService->setMessage($this->wpService->__('Cleaning up...', 'municipio'));

        if ($insertedWpPostsCount !== $totalObjects) {
            throw new ExternalContentException('Failed to insert: ' . $postType . '. Expected ' . $totalObjects . ' but got ' . $insertedWpPostsCount);
        }

        if ($postId === null) {
            // Run only for entire sync
            (new \Municipio\SchemaData\ExternalContent\SyncHandler\Cleanup\CleanupPostsNoLongerInSource($postType, $this->wpService))->cleanup($schemaObjects);
        }

        (new \Municipio\SchemaData\ExternalContent\SyncHandler\Cleanup\CleanupAttachmentsNoLongerInUse($this->wpService, $GLOBALS['wpdb']))->cleanup();
    }

    private function setMetaDataFromSchema(BaseType $schema, int $postInserted): void
    {
        $metaDataItems = $this->metaDataItemsFromSchema->getMetaDataItems($schema);
        $metaDataItemsKeys = array_map(fn($item) => $item->getKey(), $metaDataItems);
        $metaDataItemsKeys = array_unique($metaDataItemsKeys);

        foreach ($metaDataItemsKeys as $key) {
            $this->wpService->deletePostMeta($postInserted, $key);
        }

        foreach ($metaDataItems as $metaDataItem) {
            $this->wpService->addPostMeta($postInserted, $metaDataItem->getKey(), $metaDataItem->getValue());
        }
    }

    /**
     * Retrieves the source reader based on the provided source configuration.
     *
     * @param SourceConfigInterface $sourceConfig The configuration for the source.
     * @return SourceReaderInterface The source reader instance.
     */
    private function getSourceReader(SourceConfigInterface $sourceConfig): SourceReaderInterface
    {
        return (new \Municipio\SchemaData\ExternalContent\SourceReaders\Factories\SourceReaderFromConfig())->create($sourceConfig);
    }

    /**
     * Retrieves the post factory instance based on the provided source configuration.
     *
     * @param SourceConfigInterface $sourceConfig The source configuration object.
     * @return WpPostArgsFromSchemaObjectInterface The post factory instance.
     */
    private function getPostFactory(SourceConfigInterface $sourceConfig): WpPostArgsFromSchemaObjectInterface
    {
        return (new \Municipio\SchemaData\ExternalContent\WpPostArgsFromSchemaObject\Factory\Factory($sourceConfig))->create();
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
