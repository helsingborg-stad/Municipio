<?php

namespace Municipio\ExternalContent\Sync;

use Municipio\ExternalContent\Sources\SourceInterface;
use Municipio\ExternalContent\WpPostArgsFromSchemaObject\WpPostArgsFromSchemaObjectInterface;
use WpService\Contracts\GetPostMeta;
use WpService\Contracts\GetPostType;
use WpService\Contracts\WpInsertPost;

/**
 * Sync single post from source to local by post id.
 */
class SyncSingleFromSourceToLocalByPostId implements SyncSourceToLocalInterface
{
    /**
     * Class constructor
     */
    public function __construct(
        private int|string $postId,
        private SourceInterface $source,
        private WpPostArgsFromSchemaObjectInterface $wpPostFactory,
        private WpInsertPost&GetPostMeta&GetPostType $wpService
    ) {
    }

    /**
     * @inheritDoc
     */
    public function sync(): void
    {
        $originId = $this->wpService->getPostMeta($this->postId, 'originId', true);
        $postType = $this->wpService->getPostType($this->postId);

        if (empty($originId) || empty($postType)) {
            return;
        }

        $schemaObject     = $this->source->getObject($originId);
        $postDataToInsert = $this->wpPostFactory->create($schemaObject, $this->source);
        $this->wpService->wpInsertPost($postDataToInsert);
    }
}
