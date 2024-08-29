<?php

namespace Municipio\ExternalContent\Sync;

use Municipio\ExternalContent\Sources\SourceInterface;
use WpService\WpService;

class SyncBuilder
{
    /**
     * Class constructor
     *
     * @param string $postType
     * @param int|null $postId
     * @param SourceInterface[] $sources
     * @param TaxonomyItemInterface[] $taxonomyItems
     * @param WpService $wpService
     */
    public function __construct(
        private string $postType,
        private ?int $postId,
        private array $sources,
        private array $taxonomyItems,
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
        $wpPostFactoryBuilder = new \Municipio\ExternalContent\WpPostFactory\WpPostFactoryBuilder($this->taxonomyItems, $wpTermFactory, $this->wpService);
        $wpPostFactory        = $wpPostFactoryBuilder->build();

        return ($this->postId === null)
            ? new \Municipio\ExternalContent\Sync\SyncAllFromSourceToLocal($source, $wpPostFactory, $this->wpService)
            : new \Municipio\ExternalContent\Sync\SyncSingleFromSourceToLocalByPostId($this->postId, $this->sources, $wpPostFactory, $this->wpService);
    }

    /**
     * Try to get the source.
     *
     * @return SourceInterface|null The source, or null if not found.
     */
    private function tryGetSource(): ?SourceInterface
    {
        $sources = array_filter($this->sources, fn($source) => $source->getPostType() === $this->postType);
        return $sources[0] ?? null;
    }
}
