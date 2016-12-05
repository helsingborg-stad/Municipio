<?php

namespace Intranet\User;

class Subscription
{
    public static $cacheGroup = 'intranat-subscriptions';

    public static $forcedSubscriptions;
    public static $forcedSubscriptionsIds;
    public static $userSubscription;

    public function __construct()
    {
        add_action('wp_ajax_toggle_subscription', '\Intranet\User\Subscription::toggleSubscription');
        add_action('wp_ajax_nopriv_toggle_subscription', '\Intranet\User\Subscription::toggleSubscription');
    }

    public static function getForcedList()
    {
        $cacheKey = md5(serialize(array('getForcedList')));

        if (self::$forcedSubscriptions || $cacheResult = wp_cache_get($cacheKey, self::$cacheGroup)) {
            if ($cacheResult) {
                return $cacheResult;
            }

            return self::$forcedSubscriptions;
        }

        $sites = get_sites();
        $sites = array_filter($sites, function ($site) {
            return $site->is_forced;
        });

        // Sort alphabetically but always put main site first
        uasort($sites, function ($a, $b) {
            if (is_main_site($a->blog_id)) {
                return -1;
            }

            return $a->name > $b->name;
        });

        self::$forcedSubscriptions = $sites;
        wp_cache_add($cacheKey, self::$forcedSubscriptions, self::$cacheGroup, 3600*24*30);
        return self::$forcedSubscriptions;
    }

    /**
     * Get forced subscriptions
     * @return array Blog id's of forced subscriptions
     */
    public static function getForcedSubscriptions($onlyBlogId = false, $mainBlog = true)
    {
        $sites = self::getForcedList();

        // Remove main blog from array if wanted
        if (!$mainBlog) {
            array_filter($sites, function ($site) {
                return !is_main_site($site->blog_id);
            });
        }

        // Fetch only ids if wanted
        if ($onlyBlogId) {
            $ids = array();

            foreach ($sites as $site) {
                $ids[] = $site->blog_id;
            }

            return $ids;
        }

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
        if (!is_user_logged_in()) {
            return array();
        }

        if (is_null($userId)) {
            $userId = get_current_user_id();
        }

        $cacheKey = md5(serialize(array('getSubscriptions', $userId)));

        // Return if cached
        if (!$onlyBlogId && (self::$userSubscription || $cacheResult = wp_cache_get($cacheKey, self::$cacheGroup))) {
            if ($cacheResult) {
                return $cacheResult;
            }

            return self::$userSubscription;
        }

        $subscriptions = get_user_meta($userId, 'intranet_subscriptions', true);
        $subscriptions = json_decode($subscriptions);

        // Return if only blog ids
        if ($onlyBlogId) {
            return $subscriptions;
        }

        $sites = get_sites();

        if (!is_array($subscriptions)) {
            $subscriptions = array();
        }

        $subscriptions = array_filter($sites, function ($site) use ($subscriptions) {
            return in_array($site->blog_id, $subscriptions);
        });

        uasort($subscriptions, function ($a, $b) {
            return $a->name > $b->name;
        });

        self::$userSubscription = $subscriptions;
        wp_cache_add($cacheKey, self::$userSubscription, self::$cacheGroup, 3600*24*30);
        return self::$userSubscription;
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

        $subscriptions = (array) self::getSubscriptions($userId, true);
        $matches = array_filter($subscriptions, function ($subscription) use ($blogId) {
            return $subscription == $blogId;
        });

        return count($matches) > 0;
    }

    /**
     * Check if a specific blog id is a forced subscription
     * @param  integer  $blogId Blog id to check
     * @return boolean
     */
    public static function isForcedSubscription($blogId)
    {
        return in_array($blogId, self::getForcedSubscriptions(true));
    }

    /**
     * Toggle subscription on/off for the current user
     * @param  integer $blogId Blog id
     * @return string          "subscribed" or "unsibscribed"
     */
    public static function toggleSubscription($blogId = null)
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
            self::unsubscribe($userId, $blogId);

            if (defined('DOING_AJAX') && DOING_AJAX) {
                echo 'unsubscribed';
                wp_die();
            }

            return 'unsubscribed';
        }

        self::subscribe($userId, $blogId);

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
    public static function update($userId, $subscriptions)
    {
        $subscriptions = array_values($subscriptions);
        $subscriptions = array_unique($subscriptions);
        $subscriptions = json_encode($subscriptions);

        // Ban the subscriptions cache
        $cacheKey = md5(serialize(array('getSubscriptions',$userId)));
        wp_cache_delete($cacheKey, self::$cacheGroup);

        return update_user_meta($userId, 'intranet_subscriptions', $subscriptions);
    }

    /**
     * Subscribe to a blog id
     * @param  integer $userId User id
     * @param  integer $blogId Blog id
     * @return array           All subscriptions of the user
     */
    public static function subscribe($userId, $blogId)
    {
        $subscriptions = self::getSubscriptions($userId, true);
        $subscriptions[] = $blogId;

        self::update($userId, $subscriptions);

        return $subscriptions;
    }

    /**
     * Unsubscribe from a blog id
     * @param  integer $userId User id
     * @param  integer $blogId BLog id
     * @return array           All subscriptions of the user
     */
    public static function unsubscribe($userId, $blogId)
    {
        $subscriptions = self::getSubscriptions($userId, true);
        $subscriptions = array_filter($subscriptions, function ($subscription) use ($blogId) {
            return $subscription != $blogId;
        });

        self::update($userId, $subscriptions);

        return $subscriptions;
    }
}
