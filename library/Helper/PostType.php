<?php

namespace Municipio\Helper;

class PostType
{
    /**
     * Get public post types
     * @param  array  $filter Don't get these
     * @return array
     */
    public static function getPublic($filter = array())
    {
        $postTypes = array();

        foreach (get_post_types() as $key => $postType) {
            $args = get_post_type_object($postType);

            if (!$args->public || $args->name === 'page') {
                continue;
            }

            $postTypes[$postType] = $args;
        }

        if (!empty($filter)) {
            $postTypes = array_filter($postTypes, function ($item) use ($filter) {
                if (substr($item, 0, 4) === 'mod-') {
                    return false;
                }

                return !in_array($item, $filter);
            });
        }

        return $postTypes;
    }

    /**
     * Get post type REST URL
     * @param  string|null $postType Post type slug
     * @return string                Post types REST URL
     */
    public static function postTypeRestUrl($postType = null)
    {
        $restUrl = null;
        $postType = !$postType ? get_post_type() : $postType;
        $postTypeObj = get_post_type_object($postType);

        if ($postTypeObj && !empty($postTypeObj->show_in_rest) && !empty($postTypeObj->rest_base)) {
            $restUrl = esc_url_raw(get_rest_url() . 'wp/v2/' . $postTypeObj->rest_base);
        }

        return $restUrl;
    }
}
