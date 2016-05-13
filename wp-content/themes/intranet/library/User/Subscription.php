<?php

namespace Intranet\User;

class Subscription
{
    public function __construct()
    {
        add_action('wp_ajax_toggle_subscription', array($this, 'toggleSubscription'));
        add_action('wp_ajax_nopriv_toggle_subscription', array($this, 'toggleSubscription'));
    }

    /**
     * Get forced subscriptions
     * @return array Blog id's of forced subscriptions
     */
    public static function getForcedSubscriptions()
    {
        $sites = wp_get_sites();

        foreach ($sites as $key => $site) {
            $force = get_blog_option($site['blog_id'], 'intranet_force_subscription');

            if (!$force || $force == 'false') {
                unset($sites[$key]);
                continue;
            }

            switch_to_blog($site['blog_id']);
            $sites[$key]['name'] = get_bloginfo();
            $sites[$key]['short_name'] = get_blog_option($site['blog_id'], 'intranet_short_name');
            restore_current_blog();
        }

        // Sort alphabetically but always put main site first
        uasort($sites, function ($a, $b) {
            if (is_main_site($a['blog_id'])) {
                return -1;
            }

            return $a['name'] > $b['name'];
        });

        return $sites;
    }

    /**
     * Gets a user's subscriptions
     * @param  integer $userId      User id
     * @param  boolean $onlyBlogId  True to only return blog ids
     * @return array                Subscriptions (blog id:s)
     */
    public static function getSubscriptions($userId = null, $onlyBlogId = false)
    {
        if (is_null($userId)) {
            $userId = get_current_user_id();
        }

        $sites = wp_get_sites();

        $subscriptionsIds = array();
        $subscriptions = get_user_meta($userId, 'intranet_subscriptions', true);
        $subscriptions = json_decode($subscriptions);

        if (!is_array($subscriptions)) {
            $subscriptions = array();
        }

        $subscriptions = array_filter($sites, function ($site) use ($subscriptions) {
            return in_array($site['blog_id'], $subscriptions);
        });

        foreach ($subscriptions as $key => $site) {
            switch_to_blog($site['blog_id']);

            $subscriptionsIds[] = $site['blog_id'];
            $subscriptions[$key]['name'] = get_bloginfo();
            $subscriptions[$key]['short_name'] = get_blog_option($site['blog_id'], 'intranet_short_name');

            restore_current_blog();
        }

        if ($onlyBlogId) {
            return $subscriptionsIds;
        }

        uasort($subscriptions, function ($a, $b) {
            return $a['name'] > $b['name'];
        });

        return $subscriptions;
    }

    /**
     * Checks if a user has subscribed to a blog id
     * @param  integer  $userId User id
     * @param  integer  $blogId Blog id
     * @return boolean
     */
    public static function hasSubscribed($blogId, $userId = null)
    {
        if (is_null($userId)) {
            $userId = get_current_user_id();
        }

        $subscriptions = self::getSubscriptions($userId, true);
        $matches = array_filter($subscriptions, function ($subscription) use ($blogId) {
            return $subscription == $blogId;
        });

        return count($matches) > 0;
    }

    /**
     * Toggle subscription on/off for the current user
     * @param  integer $blogId Blog id
     * @return string          "subscribed" or "unsibscribed"
     */
    public function toggleSubscription($blogId = null)
    {
        $userId = get_current_user_id();

        // Get blogid from ajax post
        if (defined('DOING_AJAX') && DOING_AJAX && \Municipio\Helper\Input::hasValue($_POST['blog_id'])) {
            $blogId = $_POST['blog_id'];
        }

        // Check so that both userid and blogid has values
        if (!$userId || !$blogId) {
            if (defined('DOING_AJAX') && DOING_AJAX) {
                echo 'false';
                wp_die();
            }

            return false;
        }

        // Check if blogid is subscribed
        if (self::hasSubscribed($blogId)) {
            $this->unsubscribe($userId, $blogId);

            if (defined('DOING_AJAX') && DOING_AJAX) {
                echo 'unsubscribed';
                wp_die();
            }

            return 'unsubscribed';
        }

        $this->subscribe($userId, $blogId);

        if (defined('DOING_AJAX') && DOING_AJAX) {
            echo 'subscribed';
            wp_die();
        }

        return 'subscribed';
    }

    /**
     * Update a user's subscriptions
     * Attention: Do not use this directly, please use the "subscribe" function
     * @param  integer $userId        User id
     * @param  array   $subscriptions Array with blog id's
     * @return boolean
     */
    private function update($userId, $subscriptions)
    {
        $subscriptions = array_values($subscriptions);
        $subscriptions = json_encode($subscriptions);
        return update_user_meta($userId, 'intranet_subscriptions', $subscriptions);
    }

    /**
     * Subscribe to a blog id
     * @param  integer $userId User id
     * @param  integer $blogId Blog id
     * @return array           All subscriptions of the user
     */
    public function subscribe($userId, $blogId)
    {
        $subscriptions = self::getSubscriptions($userId, true);
        $subscriptions[] = $blogId;

        $this->update($userId, $subscriptions);

        return $subscriptions;
    }

    /**
     * Unsubscribe from a blog id
     * @param  integer $userId User id
     * @param  integer $blogId BLog id
     * @return array           All subscriptions of the user
     */
    public function unsubscribe($userId, $blogId)
    {
        $subscriptions = self::getSubscriptions($userId, true);
        $subscriptions = array_filter($subscriptions, function ($subscription) use ($blogId) {
            return $subscription != $blogId;
        });

        $this->update($userId, $subscriptions);

        return $subscriptions;
    }
}
