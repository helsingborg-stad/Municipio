<?php

namespace Municipio\ExternalContent\Sync;

use Municipio\ExternalContent\SourceReaders\SourceReaderInterface;
use Municipio\ExternalContent\Sources\SourceInterface;
use Municipio\ExternalContent\WpPostArgsFromSchemaObject\AddChecksum;
use Municipio\ExternalContent\WpPostArgsFromSchemaObject\DateDecorator;
use Municipio\ExternalContent\WpPostArgsFromSchemaObject\IdDecorator;
use Municipio\ExternalContent\WpPostArgsFromSchemaObject\JobPostingDecorator;
use Municipio\ExternalContent\WpPostArgsFromSchemaObject\MetaPropertyValueDecorator;
use Municipio\ExternalContent\WpPostArgsFromSchemaObject\OriginIdDecorator;
use Municipio\ExternalContent\WpPostArgsFromSchemaObject\PostTypeDecorator;
use Municipio\ExternalContent\WpPostArgsFromSchemaObject\SchemaDataDecorator;
use Municipio\ExternalContent\WpPostArgsFromSchemaObject\SourceIdDecorator;
use Municipio\ExternalContent\WpPostArgsFromSchemaObject\TermsDecorator;
use Municipio\ExternalContent\WpPostArgsFromSchemaObject\ThumbnailDecorator;
use Municipio\ExternalContent\WpPostArgsFromSchemaObject\VerifyChecksum;
use Municipio\ExternalContent\WpPostArgsFromSchemaObject\WpPostArgsFromSchemaObject;
use wpdb;
use WpService\WpService;

/**
 * Class SyncBuilder
 */
class SyncBuilder implements SyncBuilderInterface
{
    /**
     * Class constructor
     *
     * @param string $postType
     * @param int|null $postId
     * @param SourceReaderInterface $sourceReader
     * @param TaxonomyItemInterface[] $taxonomyItems
     * @param WpService $wpService
     */
    public function __construct(
        private string $postType,
        private ?int $postId,
        private SourceReaderInterface $sourceReader,
        private array $taxonomyItems,
        private WpService $wpService,
        private wpdb $wpdb
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

        $postArgsFromSchemaObject = new WpPostArgsFromSchemaObject();
        $postArgsFromSchemaObject = new PostTypeDecorator($this->postType, $postArgsFromSchemaObject);
        $postArgsFromSchemaObject = new DateDecorator($postArgsFromSchemaObject);
        $postArgsFromSchemaObject = new IdDecorator($this->postType, $source->getId(), $postArgsFromSchemaObject, $this->wpService);
        $postArgsFromSchemaObject = new JobPostingDecorator($postArgsFromSchemaObject);
        $postArgsFromSchemaObject = new SchemaDataDecorator($postArgsFromSchemaObject);
        $postArgsFromSchemaObject = new OriginIdDecorator($postArgsFromSchemaObject);
        $postArgsFromSchemaObject = new ThumbnailDecorator($postArgsFromSchemaObject, $this->wpService);
        $postArgsFromSchemaObject = new SourceIdDecorator($source->getId(), $postArgsFromSchemaObject);
        $postArgsFromSchemaObject = new MetaPropertyValueDecorator($postArgsFromSchemaObject);
        $postArgsFromSchemaObject = new TermsDecorator($this->taxonomyItems, $wpTermFactory, $this->wpService, $postArgsFromSchemaObject); // phpcs:ignore Generic.Files.LineLength.TooLong
        $postArgsFromSchemaObject = new AddChecksum($postArgsFromSchemaObject);
        $postArgsFromSchemaObject = new VerifyChecksum($postArgsFromSchemaObject, $this->wpService);

        if ($this->postId === null) {
            $sync = new SyncAllFromSourceToLocal($source, $postArgsFromSchemaObject, $this->wpService);
            $sync = new PrunePostsNoLongerInSource($source, $this->wpService, $sync);
            $sync = new PruneTermsNoLongerInUse($source, $this->wpService, $sync);
            return new PruneAttachmentsNoLongerInUse($this->wpService, $this->wpdb, $sync);
        } else {
            return new SyncSingleFromSourceToLocalByPostId(
                $this->postId,
                $source,
                $postArgsFromSchemaObject,
                $this->wpService
            );
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
        return reset($sources) ?: null;
    }
}
