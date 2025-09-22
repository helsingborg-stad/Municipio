<?php

namespace Municipio\Helper;

/**
 * Class WP
 */
class WP
{
    /**
     * Get the joined terms for a post.
     *
     * @param array $taxonomies The taxonomies to retrieve terms from.
     * @param int $postId The ID of the post.
     * @param array $termQueryArgs Additional query arguments for retrieving terms.
     * @return string The joined terms.
     */
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

    /**
     * Get the terms for a post.
     *
     * @param array $taxonomies The taxonomies to retrieve terms from.
     * @param int $postId The ID of the post.
     * @param array $termQueryArgs Additional query arguments for retrieving terms.
     * @return array The terms.
     */
    public static function getPostTerms(array $taxonomies, int $postId = 0, array $termQueryArgs = []): array
    {
        $terms = wp_get_post_terms(
            $postId > 0 ? $postId : get_queried_object_id(),
            $taxonomies,
            $termQueryArgs
        );

        return !empty($terms) && !is_wp_error($terms) ? $terms : [];
    }

    /**
     * Get the terms based on the provided WordPress term query arguments.
     *
     * @param array|null $wpTermQueryArgs The WordPress term query arguments.
     * @return array The terms.
     */
    public static function getTerms(?array $wpTermQueryArgs = null): array
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
    /**
     * Get all custom fields for a given post using ACF get_field()
     *  with a fallback to standard WordPress get_post_meta().
     *
     * @param int $postId The ID of the post to get the custom fields from.
     * @return array An associative array of custom field values indexed by their field names.
     */
    public static function getFields(int $postId = 0)
    {
        $fields = [];
        $meta   = get_post_meta($postId);

        if (!is_array($meta) || empty($meta)) {
            return $fields;
        }

        foreach ($meta as $key => $value) {
            if (!empty($value) && is_array($value)) {
                foreach ($value as $_value) {
                    $fields[$key] = maybe_unserialize($_value);
                }
            } else {
                $fields[$key] = $value;
            }
        }

        return $fields;
    }

    /**
     * Get the meta value for a specific key from a post.
     *
     * @param string $metaKey The meta key to retrieve the value for.
     * @param mixed $defaultValue The default value to return if the meta key is not found.
     * @param int $postId The ID of the post to retrieve the meta value from.
     * @return mixed The meta value.
     */
    public static function getPostMeta(string $metaKey = '', $defaultValue = null, int $postId = 0)
    {
        $postMeta = self::queryPostMeta($postId);

        $isNull        = fn () => !in_array($metaKey, array_keys($postMeta)) || $postMeta[$metaKey] === null;
        $isEmptyString = fn () => is_string($postMeta[$metaKey]) && empty($postMeta[$metaKey]);
        $isEmptyArray  = fn () => is_array($postMeta[$metaKey]) && empty($postMeta[$metaKey]);

        $caseEmptyArray  = fn () => $isEmptyArray() ? $defaultValue : $postMeta[$metaKey];
        $caseEmptyString = fn () => $isEmptyString() ? $defaultValue : $caseEmptyArray();
        $caseNull        = fn () => $isNull() ? $defaultValue : $caseEmptyString();

        return !empty($metaKey) ? $caseNull() : $postMeta;
    }

    /**
     * Query the meta values for a specific post.
     *
     * @param int $postId The ID of the post to query the meta values for.
     * @return array The meta values.
     */
    private static function queryPostMeta(int $postId = 0): array
    {
        $post = $postId > 0 ? $postId : get_queried_object_id();

        $removeNullValues           = fn ($arr) => array_filter($arr, fn ($i) => $i !== null);
        $removeNullVaulesFromArrays = fn ($meta) => is_array($meta) ? $removeNullValues($meta) : $meta;
        $unserializeMetaValue       = fn ($meta) => maybe_unserialize($meta);
        $flattenMetaValue           = fn ($meta) => $meta[0] ?? $meta;

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
    /**
     * Embeds a URL into the content.
     *
     * @param string $url The URL to embed.
     * @return mixed The embedded content.
     */
    public static function embed(string $url = '')
    {
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            return $GLOBALS['wp_embed']->run_shortcode("[embed]{$url}[/embed]");
        }

        return false;
    }

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

        if (ResourceFromApiHelper::isRemotePostID($post)) {
            // Get post by ID using get_posts
            $remotePostFound = fn ($posts) =>
                is_array($posts) &&
                !empty($posts) &&
                is_a($posts[0], 'WP_Post') &&
                $posts[0]->ID === $post;

            $posts = get_posts(array(
                'post__in'         => [$post],
                'posts_per_page'   => 1,
                'suppress_filters' => false, // Important to allow filters to modify the query
            ));

            if ($remotePostFound($posts)) {
                return get_post($posts[0], $output, $filter);
            }
        }

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
        if (ResourceFromApiHelper::isRemotePostID($post)) {
            $post = self::getPost($post);
        }

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
        if (ResourceFromApiHelper::isRemotePostID($post)) {
            $post = self::getPost($post);
        }

