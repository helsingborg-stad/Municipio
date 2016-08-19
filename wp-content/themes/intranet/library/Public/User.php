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
        return network_site_url('subscriptions');
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
