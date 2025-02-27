<?php

namespace Municipio\ExternalContent\Sync;

use Municipio\ExternalContent\Config\SourceConfigInterface;
use Municipio\HooksRegistrar\Hookable;
use wpdb;
use WpService\Contracts\AddAction;

/**
 * Class SyncEventListener
 */
class SyncEventListener implements Hookable
{
    /**
     * Class constructor
     *
     * @param SourceConfigInterface[] $sourceConfigurations
     * @param TaxonomyItemInterface[] $taxonomyItems
     * @param AddAction $wpService
     * @param wpdb $wpdb
     */
    public function __construct(
        private array $sourceConfigurations,
        private array $taxonomyItems,
        private AddAction $wpService,
        private wpdb $wpdb,
        private CretaeSourceReaderFromSourceConfig $sourceReaderFactory
    ) {
    }

    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        $this->wpService->addAction('Municipio/ExternalContent/Sync', array($this, 'sync'), 10, 2);
    }

    /**
     * @inheritDoc
     */
    public function sync(string $postType, ?int $postId = null): void
    {
        $sourceConfig = $this->getSourceConfigFromPostType($postType);
        $sourceReader = $this->sourceReaderFactory->create($sourceConfig);

        $syncBuilder           = new \Municipio\ExternalContent\Sync\SyncBuilder(
            $postType,
            $postId,
            $sourceConfig,
            $this->taxonomyItems,
            $this->wpService,
            $this->wpdb
        );
        $syncFromSourceToLocal = $syncBuilder->build();
        $syncFromSourceToLocal->sync();
    }

    private function getSourceConfigFromPostType(string $postType): ?SourceConfigInterface
    {
        return array_filter($this->sourceConfigurations, fn($config) => $config->getPostType() === $postType)[0] ?? null;
    }
}
