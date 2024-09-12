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

        $wpTermFactory = new \Municipio\ExternalContent\WpTermFactory\WpTermFactory();
        $wpTermFactory = new \Municipio\ExternalContent\WpTermFactory\WpTermUsingSchemaObjectName($wpTermFactory);

        $postArgsFromSchemaObject = new \Municipio\ExternalContent\WpPostArgsFromSchemaObject\WpPostFactory();
        $postArgsFromSchemaObject = new \Municipio\ExternalContent\WpPostArgsFromSchemaObject\DateDecorator($postArgsFromSchemaObject);
        $postArgsFromSchemaObject = new \Municipio\ExternalContent\WpPostArgsFromSchemaObject\IdDecorator($postArgsFromSchemaObject, $this->wpService);
        $postArgsFromSchemaObject = new \Municipio\ExternalContent\WpPostArgsFromSchemaObject\JobPostingDecorator($postArgsFromSchemaObject);
        $postArgsFromSchemaObject = new \Municipio\ExternalContent\WpPostArgsFromSchemaObject\SchemaDataDecorator($postArgsFromSchemaObject);
        $postArgsFromSchemaObject = new \Municipio\ExternalContent\WpPostArgsFromSchemaObject\OriginIdDecorator($postArgsFromSchemaObject);
        $postArgsFromSchemaObject = new \Municipio\ExternalContent\WpPostArgsFromSchemaObject\ThumbnailDecorator($postArgsFromSchemaObject, $this->wpService);
        $postArgsFromSchemaObject = new \Municipio\ExternalContent\WpPostArgsFromSchemaObject\SourceIdDecorator($postArgsFromSchemaObject);
        $postArgsFromSchemaObject = new \Municipio\ExternalContent\WpPostArgsFromSchemaObject\VersionDecorator($postArgsFromSchemaObject);
        $postArgsFromSchemaObject = new \Municipio\ExternalContent\WpPostArgsFromSchemaObject\TermsDecorator($this->taxonomyItems, $wpTermFactory, $this->wpService, $postArgsFromSchemaObject);

        if ($this->postId === null) {
            $sync = new \Municipio\ExternalContent\Sync\SyncAllFromSourceToLocal($source, $postArgsFromSchemaObject, $this->wpService);
            return new \Municipio\ExternalContent\Sync\PruneAllNoLongerInSource($source, $this->wpService, $sync);
        } else {
            return new \Municipio\ExternalContent\Sync\SyncSingleFromSourceToLocalByPostId($this->postId, $this->sources, $postArgsFromSchemaObject, $this->wpService);
        }
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
