<?php

namespace Intranet\CustomPostType;

class News
{
    public static $postTypeSlug = 'intranet-news';

    public function __construct()
    {
        add_action('init', array($this, 'registerCustomPostType'));
        add_action('post_submitbox_misc_actions', array($this, 'stickyPostCheckbox'));
        add_action('save_post', array($this, 'saveStickyPost'));

        add_action('pre_get_posts', array($this, 'stickySorting'));
        add_filter('posts_orderby', array($this, 'sortStickyPost'), 10, 2);
    }

    /**
     * Add sorting on is_sticky
     * @param  string $orderby Sort query
     * @param  object $query   WP_Query
     * @return string          New sort query
     */
    public function sortStickyPost($orderby, $query)
    {
        if (is_admin() || !isset($query->query_vars['post_type']) || $query->query_vars['post_type'] != self::$postTypeSlug || !$query->is_main_query()) {
            return $orderby;
        }

        global $wpdb;
        $orderby = "mt3.meta_value DESC, " . $orderby;

        return $orderby;
    }

    /**
     * Step one of the sticky sorting
     * @param  object $query  WP_Query
     * @return void
     */
    public function stickySorting($query)
    {
        if (is_admin() || !isset($query->query_vars['post_type']) || $query->query_vars['post_type'] != self::$postTypeSlug || !$query->is_main_query()) {
            return;
        }

        $metaQuery = $query->get('meta_query');
        $metaQuery[] = array(
            'relation' => 'OR',
            array(
                'key' => 'is_sticky',
                'value' => 1,
                'compare' => 'NUMERIC',
            ),
            array(
                'key' => 'is_sticky',
                'compare' => 'NOT EXISTS'
            )
        );

        $query->set('meta_query', $metaQuery);
    }

    /**
     * Adds checkbox to misc publishing actions for set post as sticky
     * @return void
     */
    public function stickyPostCheckbox()
    {
        global $post;

        if ($post->post_type != self::$postTypeSlug) {
            return;
        }

        $checked = checked(true, get_post_meta($post->ID, 'is_sticky', true), false);

        echo '<div class="misc-pub-section">';
        echo '<label for="intranet_news_is_sticky"><input type="checkbox" name="intranet_news_is_sticky" value="true" id="intranet_news_is_sticky" ' . $checked .'> ' . __('Pin to top', 'municipio-intranet') . '</label>';
        echo '</div>';
    }

    /**
     * Saves the "sticky" setting
     * @param  integer $postId The post id
     * @return void
     */
    public function saveStickyPost($postId)
    {
        if (!isset($_POST['post_type']) || $_POST['post_type'] != self::$postTypeSlug) {
            return;
        }

        if (isset($_POST['intranet_news_is_sticky']) && $_POST['intranet_news_is_sticky'] == 'true') {
            update_post_meta($postId, 'is_sticky', true);
        } else {
            delete_post_meta($postId, 'is_sticky');
        }
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

        register_post_type(self::$postTypeSlug, $args);
    }

    /**
     * Get news
     * @param  integer $count Number of posts to get
     * @param  mixed   $site  'all' for all sites, array with blog ids or null for current
     * @return array          News array
     */
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

    /**
     * Combine news from multiple sites into one feed
     * @param  array   $sites Array with blog ids
     * @param  integer $count Number of posts to get
     * @return array          Posts
     */
    public static function getNewsFromSites($sites = array(), $count = 10)
    {
        global $wpdb;

        $news = array();
        $i = 0;
        $sql = null;

        $postStatuses = array('publish');

        if (is_user_logged_in()) {
            $postStatuses[] = 'private';
        }

        // Add quotes to each item
        $postStatuses = array_map(function ($item) {
            return sprintf("'%s'", $item);
        }, $postStatuses);

        // Convert to comma separated string
        $postStatuses = implode(',', $postStatuses);

        foreach ($sites as $site) {
            if ($i > 0) {
                $sql .= " UNION ";
            }

            $postsTable = "{$wpdb->prefix}{$site}_posts";
            $postMetaTable = "{$wpdb->prefix}{$site}_postmeta";
            if ($site == 1) {
                $postsTable = "{$wpdb->prefix}posts";
                $postMetaTable = "{$wpdb->prefix}postmeta";
            }

            $sql .= "(
                SELECT
                    '{$site}' AS blog_id,
                    posts.ID AS post_id,
                    posts.post_date,
                    MAX(CASE WHEN postmeta.meta_key = 'is_sticky' THEN postmeta.meta_value ELSE NULL END) AS is_sticky
                FROM $postsTable posts
                LEFT JOIN $postMetaTable postmeta ON posts.ID = postmeta.post_id
                WHERE
                    posts.post_type = '" . self::$postTypeSlug . "'
                    AND posts.post_status IN ({$postStatuses})
                )";

            $i++;
        }

        $sql .= " ORDER BY is_sticky DESC, post_date DESC LIMIT $count";
        $newsPosts = $wpdb->get_results($sql);

        foreach ($newsPosts as $item) {
            $table = "{$wpdb->base_prefix}postmeta";
            if ($item->blog_id > 1) {
                $table = "{$wpdb->base_prefix}{$item->blog_id}_postmeta";
            }

            $query = "SELECT meta_value FROM {$table} WHERE post_id = {$item->post_id} AND meta_key = '_target_groups' ORDER BY meta_id DESC LIMIT 1";
            $targetGroups = $wpdb->get_var($query);
            $targetGroups = unserialize($targetGroups);

            if (!\Intranet\User\TargetGroups::userInGroup($targetGroups)) {
                continue;
            }

            $news[] = get_blog_post($item->blog_id, $item->post_id);

            end($news);
            $key = key($news);

            $news[$key]->blog_id = $item->blog_id;
            $news[$key]->is_sticky = $item->is_sticky;
        }

        return $news;
    }
}
