<?php

namespace Intranet\User;

class Login
{
    public function __construct()
    {
        add_action('wp_login', array($this, 'adMapping'), 10, 2);
        add_action('wp_login_failed', array($this, 'frontendLoginFailed'));
        add_action('wp_logout', array($this, 'frontendLogout'));
    }

    public function adMapping($username, $user)
    {
        $userId = $user->data->ID;

        // Map the administration unit
        if (!empty(get_the_author_meta('ad_company', $userId))) {
            $departmentId = \Intranet\User\AdministrationUnits::getAdministrationUnitIdFromString(get_the_author_meta('ad_company', $userId));

            if (!$departmentId) {
                $departmentId = \Intranet\User\AdministrationUnits::insertAdministrationUnit(get_the_author_meta('ad_company', $userId));
            }

            update_user_meta($userId, 'user_administration_unit', $departmentId);
        }

        // Map department
        if (!empty(get_the_author_meta('ad_department', $userId))) {
            update_user_meta($userId, 'user_department', get_the_author_meta('ad_department', $userId));
        }

        // Map phone number
        if (!empty(get_the_author_meta('ad_telephone', $userId))) {
            update_user_meta($userId, 'user_phone', \Intranet\Helper\DataCleaner::phoneNumber(get_the_author_meta('ad_telephone', $userId)));
        }

        return;
    }

    /**
     * Handles logout from frontend
     * @return void
     */
    public function frontendLogout()
    {
        wp_redirect(home_url('/'));
        exit;
    }

    /**
     * Handles incorrect logins on frontend
     * @param  string $username
     * @return void
     */
    public function frontendLoginFailed($username)
    {
        // Where did the submit come from
        $referrer = $_SERVER['HTTP_REFERER'];

        // If there's a valid referrer, and it's not the default log-in screen
        if (!empty($referrer) && !strstr($referrer, 'wp-login') && !strstr($referrer, 'wp-admin')) {
            // let's append some information (login=failed) to the URL for the theme to use
            wp_redirect(strstr($referrer, '?login=failed') ? $referrer : $referrer . '?login=failed');
        }
    }
}
