<?php

namespace Municipio\Theme;

class Archive
{
    public function __construct()
    {
        add_filter('wp_title', array($this, 'pageTitle'));
        add_filter('get_the_archive_title', array($this, 'pageHeader'));
        add_action('pre_get_posts', array($this, 'onlyFirstLevel'));
        add_action('pre_get_posts', array($this, 'enablePageForPostTypeChildren'));
        add_action('pre_get_posts', array($this, 'filterNumberOfPostsInArchive'), 20, 1);
    }

    /*
    * Filter number of posts that should be displayed in archive list
    * @param $WP_Query The query to show current archive page return
    * @return bool True or false depending on if the query has been altered or not.
    */
    public function filterNumberOfPostsInArchive($query) : bool
    {
        if (!is_admin() && $query->is_main_query()) {

            //Check that posttype is valid
            if (!isset($query->query["post_type"])) {
                return false;
            }

            //Get current post count
            $postCount = get_field('archive_' . $query->query["post_type"] . '_number_of_posts', 'option');

            //If not set, use default value
            if (isset($postCount) && !empty($postCount) && is_numeric($postCount)) {
                $query->set('posts_per_page', $postCount);
                return true;
            }
        }

        return false;
    }

    /**
     * Filter away "Archive:" etc from pageHeader
     * @param  string $title
     * @return string
     */
    public function pageHeader($title)
    {
        if (is_category()) {
            return single_cat_title('', false);
        } elseif (is_tag()) {
            return single_cat_title('', false);
        } elseif (is_author()) {
            $title = '<span class="vcard">' . get_the_author() . '</span>';
        } elseif (is_year()) {
            return get_the_date(_x('Y', 'yearly archives date format'));
        } elseif (is_month()) {
            return get_the_date(_x('F Y', 'monthly archives date format'));
        } elseif (is_day()) {
            return get_the_date(_x('F j, Y', 'daily archives date format'));
        } elseif (is_post_type_archive()) {
            return post_type_archive_title('', false);
        }
        return $title;
    }

    /**
     * Filter away "Archive:" from archive title
     * @param  string $title
     * @return string
     */
    public function pageTitle($title)
    {
        return preg_replace('/(archive|arkiv|' . __('Archive') . '):/i', '', $title);
    }

    public function onlyFirstLevel($query)
    {
        if (is_author() || !is_archive() || !$query->is_main_query() || is_admin()) {
            return;
        }

        $inMenu = false;
        foreach ((array) get_field('avabile_dynamic_post_types', 'options') as $type) {
            if ($type['slug'] !== $query->post_type) {
                continue;
            }

            if (!$type['show_posts_in_sidebar_menu']) {
                return;
            }
        }

        $query->set('post_parent', 0);
    }

    /**
     * Makes it possible to have "page" children below a parent page that's a page_for_{post_type}
     * @param  WP_Query $query
     * @return void
     */
    public function enablePageForPostTypeChildren($query)
    {
        if (!$query->is_main_query() || is_admin()) {
            return;
        }

        // Check if page_for_{post_type} isset,  return if not
        $postType = $query->get('post_type');
        if (is_array($postType)) {
            $postType = end($postType);
        }

        $pageForPostType = get_option('page_for_' . $postType);
        if (!$pageForPostType) {
            return;
        }

        // Test if wp_query gives results, return if it does
        $testQuery = new \WP_Query($query->query);
        if ($testQuery->have_posts()) {
            return;
        }

        // Modify query to check for page instead of post_type
        $query->set('post_type', 'page');
        $query->set('child_of', $pageForPostType);
    }
}
