<?php

namespace Municipio\Walker;

class Navigation extends \Walker
{
    public $tree_type = 'sidebar-menu';

    public $db_fields = array(
        'parent' => 'post_parent',
        'id'     => 'ID'
    );

    public function walk($elements, $max_depth)
    {
        global $post;
        global $childOf;

        if ($childOf === $post->ID) {
            $max_depth = 1;
        }

        $args = array_slice(func_get_args(), 2);
        $output = '';

        //invalid parameter or nothing to walk
        if ($max_depth < -1 || empty($elements)) {
            return $output;
        }

        $parent_field = $this->db_fields['parent'];

        // flat display
        if (-1 == $max_depth) {
            $empty_array = array();
            foreach ($elements as $e) {
                $this->display_element($e, $empty_array, 1, 0, $args, $output);
            }
            return $output;
        }

        /*
         * Need to display in hierarchical order.
         * Separate elements into two buckets: top level and children elements.
         * Children_elements is two dimensional array, eg.
         * Children_elements[10][] contains all sub-elements whose parent is 10.
         */
        $top_level_elements = array();
        $children_elements  = array();
        foreach ($elements as $e) {
            if ($args[0]['child_of'] == $e->$parent_field) {
                $top_level_elements[] = $e;
            } else {
                $children_elements[ $e->$parent_field ][] = $e;
            }
        }

        /*
         * When none of the elements is top level.
         * Assume the first one must be root of the sub elements.
         */
        if (empty($top_level_elements)) {
            $first = array_slice($elements, 0, 1);
            $root = $first[0];

            $top_level_elements = array();
            $children_elements  = array();
            foreach ($elements as $e) {
                if ($root->$parent_field == $e->$parent_field) {
                    $top_level_elements[] = $e;
                } else {
                    $children_elements[ $e->$parent_field ][] = $e;
                }
            }
        }

        foreach ($top_level_elements as $e) {
            $this->display_element($e, $children_elements, $max_depth, 0, $args, $output);
        }

        /*
         * If we are displaying all levels, and remaining children_elements is not empty,
         * then we got orphans, which should be displayed regardless.
         */
        if (($max_depth == 0) && count($children_elements) > 0) {
            $empty_array = array();
            foreach ($children_elements as $orphans) {
                foreach ($orphans as $op) {
                    $this->display_element($op, $empty_array, 1, 0, $args, $output);
                }
            }
        }

        return $output;
    }

    /**
     * The start_lvl() method is run when the walker reaches the start of a new "branch" in the tree structure.
     * Generally, this method is used to add the opening tag of acontainer HTML element (such as <ol>, <ul>,
     * or <div>) to $output.
     * @param  string  &$output The output
     * @param  integer $depth   The depth of this level
     * @param  array   $args    The args of this level
     * @return void             Appends markup to the output
     */
    public function start_lvl(&$output, $depth = 0, $args = array())
    {
        $indent = str_repeat("\t", $depth);
        $output .= "\n$indent<ul class='sub-menu'>\n";
    }

    /**
     * This method is run when the walker reaches the end of a "branch" in the tree structure.
     * Generally, this method is used to add the closing tag of a container HTML element (such
     * as </ol>, </ul>, or </div>) to $output.
     * @param  string  &$output The output
     * @param  integer $depth   The depth of this level
     * @param  aarray   $args    The args of this level
     * @return void             Appends markup to the output
     */
    public function end_lvl(&$output, $depth = 0, $args = array())
    {
        $indent = str_repeat("\t", $depth);
        $output .= "$indent</ul>\n";
    }

    /**
     * Generally, this method is used to add the opening HTML tag for a single tree item
     * (such as <li>, <span>, or <a>) to $output.
     * @param  string  &$output           The output
     * @param  object  $object            The item object
     * @param  integer $depth             The depth of this element
     * @param  array   $args              The args of this element
     * @param  integer $current_object_id The object id
     * @return void                       Appends markup to the output
     */
    public function start_el(&$output, $item, $depth = 0, $args = array(), $current_object_id = 0)
    {
        global $post;

        $classes = array();

        if ($item->ID === get_the_ID()) {
            $classes[] = 'current';
        }

        $childrenArgs = array(
            'post_type' => 'page',
            'post_status' => 'publish',
            'post_parent' => $item->ID
        );

        if (count(get_children($childrenArgs)) > 0) {
            $classes[] = 'has-children';
        }

        if (in_array($item->ID, get_post_ancestors($post->ID))) {
            $classes[] = 'current-node';
        }

        $output .= sprintf(
            "\n" . '<li %s><a href="%s">%s</a>' . "\n",
            'class="' . implode(' ', $classes) . '"',
            get_permalink($item->ID),
            $item->post_title
        );
    }

    /**
     * Generally, this method is used to add any closing HTML tag for a single tree item
     * (such as </li>, </span>, or </a>) to $output. Note that elements are not ended
     * until after all of their children have been added.
     * @param  string  &$output           The output
     * @param  object  $object            The item object
     * @param  integer $depth             The depth of this element
     * @param  array   $args              The args of this element
     * @return void                       Appends markup to the output
     */
    public function end_el(&$output, $object, $depth = 0, $args = array())
    {
        $output .= '</li>';
    }
}
