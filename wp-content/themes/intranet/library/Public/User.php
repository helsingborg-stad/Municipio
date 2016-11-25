<?php

if (!function_exists('municipio_intranet_get_user_profile_url')) {
    /**
     * Get profile url
     * @param  mixed $user User id or login name, default is current logged in user
     * @return string
     */
    function municipio_intranet_get_user_profile_url($user = null)
    {
        if (is_null($user)) {
            $user = wp_get_current_user();
        } elseif (is_numeric($user)) {
            $user = get_user_by('ID', $user);
        } elseif (is_string($user)) {
            $user = get_user_by('slug', $user);
        }

        if (!is_a($user, 'WP_User')) {
            return null;
        }

        return network_site_url('user/' . $user->data->user_login);
    }
}


if (!function_exists('municipio_intranet_is_author_page')) {
    /**
     * Check if current page is author
     * @return bool
     */
    function municipio_intranet_is_author_page()
    {
        global $wp_query;
        if (isset($wp_query->query['author_name'])) {
            if (!empty($wp_query->query['author_name'])) {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('municipio_intranet_get_user_profile_edit_url')) {
    /**
     * Get edit profile url
     * @param  mixed $user User id or login name, default is current logged in user
     * @return string
     */
    function municipio_intranet_get_user_profile_edit_url($user = null)
    {
        $url = municipio_intranet_get_user_profile_url($user);
        return $url . '/edit';
    }
}

if (!function_exists('municipio_intranet_get_user_manage_subscriptions_url')) {
    /**
     * Get url to manage subscriptions page
     * @param  mixed $user User id or login name, default is current logged in user
     * @return string
     */
    function municipio_intranet_get_user_manage_subscriptions_url()
    {
        return network_site_url('sites');
    }
}

if (!function_exists('municipio_intranet_get_user_full_name')) {
    /**
     * Get url to manage subscriptions page
     * @param  mixed $user User id or login name, default is current logged in user
     * @return string
     */
    function municipio_intranet_get_user_full_name($user = null)
    {
        if (is_null($user)) {
            $user = wp_get_current_user();
        } elseif (is_numeric($user)) {
            $user = get_user_by('ID', $user);
        } elseif (is_string($user)) {
            $user = get_user_by('slug', $user);
        }

        if (!empty(get_user_meta($user->ID, 'first_name', true)) && !empty(get_user_meta($user->ID, 'last_name', true))) {
            return get_user_meta($user->ID, 'first_name', true) . ' ' . get_user_meta($user->ID, 'last_name', true);
        }

        return $user->user_login;
    }
}

if (!function_exists('municipio_intranet_get_first_name')) {
    /**
     * Get url to manage subscriptions page
     * @param  mixed $user User id or login name, default is current logged in user
     * @return string
     */
    function municipio_intranet_get_first_name($user = null)
    {
        if (is_null($user)) {
            $user = wp_get_current_user();
        } elseif (is_numeric($user)) {
            $user = get_user_by('ID', $user);
        } elseif (is_string($user)) {
            $user = get_user_by('slug', $user);
        }

        if (!empty(get_user_meta($user->ID, 'first_name', true))) {
            return get_user_meta($user->ID, 'first_name', true);
        }

        return $user->user_login;
    }
}

if (!function_exists('municipio_intranet_get_administration_unit_name')) {
    /**
     * Get url to manage subscriptions page
     * @param  mixed $user User id or login name, default is current logged in user
     * @return string
     */
    function municipio_intranet_get_administration_unit_name($id)
    {
        return \Intranet\User\AdministrationUnits::getAdministrationUnit($id);
    }
}

if (!function_exists('municipio_intranet_user_has_birthday')) {
    /**
     * Check if user has birthday today
     * @param  mixed $user User id or login name, default is current logged in user
     * @return string
     */
    function municipio_intranet_user_has_birthday($user = null)
    {
        if (is_null($user)) {
            $user = wp_get_current_user();
        } elseif (is_numeric($user)) {
            $user = get_user_by('ID', $user);
        } elseif (is_string($user)) {
            $user = get_user_by('slug', $user);
        }

        if (get_the_author_meta('user_hide_birthday') == 1) {
            return false;
        }

        $birthday = false;
        if (!get_the_author_meta('user_birthday') || !get_the_author_meta('user_birthday')['year'] || !get_the_author_meta('user_birthday')['month'] || !get_the_author_meta('user_birthday')['day']) {
            return false;
        }

        if (get_the_author_meta('user_birthday')['month'] == date('m') && get_the_author_meta('user_birthday')['day'] == date('d')) {
            return true;
        }

        return false;
    }
}

if (!function_exists('municipio_intranet_user_birthday')) {
    /**
     * Check if user has birthday today
     * @param  mixed $user User id or login name, default is current logged in user
     * @return string
     */
    function municipio_intranet_user_birthday($user = null)
    {
        if (is_null($user)) {
            $user = wp_get_current_user();
        } elseif (is_numeric($user)) {
            $user = get_user_by('ID', $user);
        } elseif (is_string($user)) {
            $user = get_user_by('slug', $user);
        }

        if (get_the_author_meta('user_hide_birthday') == 1) {
            return false;
        }

        if (!get_the_author_meta('user_birthday') || !get_the_author_meta('user_birthday')['year'] || !get_the_author_meta('user_birthday')['month'] || !get_the_author_meta('user_birthday')['day']) {
            return false;
        }

        $bday = get_the_author_meta('user_birthday')['year'] . '-' . get_the_author_meta('user_birthday')['month'] . '-' . get_the_author_meta('user_birthday')['day'];

        return mysql2date('j F', $bday);
    }
}
