<?php

namespace Municipio\ExternalContent\Sync;

use Municipio\ExternalContent\Sources\SourceInterface;
use WpService\Contracts\DeletePost;
use WpService\Contracts\GetPosts;

class PruneAllNoLongerInSource implements SyncSourceToLocalInterface
{
    public function __construct(
        private SourceInterface $source,
        private DeletePost&GetPosts $wpService,
        private SyncSourceToLocalInterface $inner
    ) {
    }

    /**
     * @inheritDoc
     */
    public function sync(): void
    {
        $this->inner->sync();

        $objects       = $this->source->getObjects();
        $idsFromSource = array_map(fn($object) => $object->getProperty('@id'), $objects);
        $posts         = $this->wpService->getPosts([
            'meta_key'     => 'originId',
            'meta_value'   => $idsFromSource,
            'meta_compare' => 'NOT IN',
            'post_type'    => $this->source->getPostType(),
            'numberposts'  => -1
        ]);

        foreach ($posts as $post) {
            $this->wpService->deletePost($post->ID, true);
        }
    }
}
