<?php

namespace Intranet\CustomPostType;

class Incidents
{
    public static $postTypeSlug = 'incidents';

    public function __construct()
    {
        add_action('init', array($this, 'registerCustomPostType'));
        add_filter('posts_results', array($this, 'getIncidentsArchive'), 10, 2);

        add_action('Municipio/blog/post_info', function ($post) {
            if ($post->post_type != 'incidents' || (!get_field('start_date') && !get_field('end_date'))) {
                return;
            }

            $startDate = get_field('start_date') ? date('Y-m-d H:i', strtotime(get_field('start_date'))) : '';
            $endDate = get_field('end_date') ? __('to', 'municipio-intranet') . ' ' . date('Y-m-d H:i', strtotime(get_field('end_date'))) : '';

            echo '
                <li>
                    ' . __('Duration', 'municipio-intranet') . ':
                    ' . $startDate . '
                    ' . $endDate . '
                </li>
            ';
        });
    }

    /**
     * Registers the custom post type
     * @return void
     */
    public function registerCustomPostType()
    {
        $nameSingular = 'Incident';
        $namePlural = 'Incidents';

        $icon = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4KPCEtLSBHZW5lcmF0b3I6IEFkb2JlIElsbHVzdHJhdG9yIDIwLjEuMCwgU1ZHIEV4cG9ydCBQbHVnLUluIC4gU1ZHIFZlcnNpb246IDYuMDAgQnVpbGQgMCkgIC0tPgo8c3ZnIHZlcnNpb249IjEuMSIgaWQ9IkxheWVyXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4IgoJIHZpZXdCb3g9IjAgMCAyNCAyNCIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgMjQgMjQ7IiB4bWw6c3BhY2U9InByZXNlcnZlIj4KPHBhdGggZD0iTTcuMSwxLjRDNy4xLDAuNiw3LjcsMCw4LjUsMGMwLjksMCwxLjYsMC45LDEuNCwxLjhjLTAuMSwwLjMsMCwwLjcsMC4xLDFjMC40LDAuNywxLjEsMS4xLDIsMS4xYzAuOSwwLDEuNi0wLjQsMi0xLjEKCWMwLjItMC4zLDAuMi0wLjcsMC4xLTFDMTMuOSwwLjksMTQuNiwwLDE1LjUsMGMwLjgsMCwxLjQsMC42LDEuNCwxLjRjMCwwLjctMC41LDEuMi0xLjEsMS40Yy0wLjMsMC4xLTAuNiwwLjMtMC44LDAuNQoJYy0wLjYsMSwwLjIsMS45LDAuOSwyLjhDMTQuOCw2LjcsMTMuNCw3LDEyLDdjLTEuNSwwLTIuOC0wLjMtNC0wLjhDOC44LDUuMiw5LjYsNC40LDksMy4zQzguOCwzLDguNSwyLjksOC4yLDIuOAoJQzcuNSwyLjYsNy4xLDIuMSw3LjEsMS40eiBNMjAuNiwxNS41aDIuNGMwLjYsMCwxLjEtMC41LDEuMS0xcy0wLjUtMS0xLjEtMWgtMi40Yy0wLjYsMC0xLjEtMC40LTEuMi0xLjFjLTAuMS0wLjcsMC4zLTEuMSwwLjgtMS4zCglsMi4yLTAuOWMwLjUtMC4yLDAuOC0wLjgsMC42LTEuM2MtMC4yLTAuNS0wLjgtMC44LTEuNC0wLjZsLTIuMywwLjljLTAuMywwLjEtMC43LDAtMC45LTAuNGMtMC4yLTAuNC0wLjQtMC44LTAuNi0xLjIKCUMxNiw4LjUsMTQuMSw5LDEyLDlDOS45LDksOCw4LjUsNi4zLDcuNUM2LjEsNy45LDUuOSw4LjIsNS43LDguNkM1LjUsOS4yLDUuMSw5LjIsNC44LDkuMUwyLjUsOC4yQzEuOSw4LDEuMyw4LjIsMS4xLDguOAoJYy0wLjIsMC41LDAsMS4xLDAuNiwxLjNjMCwwLDAsMCwwLDBMMy45LDExYzAuNSwwLjIsMC45LDAuNywwLjgsMS4zQzQuNiwxMyw0LDEzLjQsMy41LDEzLjRIMS4xYy0wLjYsMC0xLjEsMC41LTEuMSwxczAuNSwxLDEuMSwxCgloMi40YzAuNiwwLDEuMiwwLjQsMS4zLDEuMUM0LjgsMTcuMiw0LjUsMTcuNyw0LDE4TDEuNSwxOUMxLDE5LjMsMC44LDE5LjksMSwyMC40czAuOSwwLjgsMS40LDAuNUw1LDE5LjhjMC4zLTAuMSwwLjctMC4xLDAuOSwwLjMKCWMxLjMsMi4yLDMuNiwzLjcsNi4xLDMuOWMyLjUtMC4zLDQuOC0xLjcsNi4xLTMuOGMwLjMtMC40LDAuNi0wLjUsMS0wLjRsMi42LDEuMWMwLjUsMC4yLDEuMiwwLDEuNC0wLjVjMC4yLTAuNSwwLTEuMS0wLjUtMS40CgljMCwwLDAsMCwwLDBMMjAsMThjLTAuNS0wLjItMC45LTAuNy0wLjgtMS40QzE5LjQsMTUuOCwyMCwxNS41LDIwLjYsMTUuNUwyMC42LDE1LjV6Ii8+Cjwvc3ZnPgo=';

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
            'description'          => 'Incident reporting',
            'menu_icon'            => $icon,
            'public'               => true,
            'publicly_queriable'   => true,
            'show_ui'              => true,
            'show_in_nav_menus'    => true,
            'menu_position'        => 350,
            'has_archive'          => true,
            'rewrite'              => array(
                'slug'       => sanitize_title(__('incidents', 'municipio-intranet')),
                'with_front' => false
            ),
            'hierarchical'         => false,
            'exclude_from_search'  => false,
            'taxonomies'           => array(),
            'supports'             => array('title', 'revisions', 'editor', 'thumbnail', 'author', 'excerpt', 'comments')
        );

