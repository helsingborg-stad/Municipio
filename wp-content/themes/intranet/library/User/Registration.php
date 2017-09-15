<?php

namespace Intranet\User;

class Registration
{

    public function __construct()
    {
        // Do not allow "special accounts"
        add_filter('pre_user_login', array($this, 'disallowedUsers'));

        // Auto subscribe to intranets matching ad_displayname end tag
        add_action('wpmu_new_user', array($this, 'autosubscribe'));
        add_action('wp_login', array($this, 'autosubscribe'));
    }

    /**
     * Disallow username combinations
     * Example: S-accounts
     * @param  string $userLogin    User's username
     * @return void
     */
    public function disallowedUsers($userLogin)
    {
        if (wp_doing_cron()) {
            return $userLogin;
        }

        // If pattern matches it will be considered disallowed
        // pattern => redirect
        $disallowed = apply_filters('MunicipioIntranet/register/disallowed', array(
            '/^s([a-z]{4})([0-9]{4})/i' => network_home_url('?login=saccount'),
            '/^([a-z]{3})([0-9]{4})/i' => network_home_url('?login=faccount'),
            '/^([0-9]{6})([a-z]{2})/i' => network_home_url('?login=schoolaccount')
        ));

        // Loop disallowed patterns, fail-redirect if pattern matches
        foreach ($disallowed as $pattern => $redirectUrl) {
            if (!preg_match($pattern, $userLogin)) {
                continue;
            }

            setcookie('sso_manual_logout', true, time()+3600, '/', COOKIE_DOMAIN);
            wp_redirect($redirectUrl);
            exit;
        }

        // Not disallowed, return userLogin
        return $userLogin;
    }

    /**
     * Autosubscribe to the users main intranet on registration
     * @param  integer $userId User id
     * @return void
     */
    public function autosubscribe($userId)
    {
        if (!is_numeric($userId)) {
            $userId = username_exists($userId);
        }

        if (is_numeric($userId)) {

            $adTag = get_user_meta($userId, 'ad_displayname', true);
            $adTag = explode('-', $adTag);

            if (is_array($adTag) && !empty($adTag)) {
                $adTag = strtolower(trim(end($adTag)));

                $sites = \Intranet\Helper\Multisite::getSitesList();

                foreach ($sites as $key => $site) {
                    if (!$site->autosubscribe_tags) {
                        continue;
                    }

                    $siteTags = explode(',', $site->autosubscribe_tags);

                    $siteTags = array_map(function ($item) {
                        return strtolower(trim($item));
                    }, $siteTags);

                    if (!in_array($adTag, $siteTags)) {
                        continue;
                    }

                    \Intranet\User\Subscription::subscribe($userId, $site->blog_id);
                }
            }
        }
    }
}
