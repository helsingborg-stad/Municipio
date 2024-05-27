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
        $posts = array_map([$this, 'getPostArrayToInsert'], $source->getObjects());
        $posts = array_map(fn($post) => $this->setPostTypeFromSourceAndReturnPost($source, $post), $posts);
        array_map([$this->wpService, 'insertPost'], $posts);
    }

    private function getPostArrayToInsert(BaseType $schemaObject): array
    {
        $postData               = $this->wpPostFactory->create($schemaObject)->to_array();
        $postData['meta_input'] = $this->wpPostMetaFactory->create($schemaObject);

        return $postData;
    }

    private function setPostTypeFromSourceAndReturnPost(ISource $source, array $postData): array
    {
        $postData['post_type'] = $source->getPostType();
        return $postData;
    }
}
