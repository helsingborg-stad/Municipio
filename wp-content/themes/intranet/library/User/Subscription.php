<?php

namespace Intranet\User;

class Subscription
{
    public function __construct()
    {
        add_action('wp_ajax_toggle_subscription', '\Intranet\User\Subscription::toggleSubscription');
        add_action('wp_ajax_nopriv_toggle_subscription', '\Intranet\User\Subscription::toggleSubscription');

        add_action('init', array($this, 'addManageSubscriptionsPageRewrite'));
        add_filter('template_include', array($this, 'manageSubscriptionsTemplate'), 10);
    }

    /**
     * Get forced subscriptions
     * @return array Blog id's of forced subscriptions
     */
    public static function getForcedSubscriptions($onlyBlogId = false)
    {
        $sites = get_sites();
        $forcedIds = array();

        foreach ($sites as $key => $site) {
            if (!$site->is_forced) {
                unset($sites[$key]);
                continue;
            }

            $forcedIds[] = $site->blog_id;
        }

        if ($onlyBlogId) {
            return $forcedIds;
        }

        // Sort alphabetically but always put main site first
        uasort($sites, function ($a, $b) {
            if (is_main_site($a->blog_id)) {
                return -1;
            }

            return $a->name > $b->name;
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

        $subscriptionsIds = array();
        $subscriptions = get_user_meta($userId, 'intranet_subscriptions', true);
        $subscriptions = json_decode($subscriptions);

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
        $subscriptions = json_encode($subscriptions);

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

    /**
     * Adds rewrite rules for the manage subscriptions page
     */
    public function addManageSubscriptionsPageRewrite()
    {
        add_rewrite_rule('^subscriptions\/?([a-zA-Z0-9_-]+)?', 'index.php?subscriptions=$matches[1]', 'top');
        add_rewrite_tag('%subscriptions%', '([^&]+)');

        flush_rewrite_rules();
    }

    /**
     * Adds the template for manage subscriptions page
     * @param  string $template Template path before
     * @return string           Template path after
     */
    public function manageSubscriptionsTemplate($template)
    {
        global $wp_query;

        if (!isset($wp_query->query['subscriptions'])) {
            return $template;
        }

        if (!empty($wp_query->query['subscriptions']) && !get_user_by('slug', $wp_query->query['subscriptions'])) {
            $wp_query->set404();
            return get_404_template();
        }

        $template = \Municipio\Helper\Template::locateTemplate('subscriptions');
        return $template;
    }

    /**
     * Only show manage subscription page to logged in users
     * @return void
     */
    public function manageSubscriptionsAccessControl()
    {
        global $wp_query;

        if (!isset($wp_query->query['subscriptions'])) {
            return;
        }

        $currentUser = wp_get_current_user();

        if (!is_super_admin() || (!empty($wp_query->query['subscriptions']) && $currentUser->user_login != $wp_query->query['subscriptions'])) {
            $wp_query->set_404();
        }
    }
}
