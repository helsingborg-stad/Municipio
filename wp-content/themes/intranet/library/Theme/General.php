<?php

namespace Intranet\Theme;

class General
{
    public function __construct()
    {
        add_filter('Municipio/author_display/title', function ($title) {
            return __('Page manager', 'municipio-intranet');
        });

        add_filter('Municipio/author_display/name', function ($name, $userId) {
            if (!is_user_logged_in()) {
                return $name;
            }

            return '<a href="' . municipio_intranet_get_user_profile_url($userId) . '">' . $name . '</a>';
        }, 10, 2);

        add_filter('body_class', array($this, 'colorScheme'), 11);

        // Count pageviews
        add_action('wp', array($this, 'pageViewCounter'));
    }

    /**
     * Get and set color scheme to use
     * @param  array $classes  Body classes
     * @return array           Modified body classes
     */
    public function colorScheme($classes)
    {
        if (!is_user_logged_in() || empty(get_the_author_meta('user_color_scheme', get_current_user_id()))) {
            return $classes;
        }

        $classes['color_scheme'] = 'theme-' . get_the_author_meta('user_color_scheme', get_current_user_id());
        return $classes;
    }

    /**
     * Adds a pageview to the pageview counter
     * @return boolean
     */
    public function pageViewCounter()
    {
        if (!is_single() && !is_page()) {
            return false;
        }

        global $post;

        // Count pageviews
        $pageViews = get_post_meta($post->ID, '_page_views', true);

        if (empty($pageViews)) {
            $pageViews = 0;
        }

        $pageViews++;
        update_post_meta($post->ID, '_page_views', $pageViews);

        if (!is_user_logged_in()) {
            return true;
        }

        // Add user view
        $userViewed = get_post_meta($post->ID, '_user_page_viewed', true);

        if (empty($userViewed) || !is_array($userViewed)) {
            $userViewed = array(
                get_current_user_id() => date('Y-m-d H:i:s')
            );
        }

        $userViewed[get_current_user_id()] = date('Y-m-d H:i:s');
        update_post_meta($post->ID, '_user_page_viewed', $userViewed);

        return true;
    }
}
