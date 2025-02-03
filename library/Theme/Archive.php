<?php

namespace Municipio\Theme;

class Archive
{
    public function __construct()
    {
        add_action('pre_get_posts', array($this, 'onlyFirstLevel'));
        add_action('pre_get_posts', array($this, 'enablePageForPostTypeChildren'), 30, 1);
        add_action('pre_get_posts', array($this, 'filterNumberOfPostsInArchive'), 20, 1);
        add_action('pre_get_posts', array($this, 'addOrderByFallback'), 40, 1);
    }

    /**
     * Add fallback orderby to main query
     * Avoids inconsistency in ordering when using date or modified as orderby and multiple posts have the same date or modified.
     *
     * @param $query The query to show current archive page return
     * @return void
     */
    public function addOrderByFallback($query): void
    {
        if ($query->is_main_query()) {
            if ($query->get('orderby') === 'date' || $query->get('orderby') === 'modified') {
                $query->set('orderby', [$query->get('orderby'), 'ID']);
            }
        }
    }

    /*
    * Filter number of posts that should be displayed in archive list
    * @param $WP_Query The query to show current archive page return
    * @return bool True or false depending on if the query has been altered or not.
    */
    public function filterNumberOfPostsInArchive($query): bool
    {
        if (!is_admin() && $query->is_main_query()) {
            //Check that posttype is valid
            if (!isset($query->query["post_type"])) {
                return false;
            }

            //Get current post count
            $postCount = get_theme_mod('archive_' . $query->query["post_type"] . '_post_count', 12);

            //Set value
            if (isset($postCount) && !empty($postCount) && is_numeric($postCount)) {
                $query->set('posts_per_page', $postCount);
                return true;
            }
        }

        return false;
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

        // Test if we're in a pagination page, return if we are
        if (is_paged()) {
            return;
        }

        // Modify query to check for page instead of post_type
        $query->set('post_type', 'page');
        $query->set('child_of', $pageForPostType);
    }
}
