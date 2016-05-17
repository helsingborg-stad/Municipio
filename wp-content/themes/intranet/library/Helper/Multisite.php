<?php

namespace Intranet\Helper;

class Multisite
{
    /**
     * Gets the main site information
     * @return array
     */
    public static function getMainSiteBloginfo()
    {
        global $current_site;

        switch_to_blog($current_site->blog_id);

        $mainSiteInfo = array_filter(wp_get_sites(), function ($site) use ($current_site) {
            return $site['blog_id'] == $current_site->blog_id;
        });

        if (!is_array($mainSiteInfo) || !isset($mainSiteInfo[0])) {
            return false;
        }

        $mainSiteInfo = $mainSiteInfo[0];
        $mainSiteInfo['name'] = get_bloginfo();

        restore_current_blog();

        return $mainSiteInfo;
    }

    /**
     * Search for sites by name
     * @param  string $keyword The keyword
     * @return array           Matching sites
     */
    public static function searchSites($keyword = null)
    {
        // If this is an ajax request find the keyword in the POST array
        if (defined('DOING_AJAX') && DOING_AJAX && isset($_POST['s']) && !empty($_POST['s'])) {
            $keyword = $_POST['s'];
        }

        $sites = self::getSitesList(true);

        foreach ($sites as $key => $site) {
            if (stripos($site['name'], $keyword) === false && stripos($site['short_name'], $keyword) === false) {
                unset($sites[$key]);
            }
        }

        $sites = array_values($sites);

        // Echo result as json if ajax request, else return array
        if (defined('DOING_AJAX') && DOING_AJAX) {
            echo json_encode($sites);
            wp_die();
        }

        return $sites;

    }

    /**
     * Returns a list of all sites in the network with their basic settings
     * @param  boolean $includeMainSite Wheather to inclide the main page or not
     * @return array                    List of sites in network
     */
    public static function getSitesList($includeMainSite = true)
    {
        $sites = wp_get_sites();
        $subscriptions = \Intranet\User\Subscription::getSubscriptions(null, true);
        $forcedSubscriptions = \Intranet\User\Subscription::getForcedSubscriptions(true);

        $subscriptions = array_merge($subscriptions, $forcedSubscriptions);

        foreach ($sites as $key => $site) {
            if (is_main_site($site['blog_id']) && !$includeMainSite) {
                unset($sites[$key]);
                continue;
            }

            switch_to_blog($site['blog_id']);

            $sites[$key]['name'] = get_bloginfo();
            $sites[$key]['short_name'] = get_blog_option($site['blog_id'], 'intranet_short_name');
            $sites[$key]['description'] = get_bloginfo('description');
            $sites[$key]['subscribed'] = false;
            $sites[$key]['forced_subscription'] = false;

            if (in_array($site['blog_id'], $subscriptions)) {
                $sites[$key]['subscribed'] = true;
            }

            if (in_array($site['blog_id'], $forcedSubscriptions)) {
                $sites[$key]['forced_subscription'] = true;
            }

            restore_current_blog();
        }

        return $sites;
    }
}
