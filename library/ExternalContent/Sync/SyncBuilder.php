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

        $wpPostFromSchemaObject = new \Municipio\ExternalContent\WpPostArgsFromSchemaObject\WpPostFactory();
        $wpPostFromSchemaObject = new \Municipio\ExternalContent\WpPostArgsFromSchemaObject\DateDecorator($wpPostFromSchemaObject);
        $wpPostFromSchemaObject = new \Municipio\ExternalContent\WpPostArgsFromSchemaObject\IdDecorator($wpPostFromSchemaObject, $this->wpService);
        $wpPostFromSchemaObject = new \Municipio\ExternalContent\WpPostArgsFromSchemaObject\JobPostingDecorator($wpPostFromSchemaObject);
        $wpPostFromSchemaObject = new \Municipio\ExternalContent\WpPostArgsFromSchemaObject\SchemaDataDecorator($wpPostFromSchemaObject);
        $wpPostFromSchemaObject = new \Municipio\ExternalContent\WpPostArgsFromSchemaObject\OriginIdDecorator($wpPostFromSchemaObject);
        $wpPostFromSchemaObject = new \Municipio\ExternalContent\WpPostArgsFromSchemaObject\ThumbnailDecorator($wpPostFromSchemaObject, $this->wpService);
        $wpPostFromSchemaObject = new \Municipio\ExternalContent\WpPostArgsFromSchemaObject\SourceIdDecorator($wpPostFromSchemaObject);
        $wpPostFromSchemaObject = new \Municipio\ExternalContent\WpPostArgsFromSchemaObject\VersionDecorator($wpPostFromSchemaObject);
        $wpPostFromSchemaObject = new \Municipio\ExternalContent\WpPostArgsFromSchemaObject\TermsDecorator($this->taxonomyItems, $wpTermFactory, $this->wpService, $wpPostFromSchemaObject);

        return ($this->postId === null)
            ? new \Municipio\ExternalContent\Sync\SyncAllFromSourceToLocal(
                $source,
                $wpPostFromSchemaObject,
                $this->wpService
            )
            : new \Municipio\ExternalContent\Sync\SyncSingleFromSourceToLocalByPostId(
                $this->postId,
                $this->sources,
                $wpPostFromSchemaObject,
                $this->wpService
            );
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
