<?php

namespace Municipio\Helper;

class PostType
{
    /**
     * Get public post types
     * @param  array  $filter Don't get these
     * @return array
     */
    public static function getPublic($filter = array('page'))
    {
        $postTypes = array();

        foreach (get_post_types() as $key => $postType) {
            $args = get_post_type_object($postType);

            if (!$args->public) {
                continue;
            }

            $postTypes[$postType] = $args;
        }

        if (!empty($filter)) {
            $postTypes = array_filter($postTypes, function ($item) use ($filter) {
                if (substr($item->name, 0, 4) === 'mod-') {
                    return false;
                }

                return !in_array($item->name, $filter);
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
        $postType = !$postType ? self::getPostType() : $postType;
        $postTypeObj = get_post_type_object($postType);

        if ($postTypeObj && !empty($postTypeObj->show_in_rest) && !empty($postTypeObj->rest_base)) {
            $restUrl = esc_url_raw(get_rest_url() . 'wp/v2/' . $postTypeObj->rest_base);
        }

        return $restUrl;
    }

    /**
     * Get post type details

     * @return object  Information about the current posttype
     */
    public static function postTypeDetails()
    {
        $postType = self::getPostType();
        if(!is_null($postType)) {
            $postTypeObject = get_post_type_object($postType);

            if ($postTypeObject instanceof \WP_Post_Type) {
                return (object) \Municipio\Helper\FormatObject::camelCase($postTypeObject);
            }
        }
        return false;
    }

    /**
     * Return current post type

     * @return object  Current post type id
     */
    private static function getPostType()
    {
        if ($postType = get_post_type()) {
            return $postType;
        }

        global $wp_query;
        if (isset($wp_query->query) && isset($wp_query->query['post_type']) && !empty($wp_query->query['post_type'])) {
            return $wp_query->query['post_type'];
        }

        if (isset($wp_query->queried_object) && isset($wp_query->queried_object->post_type) && !empty($wp_query->queried_object->post_type)) {
            return $wp_query->queried_object->post_type;
        }

        return null;
    }
}
