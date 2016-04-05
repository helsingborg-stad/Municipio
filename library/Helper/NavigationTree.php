<?php

namespace Municipio\Helper;

class NavigationTree
{
    public $args = array();

    protected $currentPage = null;
    protected $ancestors = null;
    protected $topLevelPages = null;

    public $itemCount = 0;
    protected $depth = 0;

    protected $output = '';

    public function __construct($args = array())
    {
        // Merge args
        $this->args = array_merge(array(
            'include_top_level' => false,
            'top_level_type' => 'tree',
            'render' => 'active',
            'depth' => -1
        ), $args);

        // Get valuable page information
        $this->currentPage = $this->getCurrentPage();
        $this->ancestors = $this->getAncestors();

        if ($this->args['top_level_type'] == 'mobile') {
            $themeLocations = get_nav_menu_locations();
            $this->topLevelPages = wp_get_nav_menu_items($themeLocations['main-menu'], array(
                'menu_item_parent' => 0
            ));

            $this->topLevelPages = array_filter($this->topLevelPages, function ($item) {
                return intval($item->menu_item_parent) === 0;
            });
        } else {
            $this->topLevelPages = get_posts(array(
                'post_parent' => 0,
                'post_type' => 'page',
                'post_status' => 'publish',
                'orderby' => 'menu_order post_title',
                'order' => 'asc',
                'numberposts' => -1,
                'meta_query'    => array(
                    'relation' => 'OR',
                    array(
                        'key' => 'hide_in_menu',
                        'compare' => 'NOT EXISTS'
                    ),
                    array(
                        'key'   => 'hide_in_menu',
                        'value' => '0',
                        'compare' => '='
                    )
                )
            ));
        }

        if ($this->args['include_top_level']) {
            $this->walk($this->topLevelPages);
        } else {
            $page = isset($this->ancestors[0]) ? array($this->ancestors[0]) : array($this->currentPage);
            $this->walk($page);
        }
    }

    /**
     * Walks pages in the menu
     * @param  array $pages Pages to walk
     * @return void
     */
    protected function walk($pages)
    {
        $this->depth++;

        foreach ($pages as $page) {
            $pageId = $this->getPageId($page);
            $classes = array();

            if (is_numeric($page)) {
                $page = get_page($page);
            }

            if ($this->isAncestors($pageId)) {
                $classes[] = 'current-node';
            }

            if ($this->getPageId($this->currentPage) == $pageId) {
                $classes[] = 'current';
            }

            $this->item($page, $classes);
        }
    }

    /**
     * Outputs item
     * @param  object $page    The item
     * @param  array  $classes Classes
     * @return void
     */
    protected function item($page, $classes = array())
    {
        $pageId = $this->getPageId($page);
        $children = $this->getChildren($pageId);

        if (count($children) > 0) {
            $classes[] = 'has-children';
        }

        $this->startItem($page, $classes);

        if ($this->isActiveItem($pageId) && count($children) > 0 && ($this->args['depth'] <= 0 || $this->depth < $this->args['depth'])) {
            $this->startSubmenu($page);
            $this->walk($children);
            $this->endSubmenu($page);
        }

        $this->endItem($page);
    }

    /**
     * Gets the current page object
     * @return object
     */
    protected function getCurrentPage()
    {
        global $post;
        if (!is_object($post)) {
            return get_queried_object();
        }

        return $post;
    }

    /**
     * Get page children
     * @param  integer $parent The parent page ID
     * @return object          Page objects for children
     */
    protected function getChildren($parent)
    {
        return get_posts(array(
            'post_parent' => $parent,
            'post_type' => 'page',
            'post_status' => 'publish',
            'orderby' => 'menu_order post_title',
            'order' => 'asc',
            'numberposts' => -1,
            'meta_query'    => array(
                'relation' => 'OR',
                array(
                    'key' => 'hide_in_menu',
                    'compare' => 'NOT EXISTS'
                ),
                array(
                    'key'   => 'hide_in_menu',
                    'value' => '0',
                    'compare' => '='
                )
            )
        ), 'OBJECT');
    }

    /**
     * Get ancestors of the current page
     * @return array ID's of ancestors
     */
    protected function getAncestors()
    {
        return array_reverse(get_post_ancestors($this->currentPage));
    }

    /**
     * Checks if a specific id is in the ancestors array
     * @param  integer  $id
     * @return boolean
     */
    protected function isAncestors($id)
    {
        return in_array($id, $this->ancestors);
    }

    /**
     * Checks if the given id is in a active/open menu scope
     * @param  integer  $id Page id
     * @return boolean
     */
    protected function isActiveItem($id)
    {
        if ($this->args['render'] == 'all') {
            return true;
        }

        return $this->isAncestors($id) || $id === $this->currentPage->ID;
    }

    /**
     * Opens a menu item
     * @param  object $item    The menu item
     * @param  array  $classes Classes
     * @return void
     */
    protected function startItem($item, $classes = array())
    {
        if (!$this->shouldBeIncluded($item)) {
            return;
        }

        $this->itemCount++;

        $classes[] = 'page-' . $item->ID;

        $title = isset($item->post_title) ? $item->post_title : '';
        $objId = $this->getPageId($item);

        if (isset($item->post_type) && $item->post_type == 'nav_menu_item') {
            $title = $item->title;
        }

        if (!empty(get_field('custom_menu_title', $objId))) {
            $title = get_field('custom_menu_title', $objId);
        }

        $href = get_permalink($objId);
        if (isset($item->type) && $item->type == 'custom') {
            $href = $item->url;
        }

        $this->addOutput(sprintf(
            '<li class="%1$s"><a href="%2$s">%3$s</a>',
            $classes = implode(' ', $classes),
            $href,
            $title
        ));
    }

    /**
     * Closes a menu item
     * @param  object $item The menu item
     * @return void
     */
    protected function endItem($item)
    {
        if (!$this->shouldBeIncluded($item)) {
            return;
        }

        $this->addOutput('</li>');
    }

    /**
     * Opens a submenu
     * @return void
     */
    protected function startSubmenu($item)
    {
        if (!$this->shouldBeIncluded($item)) {
            return;
        }

        $this->addOutput('<ul class="sub-menu">');
    }

    /**
     * Closes a submenu
     * @return void
     */
    protected function endSubmenu($item)
    {
        if (!$this->shouldBeIncluded($item)) {
            return;
        }

        $this->addOutput('</ul>');
    }

    /**
     * Datermines if page should be included in the menu or not
     * @param  integer $id The page ID
     * @return boolean
     */
    public function shouldBeIncluded($item)
    {
        $pageId = $this->getPageId($item);
        $hide = get_field('hide_in_menu', $pageId) ? get_field('hide_in_menu', $pageId) : false;
        return !(isset($item->post_parent) && !$this->args['include_top_level'] && $item->post_parent === 0) || $hide;
    }


    /**
     * Adds markup to the output string
     * @param string $string Markup to add
     */
    protected function addOutput($string)
    {
        $this->output .= $string;
    }

    /**
     * Echos the output
     * @return void
     */
    public function render($echo = true)
    {
        if ($echo) {
            echo $this->output;
            return true;
        }

        return $this->output;
    }

    /**
     * Gets the item count
     * @return void
     */
    public function itemCount()
    {
        return $this->itemCount;
    }

    public function getPageId($page)
    {
        if (is_null($page)) {
            return false;
        }

        if (!is_object($page)) {
            $page = get_page($page);
        }

        if (isset($page->post_type) && $page->post_type == 'nav_menu_item') {
            return intval($page->object_id);
        }

        return $page->ID;
    }
}
