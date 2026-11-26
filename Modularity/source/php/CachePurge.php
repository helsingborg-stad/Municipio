<?php

declare(strict_types=1);

namespace Modularity;

use Modularity\Helper\FragmentCache\FragmentStoreInterface;
use WpService\Contracts\AddAction;
use WpService\Contracts\GetPosts;
use WpService\Contracts\GetPostType;
use WpService\Contracts\GetThePermalink;
use WpService\Contracts\WpIsPostRevision;
use WpService\Contracts\WpRemoteRequest;

/**
 * Class CachePurge
 *
 * The CachePurge class provides functionality for purging cache related to modularity posts.
 *
 * @package Modularity
 */

class CachePurge
{
    public function __construct(
        private FragmentStoreInterface $fragmentStore,
        private AddAction&WpIsPostRevision&GetPostType&GetPosts&GetThePermalink&WpRemoteRequest $wpService,
    ) {}

    /**
     * Purges the object cache, then the page cache, when a post is saved.
     */
    public function addHooks(): void
    {
        $this->wpService->addAction('save_post', [$this, 'purgeObjectCache'], 90, 1);
        $this->wpService->addAction('save_post', [$this, 'purgePageCache'], 100, 1);
    }

    /**
     * Send a purge request for a post if it's a modularity type.
     *
     * This function checks if a post with the given post ID is a modularity type. If it is, it sends
     * a purge request to the master URL. Purge requests are not sent for post revisions.
     *
     * @param int $post_id The ID of the post to check and potentially send a purge request for.
     * @return bool True if a purge request was sent, false otherwise (including revisions).
     */
    public function purgePageCache($postId): bool
    {
        //Not for revisions
        if ($this->wpService->wpIsPostRevision($postId)) {
            return false;
        }

        //Check if modularity, then send purge!
        if ($this->isModularityPost($postId)) {
            $moduleUsage = \Modularity\ModuleManager::getModuleUsage($postId);

            if (is_array($moduleUsage) && !empty($moduleUsage)) {
                foreach ($moduleUsage as $modulePage) {
                    if (!isset($modulePage->post_id)) {
                        continue;
                    }

                    $this->wpService->wpRemoteRequest((string) $this->wpService->getThePermalink($modulePage->post_id), [
                        'method' => 'PURGE',
                        'timeout' => 2,
                        'redirection' => 0,
                        'blocking' => false,
                    ]);
                }

                return true;
            }
        }

        return false;
    }

    /**
     * Clears the fragment cache affected by saving a post.
     *
     * Clears the post's own fragments: a module post caches its fragments under its own id,
     * and a page caches its blocks under the page id. Then clears the fragments of every
     * Posts module that lists the post's type. Nothing is cleared for revisions.
     *
     * @param int $postId The ID of the saved post.
     * @return bool True if fragments were cleared, false for revisions.
     */
    public function purgeObjectCache($postId): bool
    {
        if ($this->wpService->wpIsPostRevision($postId)) {
            return false;
        }

        $this->fragmentStore->clear($postId);
        $this->clearPostsModulesListing($postId);

        return true;
    }

    /**
     * Clears the fragments of every Posts module that lists the post's type.
     *
     * @param int $postId The ID of the saved post.
     */
    private function clearPostsModulesListing($postId): void
    {
        $postType = $this->wpService->getPostType($postId);

        if ($postType === false || $this->isModularityPostType($postType)) {
            return;
        }

        foreach ($this->getPostsModulesListing($postType) as $postsModule) {
            $this->fragmentStore->clear($postsModule->ID);
        }
    }

    /**
     * @param string $postType
     * @return \WP_Post[] Posts modules that list the post type.
     */
    private function getPostsModulesListing(string $postType): array
    {
        return $this->wpService->getPosts([
            'post_type' => 'mod-posts',
            'posts_per_page' => -1,
            'meta_query' => [
                [
                    'key' => 'posts_data_post_type',
                    'value' => $postType,
                    'compare' => '=',
                ],
            ],
        ]);
    }

    /**
     * Check if a post is of a modularity type.
     *
     * @param int $postId The ID of the post to check.
     * @return bool True if the post is of a modularity type, false otherwise.
     */
    private function isModularityPost($postId): bool
    {
        return $this->isModularityPostType((string) $this->wpService->getPostType($postId));
    }

    /**
     * Modularity types are identified by post types starting with "mod-".
     */
    private function isModularityPostType(string $postType): bool
    {
        return str_starts_with($postType, 'mod-');
    }
}
