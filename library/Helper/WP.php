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
}
