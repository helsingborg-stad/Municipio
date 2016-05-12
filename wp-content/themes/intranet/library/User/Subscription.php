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
            $force = !!get_blog_option($site['blog_id'], 'intranet_force_subscription');

            if (!$force) {
                unset($sites[$key]);
                continue;
            }

            switch_to_blog($site['blog_id']);
            $sites[$key]['name'] = get_bloginfo();
            restore_current_blog();
        }

        return $sites;
    }

    /**
     * Gets a user's subscriptions
     * @param  integer $userId User id
     * @return array           Subscriptions (blog id:s)
     */
    public static function getSubscriptions($userId = null)
    {
        if (is_null($userId)) {
            $userId = get_current_user_id();
        }

        $sites = wp_get_sites();

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
            $subscriptions[$key]['name'] = get_bloginfo();
            restore_current_blog();
        }

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

        $subscriptions = self::getSubscriptions($userId);
        $matches = array_filter($subscriptions, function ($subscription) use ($blogId) {
            return $subscription['blog_id'] == $blogId;
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
        $subscriptions = self::getSubscriptions($userId);
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
        $subscriptions = self::getSubscriptions($userId);
        $subscriptions = array_filter($subscriptions, function ($subscription) use ($blogId) {
            return $subscription['blog_id'] != $blogId;
        });

        $this->update($userId, $subscriptions);

        return $subscriptions;
    }
}
