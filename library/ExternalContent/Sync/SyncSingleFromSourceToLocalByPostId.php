<?php

namespace Municipio\ExternalContent\Sync;

use Municipio\ExternalContent\Sources\ISource;
use Municipio\ExternalContent\Sources\ISourceRegistry;
use Municipio\ExternalContent\WpPostFactory\WpPostFactoryInterface;
use Municipio\ExternalContent\WpPostMetaFactory\WpPostMetaFactoryInterface;
use Spatie\SchemaOrg\BaseType;
use WpService\Contracts\GetPost;
use WpService\Contracts\GetPostMeta;
use WpService\Contracts\InsertPost;

class SyncSingleFromSourceToLocalByPostId implements ISyncSourceToLocal
{
    public function __construct(
        private int|string $postId,
        private ISourceRegistry $sourceRegistry,
        private WpPostFactoryInterface $wpPostFactory,
        private WpPostMetaFactoryInterface $wpPostMetaFactory,
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
        $postDataToInsert =  $this->getPostArrayToInsert($schemaObject, $source);
        $this->wpService->insertPost($postDataToInsert);
    }

    /**
     * Get post array to insert.
     */
    private function getPostArrayToInsert(BaseType $schemaObject, ISource $source): array
    {
        $postData               = $this->wpPostFactory->create($schemaObject, $source)->to_array();
        $postData['meta_input'] = $this->wpPostMetaFactory->create($schemaObject, $source);

        return $postData;
    }
}
