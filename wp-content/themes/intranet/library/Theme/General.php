<?php

namespace Intranet\Theme;

class General
{

    public static $siteOptions;

    public function __construct()
    {
        add_action('init', function () {
            register_taxonomy_for_object_type('post_tag', 'page');
        });

        add_filter('Municipio/author_display/title', function ($title) {
            return __('Page manager', 'municipio-intranet');
        });

        add_filter('Municipio/author_display/name', function ($name, $userId) {
            if (!is_user_logged_in()) {
                return $name;
            }

            return '<a href="' . municipio_intranet_get_user_profile_url($userId) . '">' . $name . '</a>';
        }, 10, 2);

        add_filter('Municipio/theme/key', array($this, 'colorScheme'));

        // Count pageviews
        //add_action('wp', array($this, 'pageViewCounter'));

        // Get additional site options
        add_filter('the_sites', array($this, 'getSiteOptions'));

        add_filter('Municipio/favicons', array($this, 'favicons'));

        add_filter('Modularity/CoreTemplatesSearchTemplates', function ($templates) {
            return array_merge($templates, array(
                'author-edit',
                'table-of-contents'
            ));
        });

        add_action('widgets_init', function () {
            unregister_sidebar('slider-area');
        }, 15);

        add_filter('ReadSpeakerHelper\multisite_load', '__return_true');
        add_filter('Municipio/GoogleAnalytics/ua', array($this, 'googleAnalyticsUA'));

        add_filter('Municipio/Page/MainGrid/Classes', function ($classes) {
            return array();
        });

        add_filter('ep_analyzer_language', function () {
            return 'Swedish';
        });

        // Force hide share buttons
        add_filter('acf/load_value/name=page_show_share', function ($value) {
            return false;
        });
    }

    public function googleAnalyticsUA($ua)
    {
        if (!is_main_site() && (!$ua || empty($ua))) {
            return get_blog_option(SITE_ID_CURRENT_SITE, 'options_google_analytics_ua');
        }

        return $ua;
    }

    public function favicons($icons)
    {
        if (function_exists('switch_to_blog')) {
            switch_to_blog(BLOG_ID_CURRENT_SITE);
        }

        $icons = get_field('favicons', 'option');

        if (function_exists('switch_to_blog')) {
            restore_current_blog();
        }

        return $icons;
    }

    public static function emptySiteOptionsCache()
    {
        wp_cache_delete('intranet-site-options');
    }

    /**
     * Get additional options for sites on get_sites()
     * @param  array $sites Sites
     * @return array        Sites
     */
    public function getSiteOptions($sites)
    {
        if (isset(debug_backtrace()[5]) && strpos(debug_backtrace()[5]['file'], 'user.php') > -1) {
            return $sites;
        }

        if (!is_null(self::$siteOptions)) {
            return self::$siteOptions;
        } elseif ($cached_sites = wp_cache_get('intranet-site-options')) {
            $sites = $cached_sites;
        } else {
            foreach ($sites as $key => $site) {
                //Do not switch inside get_blog_option!
                switch_to_blog($site->blog_id);

                $site->name = get_blog_option($site->blog_id, 'blogname');
                $site->description = get_blog_option($site->blog_id, 'blogdescription');
                $site->short_name = get_blog_option($site->blog_id, 'intranet_short_name');
                $site->is_forced = get_blog_option($site->blog_id, 'intranet_force_subscription') === 'true';
                $site->is_hidden = (boolean) get_blog_option($site->blog_id, 'intranet_site_hidden');
                $site->is_administration_unit = get_blog_option($site->blog_id, 'intranet_administration_unit_network') === 'true';
                $site->subscribed = false;
                $site->autosubscribe_tags = get_blog_option($site->blog_id, 'intranet_ad_autosubscribe');

                restore_current_blog();
            }

            wp_cache_set('intranet-site-options', $sites, '', 600);
        }

        $subscriptions = (array) \Intranet\User\Subscription::getSubscriptions(null, true);

        foreach ($sites as $key => $site) {
            if ($site->is_forced || in_array($site->blog_id, $subscriptions)) {
                $site->subscribed = true;
            }

            if ($site->is_hidden) {
                switch_to_blog($site->blog_id);
                if (!current_user_can('administrator') && !current_user_can('editor')) {
                    unset($sites[$key]);
                }
                restore_current_blog();
            }
        }

        self::$siteOptions = $sites;

        return $sites;
    }

    /**
     * Get and set color scheme to use
     * @param  array $classes  Body classes
     * @return array           Modified body classes
     */
    public function colorScheme($key)
    {
        if ((defined('MUNICIPIO_INTRANET_USER_COLOR_THEME') && !MUNICIPIO_INTRANET_USER_COLOR_THEME)) {
            return $key;
        }
        return 'purple';
    }

    /**
     * Adds a pageview to the pageview counter
     * @return boolean
     */
    public function pageViewCounter()
    {
        if (!is_single() && !is_page()) {
            return false;
        }

        global $post;

        // Count pageviews
        $pageViews = get_post_meta($post->ID, '_page_views', true);

        if (empty($pageViews)) {
            $pageViews = 0;
        }

        $pageViews++;
        update_post_meta($post->ID, '_page_views', $pageViews);

        if (!is_user_logged_in()) {
            return true;
        }

        // Add user view
        $userViewed = get_post_meta($post->ID, '_user_page_viewed', true);

        if (empty($userViewed) || !is_array($userViewed)) {
            $userViewed = array(
                get_current_user_id() => date('Y-m-d H:i:s')
            );
        }

        $userViewed[get_current_user_id()] = date('Y-m-d H:i:s');
        update_post_meta($post->ID, '_user_page_viewed', $userViewed);

        return true;
    }
}