        if (get_post_status($post) !== 'publish') {
            return null;
        }

        return get_permalink($post, $leavename);
    }

    /**
     * Get remote attachment id.
     * Necessary for cases where an attachment id exists, but has no obvious connection
     * to the remote resource where it can be found.
     *
     * @param int $id The attachment id.
     * @return int The remote attachment id.
     */
    public static function getRemoteAttachmentId(int $id): int
    {

        if (ResourceFromApiHelper::isRemotePostID($id)) {
            // Already handled.
            return $id;
        }



        return $id;
    }

    /**
     * Get the thumbnail ID of a post.
     *
     * @param int|WP_Post|null $post Optional. Post ID or post object. Defaults to global $post.
     * @return int|false The thumbnail ID of the post, or false if not found.
     */
    public static function getPostThumbnailId($post = null)
    {
        $post = self::getPost($post);

        if (! $post) {
            return false;
        }

        $thumbnail_id = (int) get_post_meta($post->ID, '_thumbnail_id', true);
        return (int) apply_filters('post_thumbnail_id', $thumbnail_id, $post);
    }

    /**
     * Get the URL of the post thumbnail.
     *
     * @param int|WP_Post|null $post Optional. Post ID or post object. Defaults to global $post.
     * @param string $size Optional. Image size. Defaults to 'post-thumbnail'.
     * @return string|false The URL of the post thumbnail, or false if not found.
     */
    public static function getThePostThumbnailUrl($post = null, $size = 'post-thumbnail')
    {
        $postThumbnailId = self::getPostThumbnailId($post);

        if (! $postThumbnailId) {
            return false;
        }

        $thumbnailUrl = wp_get_attachment_image_url($postThumbnailId, $size);

        return apply_filters('post_thumbnail_url', $thumbnailUrl, $post, $size);
    }

    /**
     * Get the image source of an attachment.
     *
     * @param int $attachmentId The attachment ID.
     * @param string $size Optional. Image size. Defaults to 'thumbnail'.
     * @param bool $icon Optional. Whether to include the icon. Defaults to false.
     * @param string $postType Optional. The post type of the attachment.
     * @return array|false The image source of the attachment, or false if not found.
     */
    public static function getAttachmentImageSrc(
        $attachmentId,
        $size = 'thumbnail',
        $icon = false,
        string $postType = ''
    ) {

        if (!empty($postType) && !ResourceFromApiHelper::isRemotePostID($attachmentId)) {
            $attachmentId = ResourceFromApiHelper::getLocalAttachmentIdByPostType($attachmentId, $postType);
        }

        return wp_get_attachment_image_src($attachmentId, $size, $icon);
    }

    /**
     * Get the caption of an attachment.
     *
     * @param int $postId The attachment ID.
     * @param string $postType Optional. The post type of the attachment.
     * @return string The caption of the attachment.
     */
    public static function getAttachmentCaption($postId = 0, string $postType = '')
    {
        if (!empty($postType) && !ResourceFromApiHelper::isRemotePostID($postId)) {
            $postId = ResourceFromApiHelper::getLocalAttachmentIdByPostType($postId, $postType);

            if (ResourceFromApiHelper::isRemotePostID($postId)) {
                $post = self::getPost($postId);

                if (!$post) {
                    return '';
                }

                $caption = $post->post_excerpt;
                return apply_filters('wp_get_attachment_caption', $caption, $post->ID);
            }
        }

        return wp_get_attachment_caption($postId);
    }

    /**
     * Get the current post type.
     *
     * @return string|null The current post type or null if not found.
     */
    public static function getCurrentPostType()
    {
        global $post, $typenow, $current_screen;

        $postType = null;

        // Check the global $typenow - set in admin.php
        if ($typenow) {
            $postType = $typenow;
        }

        // Check the global $post variable - set when editing a post
        if (\is_a($post, 'WP_Post') && !empty($post->post_type)) {
            $postType = $post->post_type;
        }

        // Check the query string - set when creating a new post
        if (isset($_REQUEST['post_type'])) {
            $postType = sanitize_text_field($_REQUEST['post_type']);
        }

        // Lastly check the post_type in the edit page's query string
        if (isset($_REQUEST['post'])) {
            $postId   = intval($_REQUEST['post']);
            $postType = get_post_type($postId);
        }

        // Check the current screen object - set in screen settings
        if (\is_a($current_screen, 'WP_Screen') && !empty($current_screen->post_type)) {
            $postType = $current_screen->post_type;
        }

        return $postType;
    }
}
