<?php

namespace Municipio\Helper;

/**
 * Class WP
 */
class WP
{
    /**
     * Get posts based on the provided WP query arguments.
     *
     * @param array|null $wpQueryArgs The WP query arguments.
     * @return array The array of posts.
     */
    public static function getPosts(?array $wpQueryArgs = null): array
    {
        return self::mapPosts(get_posts($wpQueryArgs));
    }

    /**
     * Map the posts array using a set of callbacks.
     *
     * @param array $posts The array of posts to map.
     * @return array The mapped array of posts.
     */
    public static function mapPosts($posts): array
    {
        $reduceToOneCallback = fn ($callbacks) => fn ($item) =>
        array_reduce($callbacks, function ($carry, $closure) {
            return $closure($carry);
        }, $item);

        $mapGeneralPostData = function (object $post): object {
            $post->permalink = WP::getPermalink($post->id);
            $post->thumbnail =
                Post::getFeaturedImage($post->id, [800, 800]);

            return $post;
        };

        return array_map(
            $reduceToOneCallback([
                fn ($p) => (object) $p,
                fn ($p) => !isset($p->postType) ? FormatObject::camelCase((object) $p) : $p,
                fn ($p) => $mapGeneralPostData($p),
                fn ($p) => apply_filters('Municipio/Helper/WP/mapPost', $p, $p->postType),
                fn ($p) => apply_filters("Municipio/Helper/WP/{$p->postType}/mapPost", $p),
            ]),
            $posts
        );
    }

    /**
     * Retrieves post data given a post ID or post object.
     *
     * @param int|WP_Post|null $post Optional. Post ID or post object. `null`, `false`, `0` and other PHP falsey values
     * return the current global post inside the loop. A numerically valid post ID that
     * points to a non-existent post returns `null`. Defaults to global $post.
     * @param string $output Optional. The required return type. One of OBJECT, ARRAY_A, or ARRAY_N, which
     * correspond to a WP_Post object, an associative array, or a numeric array,
     * respectively. Default OBJECT.
     * @param string $filter Optional. Type of filter to apply. Accepts 'raw', 'edit', 'db',
     * or 'display'. Default 'raw'.
     * @return WP_Post|array|null Type corresponding to $output on success or null on failure.
     * When $output is OBJECT, a `WP_Post` instance is returned.
     */
    public static function getPost($post = null, $output = OBJECT, $filter = 'raw')
    {
        return get_post($post, $output, $filter);
    }

    /**
     * Get the title of a post.
     *
     * @param int|WP_Post|null $post Optional. Post ID or post object. Defaults to global $post.
     * @return string The title of the post.
     */
    public static function getTheTitle($post = 0)
    {
        return get_the_title($post);
    }

    /**
     * Get the permalink of a post.
     *
     * @param int|WP_Post|null $post Optional. Post ID or post object. Defaults to global $post.
     * @param bool $leavename Optional. Whether to keep the post name in the permalink. Defaults to false.
     * @return string The permalink of the post.
     */
    public static function getPermalink($post = 0, $leavename = false)
    {
        if (get_post_status($post) !== 'publish') {
            return null;
        }

        return get_permalink($post, $leavename);
    }
}
