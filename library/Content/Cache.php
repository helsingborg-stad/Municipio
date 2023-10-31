<?php

namespace Municipio\Content;

class Cache
{
    public function __construct()
    {
        add_action('save_post', '\Municipio\Helper\Cache::clearCache', 10, 2);

        add_filter('pre_delete_post', function ($delete, $post) {
            \Municipio\Helper\Cache::clearCache($post->ID, $post);
            return $delete;
        }, 10, 2);

        add_action('trashed_post', function ($postId) {
            $post = get_post($postId);
            \Municipio\Helper\Cache::clearCache($postId, $post);
        });

        $this->clearWPOembedCache();
    }

    public function clearWPOembedCache() {
        global $wpdb;
        if (empty(get_option('cleared_wp_oembed_cache'))) {
            $wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE '_oembed_%' AND meta_value LIKE '<iframe%'");
            update_option('cleared_wp_oembed_cache', true);
        }
    }
}