        register_post_type(self::$postTypeSlug, $args);
    }

    public function getIncidentsArchive($posts, $query)
    {
        if (!isset($query->query['post_type']) || $query->query['post_type'] !== self::$postTypeSlug) {
            return $posts;
        }

        if (is_single()) {
            foreach ($posts as $post) {
                $post->blog_id = get_current_blog_id();
                $post->incident_level = get_field('level', $post->ID);
            }

            return $posts;
        }

        return self::getIncidents();
    }

    /**
     * Get incidents from sites
     * @param  string|array $sites      "all", "subscriptions", "current" or site id:s
     * @param  string       $level      "info", "warning" or "danger" (low, medium, high)
     * @param  integer      $length     Num incidents to get
     * @return array                    Incidents
     */
    public static function getIncidents($sites = 'all', $level = null, $length = null)
    {
        if ($level == 'all') {
            $level = null;
        }

        switch ($sites) {
            case 'all':
                $sites = \Intranet\Helper\Multisite::getSitesList(true, true);
                break;

            case 'subscriptions':
                $sites = array_merge(
                    \Intranet\User\Subscription::getSubscriptions(get_current_user_id(), true),
                    \Intranet\User\Subscription::getForcedSubscriptions(true)
                );
                break;

            case 'current':
                $sites = (array) get_current_blog_id();
                break;
        }

        $posts = array();

        foreach ($sites as $site) {
            $posts = array_merge($posts, self::getIncidentsFromSite($site));
        }

        uasort($posts, function ($a, $b) {
            return $a->post_date < $b->post_date;
        });

        // Level filter
        if ($level) {
            $posts = array_filter($posts, function ($item) use ($level) {
                return $item->incident_level == $level;
            });
        }

        $posts = array_values($posts);

        // Length filter
        if ($length) {
            $posts = array_slice($posts, 0, $length);
        }

        return $posts;
    }

    /**
     * Gets incidents from a specifc site
     * @param  integer $id Site id
     * @return array       Incidents
     */
    public static function getIncidentsFromSite($id)
    {
        // Post statuses to get
        $postStatuses = array('publish');

        if (is_user_logged_in()) {
            $postStatuses[] = 'private';
        }

        switch_to_blog($id);

        $incidents = get_posts(array(
            'post_type' => self::$postTypeSlug,
            'post_status' => $postStatuses
        ));

        foreach ($incidents as $post) {
            $post->blog_id = get_current_blog_id();
            $post->incident_level = get_field('level', $post->ID);
        }

        restore_current_blog();

        return $incidents;
    }
}
