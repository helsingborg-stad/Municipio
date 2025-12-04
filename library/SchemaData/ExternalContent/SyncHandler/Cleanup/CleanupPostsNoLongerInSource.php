<?php

namespace Municipio\SchemaData\ExternalContent\SyncHandler\Cleanup;

use Municipio\Helper\EnsureArrayOf\EnsureArrayOf;
use Municipio\Schema\BaseType;
use WpService\Contracts\{GetPosts, WpDeletePost};

/**
 * Class CleanupPostsNoLongerInSource
 *
 * Cleanup posts that are no longer in the source.
 */
class CleanupPostsNoLongerInSource
{
    /**
     * Constructor for the CleanupPostsNoLongerInSource class.
     *
     * @param string $postType
     * @param WpDeletePost&GetPosts $wpService
     */
    public function __construct(
        private string $postType,
        private WpDeletePost&GetPosts $wpService
    ) {
    }

    /**
     * Cleanup posts that are no longer in the source.
     *
     * @param BaseType[] $syncedSchemaObjects
     */
    public function cleanup(array $syncedSchemaObjects): void
    {
        $syncedSchemaObjects = EnsureArrayOf::ensureArrayOf($syncedSchemaObjects, BaseType::class);

        if (empty($syncedSchemaObjects)) {
            return;
        }

        $syncedIds = array_map(fn($object) => $object->getProperty('@id'), $syncedSchemaObjects);

        foreach ($this->getPostsNotInSource($syncedIds) as $post) {
            $this->wpService->wpDeletePost($post->ID, true);
        }
    }

    /**
     * Get posts that are no longer in the source.
     *
     * @param string[] $syncedIds
     *
     * @return \WP_Post[]
     */
    private function getPostsNotInSource(array $syncedIds): array
    {
        return $this->wpService->getPosts([
            'meta_key'     => 'originId',
            'meta_value'   => $syncedIds,
            'meta_compare' => 'NOT IN',
            'post_type'    => $this->postType,
            'numberposts'  => -1
        ]);
    }
}
