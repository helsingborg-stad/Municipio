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
    }
}
