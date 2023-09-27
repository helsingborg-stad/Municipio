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
            $post->permalink = get_permalink($post->id);
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
}
