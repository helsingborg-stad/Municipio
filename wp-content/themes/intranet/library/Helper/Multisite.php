<?php

namespace Intranet\Helper;

class Multisite
{
    public static $sitesList = null;

    /**
     * Gets the main site information
     * @return array
     */
    public static function getMainSiteBloginfo()
    {
        global $current_site;

        switch_to_blog($current_site->blog_id);

        $mainSiteInfo = array_filter(get_sites(), function ($site) use ($current_site) {
            return $site->blog_id == $current_site->blog_id;
        });

        if (!is_array($mainSiteInfo) || !isset($mainSiteInfo[0])) {
            return false;
        }

        $mainSiteInfo = $mainSiteInfo[0];
        $mainSiteInfo->name = get_bloginfo();

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
            if (stripos($site->name, $keyword) === false && stripos($site->short_name, $keyword) === false) {
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
    public static function getSitesList($includeMainSite = true, $onlyIds = false)
    {
        // If sites cache is empty go on and get the sites from db
        if (is_null(self::$sitesList)) {
            $sites = array();
            $sites['all'] = get_sites();
            $sites['all_ids'] = array();
            $sites['not_main'] = array();
            $site['not_main_ids'] = array();

            foreach ($sites['all'] as $key => $site) {
                $sites['all_ids'][] = (int) $site->blog_id;
            }

            $sites['not_main'] = array_filter($sites['all'], function ($site) {
                return !is_main_site($site->blog_id);
            });

            $sites['not_main_ids'] = array_filter($sites['not_main'], function ($site) {
                return !is_main_site($site->blog_id);
            });

            self::$sitesList = $sites;
        }

        if ($onlyIds) {
            if (!$includeMainSite) {
                return self::$sitesList['not_main_ids'];
            }

            return self::$sitesList['all_ids'];
        }

        if (!$includeMainSite) {
            return self::$sitesList['not_main'];
        }

        return self::$sitesList['all'];
    }

    /**
     * Get a single site
     * @param  integer $siteId The sites ID
     * @return array           The site info
     */
    public static function getSite($siteId)
    {
        $sites = self::getSitesList();
        $sites = array_filter($sites, function ($item) use ($siteId) {
            return $item->blog_id == $siteId;
        });

        $sites = array_values($sites);

        if (isset($sites[0])) {
            return $sites[0];
        }

        return null;
    }

    public static function getSitesWhere($where = array())
    {
        $sites = self::getSitesList();

        $sites = array_filter($sites, function ($item) use ($where) {
            $return = false;

            foreach ($where as $key => $value) {
                $return = $item->{$key} == $value;
            }

            return $return;
        });

        return $sites;
    }
}
