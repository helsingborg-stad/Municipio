<?php

namespace Municipio\Content;

class Cache
{
    public function __construct()
    {
        add_action('save_post', '\Municipio\Helper\Cache::clearCache', 10, 2);
        add_action('pre_delete_post', '\Municipio\Helper\Cache::clearCache', 10, 2);

        add_action('trashed_post', function ($postId) {
            $post = get_post($postId);
            \Municipio\Helper\Cache::clearCache($postId, $post);
        });
    }
}
