<?php

namespace Municipio\Walker;

class MainMenu extends \Walker
{
    public $tree_type = 'sidebar-menu';

    public $db_fields = array(
        'parent' => 'post_parent',
        'id'     => 'ID'
    );

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
