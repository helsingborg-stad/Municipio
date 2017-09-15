<?php

namespace Intranet\User;

class Registration
{

    public function __construct()
    {
        // Auto subscribe to intranets matching ad_displayname end tag
        add_action('wpmu_new_user', array($this, 'autosubscribe'));
        add_action('wp_login', array($this, 'autosubscribe'));
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
