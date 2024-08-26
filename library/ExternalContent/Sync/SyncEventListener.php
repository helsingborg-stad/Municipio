<?php

namespace Municipio\ExternalContent\Sync;

use Municipio\ExternalContent\Sources\SourceRegistryInterface;
use Municipio\ExternalContent\Taxonomy\TaxonomyRegistrarInterface;
use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddAction;

class SyncEventListener implements Hookable
{
    public function __construct(
        private SourceRegistryInterface $sourceRegistry,
        private TaxonomyRegistrarInterface $taxonomyRegistrar,
        private AddAction $wpService
    ) {
    }

    public function addHooks(): void
    {
        $this->wpService->addAction('Municipio/ExternalContent/Sync', array($this, 'sync'));
    }

    public function sync(string $postType, ?int $postId = null): void
    {
        $syncBuilder           = new \Municipio\ExternalContent\Sync\SyncBuilder($postType, $postId, $this->sourceRegistry, $this->taxonomyRegistrar, $this->wpService);
        $syncFromSourceToLocal = $syncBuilder->build();
        $syncFromSourceToLocal->sync();
    }
}
