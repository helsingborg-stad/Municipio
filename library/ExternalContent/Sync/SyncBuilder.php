<?php

namespace Municipio\ExternalContent\Sync;

use Municipio\ExternalContent\Sources\SourceInterface;
use Municipio\ExternalContent\Sources\SourceRegistryInterface;
use Municipio\ExternalContent\Taxonomy\TaxonomyRegistrarInterface;
use WpService\WpService;

class SyncBuilder
{
    public function __construct(
        private string $postType,
        private ?int $postId,
        private SourceRegistryInterface $sourceRegistry,
        private TaxonomyRegistrarInterface $taxonomyRegistrar,
        private WpService $wpService
    ) {
    }

    /**
     * @inheritDoc
     */
    public function build(): SyncSourceToLocalInterface
    {
        $source = $this->tryGetSource();

        if ($source === null) {
            return new NullSync();
        }

        $wpTermFactory        = new \Municipio\ExternalContent\WpTermFactory\WpTermFactory();
        $wpTermFactory        = new \Municipio\ExternalContent\WpTermFactory\WpTermUsingSchemaObjectName($wpTermFactory);
        $wpPostFactoryBuilder = new \Municipio\ExternalContent\WpPostFactory\WpPostFactoryBuilder($this->taxonomyRegistrar, $wpTermFactory, $this->wpService);
        $wpPostFactory        = $wpPostFactoryBuilder->build();

        return ($this->postId === null)
            ? new \Municipio\ExternalContent\Sync\SyncAllFromSourceToLocal($source, $wpPostFactory, $this->wpService)
            : new \Municipio\ExternalContent\Sync\SyncSingleFromSourceToLocalByPostId($this->postId, $this->sourceRegistry, $wpPostFactory, $this->wpService);
    }

    /**
     * Try to get the source.
     *
     * @return ISource|null The source, or null if not found.
     */
    private function tryGetSource(): ?SourceInterface
    {
        $sources = array_filter($this->sourceRegistry->getSources(), fn($source) => $source->getPostType() === $this->postType);
        return $sources[0] ?? null;
    }
}
