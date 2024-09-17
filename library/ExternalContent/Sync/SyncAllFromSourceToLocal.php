<?php

namespace Municipio\ExternalContent\Sync;

use Municipio\ExternalContent\Sources\SourceInterface;
use Municipio\ExternalContent\WpPostArgsFromSchemaObject\WpPostArgsFromSchemaObjectInterface;
use Spatie\SchemaOrg\BaseType;
use WpService\Contracts\DoAction;
use WpService\Contracts\InsertPost;

class SyncAllFromSourceToLocal implements SyncSourceToLocalInterface
{
    public function __construct(
        private SourceInterface $source,
        private WpPostArgsFromSchemaObjectInterface $wpPostFactory,
        private InsertPost&DoAction $wpService
    ) {
    }

    /**
     * @inheritDoc
     */
    public function sync(): void
    {
        $posts = array_map([$this, 'getPostArrayToInsert'], $this->source->getObjects());

        foreach ($posts as $post) {
            $preventSync = $post['meta_input']['schemaData']['@preventSync'] ?? false;

            if (!$preventSync) {
                $this->wpService->insertPost($post);
            }
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
