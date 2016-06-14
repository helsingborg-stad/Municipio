<?php

namespace Intranet\Controller;

class TableOfContents extends \Municipio\Controller\BaseController
{
    public function init()
    {
        $this->data['tableOfContents'] = $this->getTableOfContents();
    }

    public function getTableOfContents()
    {
        global $wpdb;

        $alphaArray = array();
        $topLevel = array();
        $secondLevel = array();

        $postStatuses = array('publish');
        if (is_user_logged_in()) {
            $postStatuses[] = 'private';
        }

        // Get top level pages
        foreach (\Intranet\Helper\Multisite::getSitesList(true, true) as $blogId) {
            switch_to_blog($blogId);

            // Get top level for blog
            $blogTopLevel = $wpdb->get_results("
                SELECT *, " . $blogId . " AS blog_id FROM {$wpdb->posts} AS posts
                WHERE
                    posts.post_type = 'page'
                    AND posts.post_parent = ''
                    AND posts.post_status IN ('" . implode('\',\'', $postStatuses) . "')
                    AND NOT posts.ID = " . get_option('page_on_front') . "
            ");

            // Merge to top level list
            $topLevel = array_merge($topLevel, $blogTopLevel);

            // Get ids of top level pages
            $blogTopLevel = array();
            foreach ($topLevel as $page) {
                $topLevelIds[] = $page->ID;
            }

            // Get second level pages
            $blogSecondLevel = $wpdb->get_results("
                SELECT *, " . $blogId . " AS blog_id FROM {$wpdb->posts} AS posts
                WHERE
                    posts.post_type = 'page'
                    AND posts.post_parent IN (" . implode(',', $topLevelIds) . ")
                    AND posts.post_status IN ('" . implode('\',\'', $postStatuses) . "')
            ");

            $secondLevel = array_merge($secondLevel, $blogSecondLevel);

            restore_current_blog();
        }

        $pages = array_merge($topLevel, $secondLevel);

        foreach ($pages as $page) {
            $key = strtolower(mb_substr($page->post_title, 0, 1));
            $alphaArray[$key][] = $page;
        }

        ksort($alphaArray);

        return $alphaArray;
    }
}
