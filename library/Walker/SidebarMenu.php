<?php

namespace Municipio\Walker;

class SidebarMenu extends \Walker_Nav_Menu
{
    public function walk($elements, $max_depth)
    {
        global $post;
        $current_page = $post;

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

        foreach ($elements as $element) {
            if (isset($element->object_id) && intval($element->object_id) == $current_page->ID) {
                $current_page = $element->ID;
                break;
            }
        }

        $child_of = $this->getChildOf($current_page, $elements);

        $top_level_elements = array();
        $children_elements  = array();

        foreach ($elements as $element) {
            if ($element->$parent_field == $child_of) {
                $top_level_elements[] = $element;
            } elseif ($element->$parent_field != 0) {
                $children_elements[$element->$parent_field][] = $element;
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
            $orphans = isset($children_elements[$child_of]) ? $children_elements[$child_of] : false;

            if ($orphans) {
                foreach ($orphans as $op) {
                    $this->display_element($op, $empty_array, 1, 0, $args, $output);
                }
            }
        }

        return $output;
    }

    /**
     * Find which the top parent for the current page
     * @param  integer $current_page Current page menu id
     * @param  array $elements       Menu elements
     * @return integer               Menu parent id (child_of)
     */
    public function getChildOf($current_page, $elements)
    {
        $child_of = 0;

        if (is_a($current_page, '\WP_Post')) {
            $current_page = $current_page->ID;
        }

        foreach ($elements as $key => $element) {
            if (isset($element->ID) && isset($current_page) && $element->ID == $current_page) {
                $child_of = $element->ID;
                unset($elements[$key]);

                if (intval($element->menu_item_parent) > 0) {
                    $child_of = $this->getChildOf(intval($element->menu_item_parent), $elements);
                }

                break;
            }
        }

        return $child_of;
    }
}
