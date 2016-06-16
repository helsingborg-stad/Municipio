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
    }

    public function colorScheme($classes)
    {
        if (!is_user_logged_in() || empty(get_the_author_meta('user_color_scheme', get_current_user_id()))) {
            return $classes;
        }

        $classes['color_scheme'] = 'theme-' . get_the_author_meta('user_color_scheme', get_current_user_id());
        return $classes;
    }
}
