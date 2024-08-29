<?php

namespace Municipio\ExternalContent\Sync;

use Municipio\ExternalContent\Sources\SourceInterface;
use Municipio\ExternalContent\WpPostFactory\WpPostFactoryInterface;
use WpService\Contracts\GetPostMeta;
use WpService\Contracts\InsertPost;

class SyncSingleFromSourceToLocalByPostId implements SyncSourceToLocalInterface
{
    /**
     * Class constructor
     *
     * @param int|string $postId
     * @param SourceInterface[] $sources
     * @param WpPostFactoryInterface $wpPostFactory
     * @param InsertPost&GetPostMeta $wpService
     */
    public function __construct(
        private int|string $postId,
        private array $sources,
        private WpPostFactoryInterface $wpPostFactory,
        private InsertPost&GetPostMeta $wpService
    ) {
    }

    /**
     * @inheritDoc
     */
    public function sync(): void
    {
        $sourceId = $this->wpService->getPostMeta($this->postId, 'sourceId', true);
        $originId = $this->wpService->getPostMeta($this->postId, 'originId', true);

        if (empty($sourceId) || empty($originId)) {
            return;
        }

        $source = array_filter($this->sources, fn($source) => $source->getId() === $sourceId)[0] ?? null;

        if (!$source) {
            return;
        }

        $schemaObject     = $source->getObject($originId);
        $postDataToInsert = $this->wpPostFactory->create($schemaObject, $source);
        $this->wpService->insertPost($postDataToInsert);
    }
}
