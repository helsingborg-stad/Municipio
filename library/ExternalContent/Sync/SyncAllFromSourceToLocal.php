<?php

namespace Municipio\ExternalContent\Sync;

use Municipio\ExternalContent\Sources\SourceInterface;
use Municipio\ExternalContent\WpPostFactory\WpPostFactoryInterface;
use Spatie\SchemaOrg\BaseType;
use WpService\Contracts\InsertPost;

class SyncAllFromSourceToLocal implements SyncSourceToLocalInterface
{
    public function __construct(
        private SourceInterface $source,
        private WpPostFactoryInterface $wpPostFactory,
        private InsertPost $wpService
    ) {
    }

    /**
     * @inheritDoc
     */
    public function sync(): void
    {
        $posts = array_map([$this, 'getPostArrayToInsert'], $this->source->getObjects());

        foreach ($posts as $post) {
            $this->wpService->insertPost($post);
        }
    }

    /**
     * Get post array to insert.
     */
    private function getPostArrayToInsert(BaseType $schemaObject): array
    {
        return $this->wpPostFactory->create($schemaObject, $this->source);
    }
}
