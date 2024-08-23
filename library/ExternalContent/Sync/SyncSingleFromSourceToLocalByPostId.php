<?php

namespace Municipio\ExternalContent\Sync;

use Municipio\ExternalContent\Sources\ISource;
use Municipio\ExternalContent\Sources\ISourceRegistry;
use Municipio\ExternalContent\WpPostFactory\WpPostFactoryInterface;
use Municipio\ExternalContent\WpTermFactory\WpTermFactoryInterface;
use Municipio\HooksRegistrar\Hookable;
use Spatie\SchemaOrg\BaseType;
use WpService\Contracts\AddAction;
use WpService\Contracts\GetPostMeta;
use WpService\Contracts\InsertPost;

class SyncSingleFromSourceToLocalByPostId implements ISyncSourceToLocal
{
    public function __construct(
        private int|string $postId,
        private ISourceRegistry $sourceRegistry,
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
