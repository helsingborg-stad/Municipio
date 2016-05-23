<?php

namespace Intranet\CustomPostType;

class News
{
    public function __construct()
    {
        add_action('init', array($this, 'registerCustomPostType'));
    }

    /**
     * Registers the custom post type
     * @return void
     */
    public function registerCustomPostType()
    {
        $nameSingular = 'News';
        $namePlural = 'News';

        $icon = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAzMiAzMiI+PGcgZmlsbD0iIzAxMDAwMiI+PHBhdGggZD0iTTI5IDBIN2EzIDMgMCAwIDAtMyAzdjJIM2EzIDMgMCAwIDAtMyAzdjIwYTQgNCAwIDAgMCA0IDRoMjRhNCA0IDAgMCAwIDQtNFYzYTMgMyAwIDAgMC0zLTN6bTEgMjhjMCAxLjEwMi0uODk4IDItMiAySDRjLTEuMTAzIDAtMi0uODk4LTItMlY4YTEgMSAwIDAgMSAxLTFoMXYyMGExIDEgMCAwIDAgMiAwVjNhMSAxIDAgMCAxIDEtMWgyMmExIDEgMCAwIDEgMSAxdjI1eiIvPjxwYXRoIGQ9Ik0xOS40OTggMTMuMDA1aDhhLjUuNSAwIDAgMCAwLTFoLThhLjUuNSAwIDAgMCAwIDF6TTE5LjQ5OCAxMC4wMDVoOGEuNS41IDAgMCAwIDAtMWgtOGEuNS41IDAgMCAwIDAgMXpNMTkuNDk4IDcuMDA1aDhhLjUuNSAwIDAgMCAwLTFoLThhLjUuNSAwIDAgMCAwIDF6TTE2LjUgMjcuMDA0aC04YS41LjUgMCAwIDAgMCAxaDhhLjUuNSAwIDAgMCAwLTF6TTE2LjUgMjQuMDA0aC04YS41LjUgMCAwIDAgMCAxaDhhLjUuNSAwIDAgMCAwLTF6TTE2LjUgMjEuMDA0aC04YS41LjUgMCAwIDAgMCAxaDhhLjUuNSAwIDAgMCAwLTF6TTI3LjUgMjcuMDA0aC04YS41LjUgMCAwIDAgMCAxaDhhLjUuNSAwIDAgMCAwLTF6TTI3LjUgMjQuMDA0aC04YS41LjUgMCAwIDAgMCAxaDhhLjUuNSAwIDAgMCAwLTF6TTI3LjUgMjEuMDA0aC04YS41LjUgMCAwIDAgMCAxaDhhLjUuNSAwIDAgMCAwLTF6TTI3LjUgMTUuMDA0aC0xOWEuNS41IDAgMCAwIDAgMWgxOWEuNS41IDAgMCAwIDAtMXpNMjcuNSAxOC4wMDRoLTE5YS41LjUgMCAwIDAgMCAxaDE5YS41LjUgMCAwIDAgMC0xek05IDEzaDdhMSAxIDAgMCAwIDEtMVY1LjAwNGExIDEgMCAwIDAtMS0xSDlhMSAxIDAgMCAwLTEgMVYxMmExIDEgMCAwIDAgMSAxem0xLTdoNXY1aC01VjZ6Ii8+PC9nPjwvc3ZnPg==';

        $labels = array(
            'name'               => _x($nameSingular, 'post type general name', 'municipio-intranet'),
            'singular_name'      => _x($nameSingular, 'post type singular name', 'municipio-intranet'),
            'menu_name'          => _x($namePlural, 'admin menu', 'municipio-intranet'),
            'name_admin_bar'     => _x($nameSingular, 'add new on admin bar', 'municipio-intranet'),
            'add_new'            => _x('Add New', 'add new button', 'municipio-intranet'),
            'add_new_item'       => sprintf(__('Add new %s', 'municipio-intranet'), $nameSingular),
            'new_item'           => sprintf(__('New %s', 'municipio-intranet'), $nameSingular),
            'edit_item'          => sprintf(__('Edit %s', 'municipio-intranet'), $nameSingular),
            'view_item'          => sprintf(__('View %s', 'municipio-intranet'), $nameSingular),
            'all_items'          => sprintf(__('All %s', 'municipio-intranet'), $namePlural),
            'search_items'       => sprintf(__('Search %s', 'municipio-intranet'), $namePlural),
            'parent_item_colon'  => sprintf(__('Parent %s', 'municipio-intranet'), $namePlural),
            'not_found'          => sprintf(__('No %s', 'municipio-intranet'), $namePlural),
            'not_found_in_trash' => sprintf(__('No %s in trash', 'municipio-intranet'), $namePlural)
        );

        $args = array(
            'labels'               => $labels,
            'description'          => 'News stories',
            'menu_icon'            => $icon,
            'public'               => true,
            'publicly_queriable'   => true,
            'show_ui'              => true,
            'show_in_nav_menus'    => true,
            'menu_position'        => 4,
            'has_archive'          => true,
            'rewrite'              => array(
                'slug'       => __('news', 'municipio-intranet'),
                'with_front' => false
            ),
            'hierarchical'         => false,
            'exclude_from_search'  => false,
            'taxonomies'           => array(),
            'supports'             => array('title', 'revisions', 'editor', 'thumbnail')
        );

        register_post_type('intranet-news', $args);
    }

    public static function getNews($count = 10, $site = null)
    {
        if (is_null($site)) {
            // Current site
            $news = get_posts($args);
        } elseif ($site == 'all') {
            // All sites
            $sites = \Intranet\Helper\Multisite::getSitesList(true, true);
            $news = self::getNewsFromSites($sites, $count);
        } elseif (is_array($site)) {
            // Specific blog ids
            $news = self::getNewsFromSites($site, $count);
        }

        return $news;
    }

    public static function getNewsFromSites($sites = array(), $count = 10)
    {
        global $wpdb;
        $news = array();
        $i = 0;
        $sql = null;

        foreach ($sites as $site) {
            if ($i > 0) {
                $sql .= " UNION ";
            }

            $table = "{$wpdb->prefix}{$site}_posts";
            if ($site == 1) {
                $table = "{$wpdb->prefix}posts";
            }

            $sql .= "(SELECT '{$site}' AS blog_id, ID AS post_id, post_date FROM $table WHERE post_type = 'intranet-news' AND post_status = 'publish')";

            $i++;
        }

        $sql .= " ORDER BY post_date DESC LIMIT $count";
        $newsPosts = $wpdb->get_results($sql);

        foreach ($newsPosts as $item) {
            $news[] = get_blog_post($item->blog_id, $item->post_id);
        }

        return $news;
    }
}
