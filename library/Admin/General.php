<?php

namespace Municipio\Admin;

class General
{
    public function __construct()
    {
        add_filter('wp_dropdown_pages', array($this, 'pageForPostsDropdown'), 10, 3);
    }

    /**
     * Show private pages in the "page for posts" dropdown
     * @param  string $output Dropdown markup
     * @param  array  $r      Arguments
     * @param  array  $pages  Default pages
     * @return string         New dropdown markup
     */
    public function pageForPostsDropdown($output, $r, $pages)
    {
        if ($r['name'] !== 'page_for_posts') {
            return $output;
        }

        $r['post_status'] = array('publish', 'private');
        $pages = get_pages($r);

        $class = '';
        if (! empty($r['class'])) {
            $class = " class='" . esc_attr($r['class']) . "'";
        }

        $output = "<select name='" . esc_attr($r['name']) . "'" . $class . " id='" . esc_attr($r['id']) . "'>\n";
        if ($r['show_option_no_change']) {
            $output .= "\t<option value=\"-1\">" . $r['show_option_no_change'] . "</option>\n";
        }

        if ($r['show_option_none']) {
            $output .= "\t<option value=\"" . esc_attr($r['option_none_value']) . '">' . $r['show_option_none'] . "</option>\n";
        }

        add_filter('list_pages', array($this, 'listPagesTitle'), 100, 2);

        $output .= walk_page_dropdown_tree($pages, $r['depth'], $r);

        remove_filter('list_pages', array($this, 'listPagesTitle'), 100);

        $output .= "</select>\n";

        return $output;
    }

    /**
     * Show (private) label in "page for posts" dropdown on private pages
     * @param  string $title Page title
     * @param  object $page  Page object
     * @return string        Modified page title
     */
    public function listPagesTitle($title, $page)
    {
        if ($page->post_status == 'private') {
            return $page->post_title . ' (' . __('Private') . ')';
        }

        return $title;
    }
}
