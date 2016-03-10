<?php

namespace Municipio\Helper;

class NavigationTree
{
    public $args = array();

    protected $currentPage = null;
    protected $ancestors = null;
    protected $topLevelPages = null;

    protected $itemCount = 0;

    protected $output = '';

    public function __construct($args = array())
    {
        // Merge args
        $this->args = array_merge(array(
            'include_top_level' => false
        ), $args);

        // Get valuable page information
        $this->currentPage = $this->getCurrentPage();
        $this->ancestors = $this->getAncestors();

        $this->topLevelPages = get_posts(array(
            'post_parent' => 0,
            'post_type' => 'page',
            'post_status' => 'publish',
            'orderby' => 'menu_order post_title',
            'order' => 'asc',
            'numberposts' => -1
        ));

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
        foreach ($pages as $page) {
            $classes = array();

            if (is_numeric($page)) {
                $page = get_page($page);
            }

            if ($this->isAncestors($page->ID)) {
                $classes[] = 'current-node';
            }

            if ($this->currentPage->ID == $page->ID) {
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
        $children = $this->getChildren($page->ID);

        if (count($children) > 0) {
            $classes[] = 'has-children';
        }

        $this->startItem($page, $classes);

        if ($this->isActiveItem($page->ID) && count($children) > 0) {
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
            'numberposts' => -1
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
        if ($this->shouldBeIncluded($item)) {
            return;
        }

        $this->itemCount++;

        $classes[] = 'page-' . $item->ID;

        $this->addOutput(sprintf(
            '<li class="%1$s"><a href="%2$s">%3$s</a>',
            $classes = implode(' ', $classes),
            $href = get_permalink($item->ID),
            $title = !empty(get_field('custom_menu_title', $item->ID)) ? get_field('custom_menu_title', $item->ID) : $item->post_title
        ));
    }

    /**
     * Closes a menu item
     * @param  object $item The menu item
     * @return void
     */
    protected function endItem($item)
    {
        if ($this->shouldBeIncluded($item)) {
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
        if ($this->shouldBeIncluded($item)) {
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
        if ($this->shouldBeIncluded($item)) {
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
        $hide = get_field('hide_in_menu', $item->ID);
        return (!$this->args['include_top_level'] && $item->post_parent === 0) || $hide;
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
    public function render()
    {
        echo $this->output;
    }

    /**
     * Gets the item count
     * @return void
     */
    public function itemCount()
    {
        return $this->itemCount;
    }
}
