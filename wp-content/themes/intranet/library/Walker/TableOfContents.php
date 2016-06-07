<?php

namespace Intranet\Walker;

class TableOfContents extends \Walker
{
    public $tree_type = 'page';
    public $db_fields = array( 'parent' => 'post_parent', 'id' => 'ID' );
    public $pages = array();

    public function start_lvl(&$output, $depth = 0, $args = array())
    {
    }

    public function end_lvl(&$output, $depth = 0, $args = array())
    {
    }

    public function start_el(&$output, $page, $depth = 0, $args = array(), $current_page = 0)
    {
        $this->pages[] = $page;
    }

    /**
     * Outputs the end of the current element in the tree.
     *
     * @since 2.1.0
     * @access public
     *
     * @see Walker::end_el()
     *
     * @param string  $output Used to append additional content. Passed by reference.
     * @param WP_Post $page   Page data object. Not used.
     * @param int     $depth  Optional. Depth of page. Default 0 (unused).
     * @param array   $args   Optional. Array of arguments. Default empty array.
     */
    public function end_el(&$output, $page, $depth = 0, $args = array())
    {
    }
}
