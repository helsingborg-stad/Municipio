<?php

namespace Municipio\Helper;

class NavigationTree
{
    public $args = array();

    protected $postStatuses = array('publish');

    protected $currentPage = null;
    protected $ancestors = null;

    protected $topLevelPages = null;
    protected $secondLevelPages = null;

    protected $pageForPostTypeIds = null;

    public $itemCount = 0;
    protected $depth = 0;

    protected $output = '';

    public function __construct($args = array())
    {
        // Merge args
        $this->args = array_merge(array(
            'include_top_level' => false,
            'sublevel' => false,
            'top_level_type' => 'tree',
            'render' => 'active',
            'depth' => -1,
            'wrapper' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
            'classes' => 'nav',
            'id' => ''
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
            $this->getTopLevelPages();
        }

        if ($this->args['include_top_level']) {
            if ($this->args['sublevel']) {
                $this->walk($this->topLevelPages, 'nav-has-sublevel');
                $this->getSecondLevelPages();

                $walkIndex = null;
                if (!empty($this->ancestors)) {
                    $walkIndex = $this->ancestors[0];
                } else {
                    $walkIndex = $this->currentPage->ID;
                }

                if (isset($this->secondLevelPages[$walkIndex]) && !is_null($walkIndex)) {
                    if ($this->currentPage->post_parent == 0) {
                        global $isSublevel;
                        $isSublevel = true;
                    }

                    $this->walk($this->secondLevelPages[$walkIndex], 'nav-sublevel');
                }
            } else {
                $this->startWrapper();
                $this->walk($this->topLevelPages);
                $this->endWrapper();
            }
        } else {
            $page = isset($this->ancestors[0]) ? $this->ancestors[0] : $this->currentPage;

            if ($page) {
                $this->startWrapper();
                $this->walk(array($page));
                $this->endWrapper();
            }
        }
    }

    protected function getTopLevelPages()
    {
        if (is_user_logged_in()) {
            $this->postStatuses[] = 'private';
        }

        $topLevelQuery = new \WP_Query(array(
            'post_parent' => 0,
            'post_type' => 'page',
            'post_status' => $this->postStatuses,
            'orderby' => 'menu_order post_title',
            'order' => 'asc',
            'posts_per_page' => -1,
            'meta_query'    => array(
                'relation' => 'AND',
                array(
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
            )
        ));

        $this->topLevelPages = $topLevelQuery->posts;
        return $this->topLevelPages;
    }

    protected function getSecondLevelPages()
    {
        $secondLevel = array();

        foreach ($this->topLevelPages as $topLevelPage) {
            $pages = get_children(array(
                'post_parent' => $topLevelPage->ID,
            ));

            $secondLevel[$topLevelPage->ID] = $pages;
        }

        $this->secondLevelPages = $secondLevel;
        return $secondLevel;
    }

    /**
     * Walks pages in the menu
     * @param  array $pages Pages to walk
     * @return void
     */
    protected function walk($pages, $classes = null)
    {
        $this->depth++;

        if ($this->args['sublevel']) {
            $this->startWrapper($classes);
        }

        foreach ($pages as $page) {
            $pageId = $this->getPageId($page);
            $classes = array();

            if (is_numeric($page)) {
                $page = get_post($page);
            }

            if ($this->isAncestors($pageId)) {
                $classes[] = 'current-node current-menu-ancestor';
            }

            if ($this->getPageId($this->currentPage) == $pageId) {
                $classes[] = 'current current-menu-item';
            }

            $this->item($page, $classes);
        }

        if ($this->args['sublevel']) {
            $this->endWrapper();
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
        if (is_post_type_archive()) {
            $pageForPostType = get_option('page_for_' . get_post_type());
            return get_post($pageForPostType);
        }

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
        $key = array_search($parent, $this->getPageForPostTypeIds());

        if ($key && is_post_type_hierarchical($key)) {
            $inMenu = false;

            foreach (get_field('avabile_dynamic_post_types', 'options') as $type) {
                if ($type['slug'] !== $key) {
                    continue;
                }

                $inMenu = $type['show_posts_in_sidebar_menu'];
            }

            if ($inMenu) {
                return get_posts(array(
                    'post_type' => $key,
                    'post_status' => $this->postStatuses,
                    'post_parent' => 0,
                    'orderby' => 'menu_order post_title',
                    'order' => 'asc',
                    'posts_per_page' => -1,
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

            return array();
        }

        return get_posts(array(
            'post_parent' => $parent,
            'post_type' => 'any',
            'post_status' => $this->postStatuses,
            'orderby' => 'menu_order post_title',
            'order' => 'asc',
            'posts_per_page' => -1,
            'meta_query'    => array(
                'relation' => 'AND',
                array(
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
            )
        ), 'OBJECT');
    }

    protected function getPageForPostTypeIds()
    {
        if (is_array($this->pageForPostTypeIds)) {
            return $this->pageForPostTypeIds;
        }

        $pageIds = array();

        foreach (get_post_types(array(), 'objects') as $postType) {
            if (! $postType->has_archive) {
                continue;
            }

            if ('post' === $postType->name) {
                $pageId = get_option('page_for_posts');
            } else {
                $pageId = get_option("page_for_{$postType->name}");
            }

            if (!$pageId) {
                continue;
            }

            $pageIds[$postType->name] = $pageId;
        }

        $this->pageForPostTypeIds = $pageIds;
        return $this->pageForPostTypeIds;
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
        if ($this->args['render'] == 'all' || !is_object($this->currentPage)) {
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
        if (!$this->shouldBeIncluded($item) || !is_object($item)) {
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
     * Starts wrapper
     * @return void
     */
    protected function startWrapper($classes = null)
    {
        $wrapperStart = explode('%3$s', $this->args['wrapper'])[0];
        $this->addOutput(sprintf(
            $wrapperStart,
            $this->args['id'],
            trim($this->args['classes'] . ' ' . $classes)
        ));
    }

    /**
     * Ends wrapper
     * @return void
     */
    protected function endWrapper()
    {
        $wrapperEnd = explode('%3$s', $this->args['wrapper'])[1];
        $this->addOutput(sprintf(
            $wrapperEnd,
            $this->args['id'],
            $this->args['classes']
        ));
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
     * @param  object $item The menu item
     * @return boolean
     */
    public function shouldBeIncluded($item)
    {
        if (!is_object($item)) {
            return false;
        }

        $pageId = $this->getPageId($item);
        $showInMenu = get_field('hide_in_menu', $pageId) ? !get_field('hide_in_menu', $pageId) : true;
        $isNotTopLevelItem = !($item->post_type === 'page' && isset($item->post_parent) && $item->post_parent === 0);
        $showTopLevel = $this->args['include_top_level'];

        return ($showTopLevel || $isNotTopLevelItem) && $showInMenu;
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
    public function render($echo = true, $wrapper = array())
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
            $page = get_post($page);
        }

        if (isset($page->post_type) && $page->post_type == 'nav_menu_item') {
            return intval($page->object_id);
        }

        return $page->ID;
    }
}
