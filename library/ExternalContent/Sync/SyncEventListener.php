<?php

namespace Municipio\ExternalContent\Sync;

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
     * @param \Municipio\ExternalContent\Sources\SourceInterface[] $sources
     * @param TaxonomyItemInterface[] $taxonomyItems
     * @param AddAction $wpService
     */
    public function __construct(
        private array $sources,
        private array $taxonomyItems,
        private AddAction $wpService,
        private wpdb $wpdb
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
        $syncBuilder           = new \Municipio\ExternalContent\Sync\SyncBuilder(
            $postType,
            $postId,
            $this->sources,
            $this->taxonomyItems,
            $this->wpService,
            $this->wpdb
        );
        $syncFromSourceToLocal = $syncBuilder->build();
        $syncFromSourceToLocal->sync();
    }
}
