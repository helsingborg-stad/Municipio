<?php

namespace Municipio\SchemaData\ExternalContent\SyncHandler\Cleanup;

use Municipio\SchemaData\ExternalContent\SyncHandler\SyncHandler;
use Municipio\HooksRegistrar\Hookable;
use Municipio\Schema\BaseType;
use WpService\Contracts\{AddAction, GetPosts, WpDeletePost};

/**
 * Class CleanupPostsNoLongerInSource
 *
 * Cleanup posts that are no longer in the source.
 */
class CleanupPostsNoLongerInSource implements Hookable
{
    /**
     * Constructor for the CleanupPostsNoLongerInSource class.
     *
     * @param string $postType
     * @param AddAction&WpDeletePost&GetPosts $wpService
     */
    public function __construct(
        private string $postType,
        private AddAction&WpDeletePost&GetPosts $wpService
    ) {
    }

    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        $this->wpService->addAction(SyncHandler::ACTION_AFTER, [$this, 'cleanup']);
    }

    /**
     * Cleanup posts that are no longer in the source.
     *
     * @param BaseType[] $syncedSchemaObjects
     */
    public function cleanup(array $syncedSchemaObjects): void
    {
        $syncedIds     = array_map(fn($object) => $object->getProperty('@id'), $syncedSchemaObjects);
        $postsToDelete = $this->getPostsNotInSource($syncedIds);

        foreach ($postsToDelete as $post) {
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
