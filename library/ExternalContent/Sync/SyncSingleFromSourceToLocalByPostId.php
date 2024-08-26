<?php

namespace Municipio\ExternalContent\Sync;

use Municipio\ExternalContent\Sources\SourceInterface;
use Municipio\ExternalContent\Sources\SourceRegistryInterface;
use Municipio\ExternalContent\WpPostFactory\WpPostFactoryInterface;
use Municipio\ExternalContent\WpTermFactory\WpTermFactoryInterface;
use Municipio\HooksRegistrar\Hookable;
use Spatie\SchemaOrg\BaseType;
use WpService\Contracts\AddAction;
use WpService\Contracts\GetPostMeta;
use WpService\Contracts\InsertPost;

class SyncSingleFromSourceToLocalByPostId implements SyncSourceToLocalInterface
{
    public function __construct(
        private int|string $postId,
        private SourceRegistryInterface $sourceRegistry,
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

        $source = $this->sourceRegistry->getSourceById($sourceId);

        if (!$source) {
            return;
        }

        $schemaObject     = $source->getObject($originId);
        $postDataToInsert = $this->wpPostFactory->create($schemaObject, $source);
        $this->wpService->insertPost($postDataToInsert);
    }
}
