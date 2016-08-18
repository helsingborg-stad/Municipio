<?php

namespace Intranet\CustomPostType;

class News
{
    public static $postTypeSlug = 'intranet-news';

    public function __construct()
    {
        add_action('init', array($this, 'registerCustomPostType'));
        add_action('post_submitbox_misc_actions', array($this, 'stickyPostCheckbox'));
        add_action('post_submitbox_misc_actions', array($this, 'excludeFromMainBlogCheckbox'));
        add_action('save_post', array($this, 'saveStickyPost'));
        add_action('save_post', array($this, 'saveExcludeFromMainBlog'));

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
     * Checkbox for "push post to main blog"
     * @return [type] [description]
     */
    public function excludeFromMainBlogCheckbox()
    {
        global $post;

        if ($post->post_type != self::$postTypeSlug || !\Intranet\User\Subscription::isForcedSubscription(get_current_blog_id())) {
            return;
        }

        $current = get_post_meta($post->ID, 'intranet_news_exclude_from_main_site', true);
        $checked = checked(true, $current, false);

        echo '<div class="misc-pub-section">';
        echo '<label for="intranet_news_exclude_from_main_site"><input type="checkbox" name="intranet_news_exclude_from_main_site" value="true" id="intranet_news_exclude_from_main_site" ' . $checked .'> ' . __('Exclude from main news feed', 'municipio-intranet') . '</label>';
        echo '</div>';
    }

    /**
     * Saves the "sticky" setting
     * @param  integer $postId The post id
     * @return void
     */
    public function saveExcludeFromMainBlog($postId)
    {
        if (!isset($_POST['post_type']) || $_POST['post_type'] != self::$postTypeSlug) {
            return;
        }

        if (isset($_POST['intranet_news_exclude_from_main_site']) && $_POST['intranet_news_exclude_from_main_site'] == 'true') {
            update_post_meta($postId, 'intranet_news_exclude_from_main_site', true);
        } else {
            delete_post_meta($postId, 'intranet_news_exclude_from_main_site');
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

        $icon = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyOTcgMjk3IiB3aWR0aD0iNTEyIiBoZWlnaHQ9IjUxMiI+PGcgZmlsbD0iI0ZGRiI+PHBhdGggZD0iTTE3My44NTggMTA0LjYyNmg3MC40MXY1Mi40MzJoLTcwLjQxeiIvPjxwYXRoIGQ9Ik00NC42NzcgMjYyLjQzaDI0Mi4yNTZjNS41NiAwIDEwLjA2Ny00LjUxIDEwLjA2Ny0xMC4wN1Y0NC42NGMwLTUuNTYtNC41MDgtMTAuMDY4LTEwLjA2Ny0xMC4wNjhINDQuNjc3Yy01LjU2IDAtMTAuMDY3IDQuNTA4LTEwLjA2NyAxMC4wNjh2MjA3LjcyYzAgNS41NiA0LjUwNyAxMC4wNyAxMC4wNjcgMTAuMDd6TTE1Ny43NSA5Ni41N2E4LjA1NSA4LjA1NSAwIDAgMSA4LjA1NC04LjA1NWg4Ni41MmE4LjA1NSA4LjA1NSAwIDAgMSA4LjA1NSA4LjA1NXY2OC41NGE4LjA1NSA4LjA1NSAwIDAgMS04LjA1OCA4LjA1NGgtODYuNTJhOC4wNTUgOC4wNTUgMCAwIDEtOC4wNTQtOC4wNTRWOTYuNTd6bS03OC40NjYtOC4wNTRoNTEuOTEzYTguMDU1IDguMDU1IDAgMCAxIDAgMTYuMTFINzkuMjg0YTguMDU0IDguMDU0IDAgMSAxIDAtMTYuMTF6bTAgMzQuNjJoNTEuOTEzYTguMDU1IDguMDU1IDAgMCAxIDAgMTYuMTFINzkuMjg0YTguMDU1IDguMDU1IDAgMSAxIDAtMTYuMTF6bTAgMzQuNjE2aDUxLjkxM2E4LjA1NSA4LjA1NSAwIDAgMSAwIDE2LjExSDc5LjI4NGE4LjA1NSA4LjA1NSAwIDEgMSAwLTE2LjExem0wIDUxLjkzMmgxNzMuMDRhOC4wNTYgOC4wNTYgMCAwIDEgMCAxNi4xMUg3OS4yODRhOC4wNTUgOC4wNTUgMCAwIDEgMC0xNi4xMXpNMTguNSAyNTIuMzZWNjkuMTkyaC04LjQzM0M0LjUwNyA2OS4xOTIgMCA3My43IDAgNzkuMjYydjE3My4xYzAgNS41NiA0LjUwOCAxMC4wNjcgMTAuMDY3IDEwLjA2N2gxMC40NWEyNi4wNTUgMjYuMDU1IDAgMCAxLTIuMDE2LTEwLjA3eiIvPjwvZz48L3N2Zz4=';

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
            'supports'             => array('title', 'revisions', 'editor', 'thumbnail', 'author')
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
            $postStatuses = array('publish');

            if (is_user_logged_in()) {
                $postStatuses[] = 'private';
            }

            $news = get_posts(array(
                'post_type' => 'intranet-news',
                'post_status' => $postStatuses
            ));
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

            $postsTable = "{$wpdb->base_prefix}{$site}_posts";
            $postMetaTable = "{$wpdb->base_prefix}{$site}_postmeta";

            if ($site == 1) {
                $postsTable = "{$wpdb->base_prefix}posts";
                $postMetaTable = "{$wpdb->base_prefix}postmeta";
            }

            $sql .= "(
                SELECT DISTINCT
                    '{$site}' AS blog_id,
                    posts.ID AS post_id,
                    posts.post_date,
                    CASE WHEN postmeta1.meta_value THEN postmeta1.meta_value ELSE 0 END AS is_sticky,
                    CASE WHEN postmeta2.meta_value THEN postmeta2.meta_value ELSE 0 END AS page_views,
                    postmeta3.meta_value AS user_views
                FROM $postsTable posts
                LEFT JOIN $postMetaTable postmeta1 ON posts.ID = postmeta1.post_id AND postmeta1.meta_key = 'is_sticky'
                LEFT JOIN $postMetaTable postmeta2 ON posts.ID = postmeta2.post_id AND postmeta2.meta_key = '_page_views'
                LEFT JOIN $postMetaTable postmeta3 ON posts.ID = postmeta3.post_id AND postmeta3.meta_key = '_user_page_viewed'
                WHERE
                    posts.post_type = '" . self::$postTypeSlug . "'
                    AND posts.post_status IN ({$postStatuses})
                )";

            $i++;
        }

        $sql .= " ORDER BY is_sticky DESC, post_date DESC LIMIT $count";
        $newsPosts = $wpdb->get_results($sql);

        $newsPosts = array_filter($newsPosts, function ($item) {
            return !is_null($item->post_id);
        });

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

            if (is_main_site()) {
                $query = "SELECT meta_value FROM {$table} WHERE post_id = {$item->post_id} AND meta_key = 'intranet_news_exclude_from_main_site' ORDER BY meta_id DESC LIMIT 1";
                $excludeFromMain = (boolean) $wpdb->get_var($query);

                if ($excludeFromMain) {
                    unset($news[$key]);
                    continue;
                }
            }

            $news[$key]->blog_id = $item->blog_id;
            $news[$key]->is_sticky = $item->is_sticky;
            $news[$key]->page_views = (int) $item->page_views;
            $news[$key]->user_views = is_serialized($item->user_views) ? count(unserialize($item->user_views)) : 0;
            $news[$key]->rank = \Intranet\Helper\PostRank::rank($news[$key]);
        }

        // Sort on rank
        uasort($news, function ($a, $b) {
            return $a->rank < $b->rank;
        });

        $rankTotal = 0;
        foreach ($news as $key => $post) {
            $rankTotal += $post->rank;
        }

        foreach ($news as $key => $post) {
            $news[$key]->rank_percent = ($post->rank / $rankTotal) * 100;
        }

        return $news;
    }
}
