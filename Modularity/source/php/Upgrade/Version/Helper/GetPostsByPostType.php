<?php

namespace Modularity\Upgrade\Version\Helper;

class GetPostsByPostType {
    public static function getPostsByPostType(string $postType) {
        $args = array(
            'post_type' => $postType,
            'post_status' => ['publish', 'draft', 'pending', 'private'],
            'numberposts' => -1
        );
        
        $posts = get_posts($args);

        return $posts;
    }
}