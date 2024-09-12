<?php

namespace Municipio\ExternalContent\Sync;

use Municipio\ExternalContent\Sources\SourceInterface;
use WpService\Contracts\InsertPost;

class PruneAllNoLongerInSource implements SyncSourceToLocalInterface
{
    public function __construct(
        private SourceInterface $source,
        private InsertPost $wpService,
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
        $posts         = get_posts([
            'meta_key'     => 'originId',
            'meta_value'   => $idsFromSource,
            'meta_compare' => 'NOT IN',
            'post_type'    => $this->source->getPostType(),
            'numberposts'  => -1
        ]);

        foreach ($posts as $post) {
            wp_delete_post($post->ID, true);
        }
    }
}
