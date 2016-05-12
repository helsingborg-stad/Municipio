<?php

namespace Intranet\User;

class Subscription
{
    public function __construct()
    {
        add_action('wp_ajax_toggle_subscription', array($this, 'toggleSubscription'));
        add_action('wp_ajax_nopriv_toggle_subscription', array($this, 'toggleSubscription'));
        //$this->unsubscribe(1, 1);
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

        $subscriptions = get_user_meta($userId, 'intranet_subscriptions', true);
        $subscriptions = json_decode($subscriptions);

        if (!is_array($subscriptions)) {
            $subscriptions = array();
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
            return $subscription != $blogId;
        });

        $this->update($userId, $subscriptions);

        return $subscriptions;
    }
}
