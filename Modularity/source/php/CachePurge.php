<?php

namespace Modularity;
/**
 * Class CachePurge
 *
 * The CachePurge class provides functionality for purging cache related to modularity posts.
 *
 * @package Modularity
 */

class CachePurge
{
    public $keyGroup = 'modules';

    public function __construct()
    {
        if (function_exists('is_multisite') && is_multisite()) {
            $this->keyGroup = $this->keyGroup . '-' . get_current_blog_id();
        }

        add_action('save_post', array($this, 'purgeObjectCache'), 90, 1);
        add_action('save_post', array($this, 'purgePageCache'), 100, 1);
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
        if (wp_is_post_revision($postId)) {
            return false;
        }

        //Check if modularity, then send purge!
        if ($this->isModularityPost($postId)) {

            $moduleUsage = \Modularity\ModuleManager::getModuleUsage($postId);

            if(is_array($moduleUsage) && !empty($moduleUsage)) {
                foreach($moduleUsage as $modulePage) {
                    if(!isset($modulePage->post_id)) {
                        continue;
                    }

                    wp_remote_request(get_the_permalink($modulePage->post_id), array(
                        'method' => 'PURGE',
                        'timeout' => 2,
                        'redirection' => 0,
                        'blocking' => false
                    ));
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
     * a purge request to the relevant cache group. Purge requests are not sent for post revisions.
     *
     * @param int $post_id The ID of the post to check and potentially send a purge request for.
     * @return bool True if a purge request was sent, false otherwise (including revisions).
     */
    public function purgeObjectCache($postId): bool
    {
        //Not for revisions
        if (wp_is_post_revision($postId)) {
            return false;
        }

        //Get post type
        $postType = get_post_type($postId);

        if (!$this->isModularityPost($postId) && !empty($postType)) {
            $args = [
                'post_type' => 'mod-posts',
                'posts_per_page' => -1,
                'meta_query' => [
                    [
                        'key' => 'posts_data_post_type',
                        'value' => $postType,
                        'compare' => '=',
                    ]
                ]
            ];

            // Flag to check if we purged any cache
            $purgedPostCache = false;
        
            // Fetch posts using get_posts
            $posts = get_posts($args);
            
            if (!empty($posts)) {
                foreach ($posts as $post) {
                    $moduleId = $post->ID;
                    $purgedPostCache = true;
                    wp_cache_delete($moduleId, $this->keyGroup);
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
        if (strpos(get_post_type($postId), "mod-") === 0) {
            return true;
        }
        return false;
    }
}
