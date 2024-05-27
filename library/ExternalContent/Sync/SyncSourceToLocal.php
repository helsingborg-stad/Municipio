<?php

namespace Municipio\ExternalContent\Sync;

use Municipio\ExternalContent\Sources\ISource;
use Municipio\ExternalContent\WpPostFactory\WpPostFactoryInterface;
use Municipio\ExternalContent\WpPostMetaFactory\WpPostMetaFactoryInterface;
use Spatie\SchemaOrg\BaseType;
use WpService\Contracts\InsertPost;

class SyncSourceToLocal implements ISyncSourceToLocal
{
    public function __construct(
        private WpPostFactoryInterface $wpPostFactory,
        private WpPostMetaFactoryInterface $wpPostMetaFactory,
        private InsertPost $wpService
    ) {
    }

    public function sync(ISource $source): void
    {
        $posts = array_map(function (BaseType $schemaObject) use ($source) {
            return $this->getPostArrayToInsert($schemaObject, $source);
        }, $source->getObjects());

        $posts = array_map(function (array $postData) use ($source) {
            return $this->setPostTypeFromSourceAndReturnPost($postData, $source);
        }, $posts);

        array_map([$this->wpService, 'insertPost'], $posts);
    }

    private function getPostArrayToInsert(BaseType $schemaObject, ISource $source): array
    {
        $postData               = $this->wpPostFactory->create($schemaObject, $source)->to_array();
        $postData['meta_input'] = $this->wpPostMetaFactory->create($schemaObject, $source);

        return $postData;
    }

    private function setPostTypeFromSourceAndReturnPost(array $postData, ISource $source): array
    {
        $postData['post_type'] = $source->getPostType();
        return $postData;
    }
}
