<?php

namespace Municipio\ExternalContent\Sync;

use Municipio\ExternalContent\Sources\ISource;
use Municipio\ExternalContent\WpPostFactory\WpPostFactoryInterface;
use Municipio\ExternalContent\WpPostMetaFactory\WpPostMetaFactoryInterface;
use Spatie\SchemaOrg\BaseType;
use WpService\Contracts\InsertPost;

class SyncAllFromSourceToLocal implements ISyncSourceToLocal
{
    public function __construct(
        private ISource $source,
        private WpPostFactoryInterface $wpPostFactory,
        private WpPostMetaFactoryInterface $wpPostMetaFactory,
        private InsertPost $wpService
    ) {
    }

    /**
     * @inheritDoc
     */
    public function sync(): void
    {
        $posts = array_map([$this, 'getPostArrayToInsert'], $this->source->getObjects());
        array_map([$this->wpService, 'insertPost'], $posts);
    }

    /**
     * Get post array to insert.
     */
    private function getPostArrayToInsert(BaseType $schemaObject): array
    {
        $postData               = $this->wpPostFactory->create($schemaObject, $this->source)->to_array();
        $postData['meta_input'] = $this->wpPostMetaFactory->create($schemaObject, $this->source);

        return $postData;
    }
}
