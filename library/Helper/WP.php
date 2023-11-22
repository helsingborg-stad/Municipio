<?php

namespace Municipio\Helper;

class WP
{
    public static function getPostTermsJoined(array $taxonomies, int $postId = 0, array $termQueryArgs = []): string
    {
        $createString = fn ($term) => '<span>' . $term->name . '</span>';
        return array_reduce(
            self::getPostTerms($taxonomies, $postId, $termQueryArgs),
            fn ($accumilator, $term) => empty($accumilator)
                ? $createString($term)
                : $accumilator . ', ' . $createString($term),
            ''
        );
    }

    public static function getPostTerms(array $taxonomies, int $postId = 0, array $termQueryArgs = []): array
    {
        $terms = wp_get_post_terms(
            $postId > 0 ? $postId : get_queried_object_id(),
            $taxonomies,
            $termQueryArgs
        );

        return !empty($terms) && !is_wp_error($terms) ? $terms : [];
    }
    
    public static function getTerms(array $wpTermQueryArgs = null): array
    {
        $terms = get_terms($wpTermQueryArgs);

        return !empty($terms) && !is_wp_error($terms) ? $terms : [];
    }

    /**
     * Get the value of a custom field for a given post.
     *
     * @param string $fieldName The name of the custom field.
     * @param int $postId The ID of the post to get the custom field value from.
     * @param bool $single Whether to return a single value or an array of values.
     * @return mixed The value of the custom field.
     */
    public static function getField(string $fieldName = '', int $postId = 0, bool $single = true)
    {
        if (function_exists('get_field')) {
            $fieldValue = get_field($fieldName, $postId);
        } else {
            $fieldValue = get_post_meta($postId, $fieldName, $single);
        }

        return $fieldValue;
    }

    public static function getPostMeta(string $metaKey = '', $defaultValue = null, int $postId = 0)
    {
        $postMeta = self::queryPostMeta($postId);

        $isNull = fn () => !in_array($metaKey, array_keys($postMeta)) || $postMeta[$metaKey] === null;
        $isEmptyString = fn () => is_string($postMeta[$metaKey]) && empty($postMeta[$metaKey]);
        $isEmptyArray = fn () => is_array($postMeta[$metaKey]) && empty($postMeta[$metaKey]);

        $caseEmptyArray = fn () => $isEmptyArray() ? $defaultValue : $postMeta[$metaKey];
        $caseEmptyString = fn () => $isEmptyString() ? $defaultValue : $caseEmptyArray();
        $caseNull = fn () => $isNull() ? $defaultValue : $caseEmptyString();

        return !empty($metaKey) ? $caseNull() : $postMeta;
    }

    private static function queryPostMeta(int $postId = 0): array
    {
        $post = $postId > 0 ? $postId : get_queried_object_id();

        $removeNullValues = fn ($arr) => array_filter($arr, fn ($i) => $i !== null);
        $removeNullVaulesFromArrays = fn ($meta) => is_array($meta) ? $removeNullValues($meta) : $meta;
        $unserializeMetaValue = fn ($meta) => maybe_unserialize($meta);
        $flattenMetaValue = fn ($meta) => $meta[0] ?? $meta;

        return array_merge(
            array_map(
                $removeNullVaulesFromArrays,
                array_map(
                    $unserializeMetaValue,
                    array_map(
                        $flattenMetaValue,
                        get_post_meta($post) ?? []
                    )
                )
            ),
            []
        );
    }
    public static function embed(string $url = '')
    {
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            return $GLOBALS['wp_embed']->run_shortcode("[embed]{$url}[/embed]");
        }

        return false;
    }
    public static function getPosts(array $wpQueryArgs = null): array
    {
        return self::mapPosts(get_posts($wpQueryArgs));
    }

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
    public static function getPost($post = null, $output = OBJECT, $filter = 'raw') {

        if (RemotePosts::isRemotePostID($post)) {
            // Get post by ID using get_posts
            $remotePostFound = fn ($posts) =>
                is_array($posts) &&
                !empty($posts) &&
                is_a($posts[0], 'WP_Post') &&
                $posts[0]->ID === $post;

            $posts = get_posts(array(
                'post__in' => [$post],
                'posts_per_page' => 1,
                'suppress_filters' => false, // Important to allow filters to modify the query
            ));

            if ($remotePostFound($posts)) {
                return get_post($posts[0], $output, $filter);
            }
        }

        return get_post($post, $output, $filter);
    }

    public static function getTheTitle($post = 0)
    {
        if (RemotePosts::isRemotePostID($post)) {
            $post = self::getPost($post);
        }

        return get_the_title($post);
    }

    public static function getPermalink($post = 0, $leavename = false)
    {
        if (RemotePosts::isRemotePostID($post)) {
            $post = self::getPost($post);
        }

        return get_permalink($post, $leavename);
    }

    /**
     * Get remote attachment id.
     * Necessary for cases where an attachment id exists, but has no obvious connection
     * to the remote resource where it can be found.
     * 
     * @param int $id The attachment id.
     * @param string $postType The post type of the attachment.
     * @return int The remote attachment id.
     */
    public static function getRemoteAttachmentId(int $id, string $postType):int {

        if( RemotePosts::isRemotePostID($id) ) {
            // Already handled.
            return $id;
        }



        return $id;
    }

    public static function getPostThumbnailId($post = null) {
        $post = self::getPost( $post );

        if ( ! $post ) {
            return false;
        }
    
        $thumbnail_id = (int) get_post_meta( $post->ID, '_thumbnail_id', true );
        return (int) apply_filters( 'post_thumbnail_id', $thumbnail_id, $post );
    }

    public static function getThePostThumbnailUrl($post = null, $size = 'post-thumbnail')
    {
        $postThumbnailId = self::getPostThumbnailId( $post );

        if ( ! $postThumbnailId ) {
            return false;
        }
    
        $thumbnailUrl = wp_get_attachment_image_url( $postThumbnailId, $size );
    
        return apply_filters( 'post_thumbnail_url', $thumbnailUrl, $post, $size );
    }

    public static function getAttachmentImageSrc($attachmentId, $size = 'thumbnail', $icon = false, string $postType = '') {

        if (!empty($postType) && !RemotePosts::isRemotePostID($attachmentId)) {
            $attachmentId = RemotePosts::getLocalAttachmentIdByPostType($attachmentId, $postType);
        }

        return wp_get_attachment_image_src($attachmentId, $size, $icon);
    }

    public static function getAttachmentCaption($postId = 0, string $postType = '') {
        if (!empty($postType) && !RemotePosts::isRemotePostID($postId)) {
            $postId = RemotePosts::getLocalAttachmentIdByPostType($postId, $postType);

            if( RemotePosts::isRemotePostID($postId) ) {
                $post = self::getPost($postId);

                if( !$post ) {
                    return '';
                }

                $caption = $post->post_excerpt;
                return apply_filters( 'wp_get_attachment_caption', $caption, $post->ID );
            }
        }

        return wp_get_attachment_caption($postId);
    }
}
