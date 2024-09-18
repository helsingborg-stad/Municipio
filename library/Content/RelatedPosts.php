<?php

namespace Municipio\Content;

use Municipio\Helper\WP;

/* It takes a post ID, post type, title and WP_Query arguments and returns an array with the title and
posts */

class RelatedPosts
{
    /**
     * Retrieve related posts for a given post.
     *
     * If no post is given will fallback to current post and post type.
     *
     * @param int       $postId ID of the post for which related posts are to be retrieved
     * @param string    $postType Type of posts to be retrieved
     * @param string    $relatedPostTitle Title to be used for the related posts section. Set to null for none.
     * @param array     $wpQueryArgs Additional arguments to be passed to WP::getPosts
     *
     * @return array An array containing the title of the related posts section and the related posts
     */
    public static function get(
        int $postId = 0,
        string $postType = '',
        string $relatedPostTitle = '',
        array $wpQueryArgs = []
    ): array {
        $post = $postId > 0 ? $postId : get_queried_object_id();

        $type = $postType ?? get_post_type();

        $postTypeObject = get_post_type_object($type ?? null);

        // If $relatedPostTitle is null, do not use the default title.
        if (null === $relatedPostTitle) {
            $title = false;
        } else {
            $title = !empty($relatedPostTitle) ? $relatedPostTitle : sprintf(_x('More %s', 'Default title for related posts, %s being the name of the post type.', 'municipio'), strtolower($postTypeObject->labels->name));
        }

        $posts = WP::getPosts(array_merge([
            'post_type'      => $type,
            'posts_per_page' => 4,
            'exclude'        => array($post),
            'orderby'        => 'rand',
        ], $wpQueryArgs)) ?? [];

        return count($posts) > 0 ? [
            'title' => $title,
            'posts' => $posts
        ] : [];
    }
}
