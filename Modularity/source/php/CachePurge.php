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
    ) {
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
     * Send a purge request for a post if it's a modularity type.
     *
     * This function checks if a post with the given post ID is a modularity type. If it is, it sends
     * a purge request to the fragment cache of every Posts module listing that post type. Purge requests are not sent for post revisions.
     *
     * @param int $post_id The ID of the post to check and potentially send a purge request for.
     * @return bool True if a purge request was sent, false otherwise (including revisions).
     */
    public function purgeObjectCache($postId): bool
    {
        //Not for revisions
        if ($this->wpService->wpIsPostRevision($postId)) {
            return false;
        }

        //Get post type
        $postType = $this->wpService->getPostType($postId);

        if (!$this->isModularityPost($postId) && !empty($postType)) {
            $args = [
                'post_type' => 'mod-posts',
                'posts_per_page' => -1,
                'meta_query' => [
                    [
                        'key' => 'posts_data_post_type',
                        'value' => $postType,
                        'compare' => '=',
                    ],
                ],
            ];

            // Flag to check if we purged any cache
            $purgedPostCache = false;

            // Fetch posts using get_posts
            $posts = $this->wpService->getPosts($args);

            if (!empty($posts)) {
                foreach ($posts as $post) {
                    $purgedPostCache = true;
                    $this->fragmentStore->clear($post->ID);
                }
            }

            return $purgedPostCache;
        }

        return false;
    }

    /**
     * Check if a post is of a modularity type.
     *
     * This function determines if a post, identified by its post ID, belongs to a modularity
     * type. Modularity types are identified by post types starting with "mod-".
     *
     * @param int $postId The ID of the post to check.
     * @return bool True if the post is of a modularity type, false otherwise.
     */
    private function isModularityPost($postId): bool
    {
        return str_starts_with((string) $this->wpService->getPostType($postId), 'mod-');
    }
}
