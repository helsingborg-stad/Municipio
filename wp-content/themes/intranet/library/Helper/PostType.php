<?php

namespace Intranet\Helper;

class PostType
{
    public static function getPublic($filter = null)
    {
        $postTypes = get_post_types(array(
            'public' => true,
            'exclude_from_search' => false
        ));

        // Filters out given post types
        if ($filter) {
            $postTypes = array_filter($postTypes, function ($postType) use ($postTypes) {
                return !in_array($postType, (array) $postTypes);
            });
        }

        if (!is_array($filter) || count($filter) === 0) {
            return array();
        }

        return array_values(array_diff($postTypes, array('nav_menu_item', 'revision', 'hbg-alarm', 'incidents')));
    }
}
