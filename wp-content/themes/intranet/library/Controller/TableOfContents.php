<?php

namespace Intranet\Controller;

class TableOfContents extends \Intranet\Controller\BaseController
{
    public function init()
    {
        $this->data['tableOfContents'] = $this->getTableOfContents();
        $this->data['selectedDepartment'] = isset($_GET['department']) && !empty($_GET['department']) ? $_GET['department'] : null;
        $this->data['titleQuery'] = isset($_GET['title']) && !empty($_GET['title']) ? $_GET['title'] : null;
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
                    AND posts.post_title != ''
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
                    AND posts.post_title != ''
                    AND posts.post_parent IN (" . implode(',', $topLevelIds) . ")
                    AND posts.post_status IN ('" . implode('\',\'', $postStatuses) . "')
            ");

            $secondLevel = array_merge($secondLevel, $blogSecondLevel);

            restore_current_blog();
        }

        var_dump(get_current_blog_id());

        $pages = array_merge($topLevel, $secondLevel);

        if (isset($_GET['department']) && !empty($_GET['department'])) {
            $pages = array_filter($pages, function ($item) {
                return $item->blog_id == $_GET['department'];
            });
        }

        foreach ($pages as $key => $page) {
            switch_to_blog($page->blog_id);
            $titles = get_field('table_of_contents_titles', $page->ID);

            if (!is_array($titles)) {
                continue;
            }

            foreach ($titles as $title) {
                $cloned = clone $page;
                $cloned->post_title = $title['title'];
                $pages[] = $cloned;
            }

            unset($pages[$key]);

            restore_current_blog();
        }

        if (isset($_GET['title']) && !empty($_GET['title'])) {
            $pages = array_filter($pages, function ($item) {
                return stripos($item->post_title, $_GET['title']) > -1;
            });
        }

        foreach ($pages as $page) {
            $key = strtolower(mb_substr($page->post_title, 0, 1));
            $alphaArray[$key][] = $page;
        }

        ksort($alphaArray);

        return $alphaArray;
    }
}
