<?php

namespace Intranet\Api;

class Wp
{
    public function __construct()
    {
        add_action('init', array($this, 'enablePostTypes'), 50);
    }

    /**
     * Enable post types in wp endpoint
     * @return void
     */
    public function enablePostTypes()
    {
        global $wp_post_types;

        $postTypes = array('intranet-news');

        if (!is_array($postTypes) || empty($postTypes)) {
            return;
        }

        foreach ($postTypes as $postType) {
            if (!isset($wp_post_types[$postType]) || !is_object($wp_post_types[$postType])) {
                continue;
            }

            $wp_post_types[$postType]->show_in_rest = true;
            $wp_post_types[$postType]->rest_base = $postType;
            $wp_post_types[$postType]->rest_controller_class = 'WP_REST_Posts_Controller';
        }
    }
}
