<?php

namespace Intranet\Helper;

class PostType
{
    public function __construct()
    {
        add_action('registered_post_type', array($this, 'registeredPostType'), 10, 2);
    }

    public function registeredPostType($postType, $postTypeObject)
    {
        if (!$postTypeObject->public || $postTypeObject->exclude_from_search) {
            return;
        }

        $postTypes = get_site_option('public_post_types', array());
        $postTypes[] = $postType;

        update_site_option('public_post_types', array_unique($postTypes));
    }

    public static function getPublic($filter = null, $useSiteOption = true)
    {
        $postTypes = array();

        if ($useSiteOption && is_multisite()) {
            $postTypes = get_site_option('public_post_types', array());
        } else {
            $args = array(
                'public' => true,
                'exclude_from_search' => false
            );

            $postTypes = get_post_types($args);
        }

        // Filters out given post types
        $filter = array_merge(
            (array) $filter,
            array('nav_menu_item', 'revision', 'hbg-alarm', 'incidents')
        );

        return array_values(array_diff($postTypes, $filter));
    }
}
