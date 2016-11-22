<?php

namespace Intranet\Admin;

class Filters
{
    public function __construct()
    {
        add_action('admin_init', array($this, 'removeNewsFromPageForPostTypes'), 11);
        add_filter('wp_link_query_args', array($this, 'privatePostsInMceLink'));
    }

    /**
     * Removes the selector in "page for post types" for "intranet news" post type
     * @return void
     */
    public function removeNewsFromPageForPostTypes()
    {
        global $wp_settings_fields;

        if (isset($wp_settings_fields['reading']['page_for_post_type']['page_for_intranet-news'])) {
            unset($wp_settings_fields['reading']['page_for_post_type']['page_for_intranet-news']);
        }
    }

    /**
     * Get private posts in the editor link tool
     * @param  array $query Query args
     * @return array
     */
    public function privatePostsInMceLink($args)
    {
        $args['post_status'] = array('publish', 'private');
        return $args;
    }
}
