<?php

namespace Municipio\Walker;

class MainMenu extends \Walker
{
    public $tree_type = 'page';
    public $db_fields = array(
        'parent' => 'post_parent',
        'id'     => 'ID'
    );

    public function walk($elements, $max_depth)
    {
        global $post;

        $args = array_slice(func_get_args(), 2);
        $output = '';

        /**
         * Check if max_depth is not invalid
         */
        if ($max_depth < -1) {
            return $output;
        }

        /**
         * Set max_depth to 1 if this is a search request
         */
        if (is_search()) {
            $max_depth = 1;
        }

        /**
         * Check if there's any pages to walk
         */
        if (empty($elements)) {
            return $output;
        }

        /**
         * Set up variables
         */
        $top_level_elements = array();
        $children_elements  = array();
        $parent_field       = $this->db_fields['parent'];
        $child_of           = null;

        if (empty(get_post_ancestors($post->ID))) {
            return $output;
        }

        if (array_reverse(get_post_ancestors($post->ID))[1]) {
            $child_of = array_reverse(get_post_ancestors($post->ID))[1];
        } else {
            $child_of = $post->ID;
        }

        /**
         * Loop elements
         */
        foreach ((array)$elements as $e) {
            $parent_id = $e->$parent_field;

            if (isset($parent_id)) {
                /**
                 * Do not show childs of a list page
                 */
                if (get_post_meta($parent_id, '_wp_page_template', true) == 'templates/list-page.php') {
                    continue;
                }

                /**
                 * If top level page
                 */
                if ($child_of === $parent_id) {
                    $top_level_elements[] = $e;
                } elseif ((isset($post->ID) && $parent_id == $post->ID) ||
                         (isset($post->post_parent) && $parent_id == $post->post_parent) ||
                         (isset($post->ancestors) && in_array($parent_id, (array) $post->ancestors))
                ) {
                    $children_elements[$e->$parent_field][] = $e;
                }
            }
        }

        /**
         * Show the top level elements
         */
        foreach ($top_level_elements as $e) {
            $this->display_element($e, $children_elements, $max_depth, 0, $args, $output);
        }

        return $output;
    }

    /**
     * Markup for starting a sub menu level
     * @param  string  &$output
     * @param  integer $depth   [description]
     * @param  array   $args    [description]
     * @return void
     */
    public function start_lvl(&$output, $depth = 0, $args = array())
    {
        $indent = str_repeat("\t", $depth);
        $output .= "\n$indent<ul class='sub-menu'>\n";
    }

    /**
     * Markup for closing a sub menu level
     * @param  string  &$output
     * @param  integer $depth   [description]
     * @param  array   $args    [description]
     * @return void
     */
    public function end_lvl(&$output, $depth = 0, $args = array())
    {
        $indent = str_repeat("\t", $depth);
        $output .= "$indent</ul>\n";
    }

    /**
     * Start an element
     * @param  string  &$output
     * @param  string  $page
     * @param  integer $depth
     * @param  array   $args
     * @param  integer $current_page
     * @return void
     */
    public function start_el(&$output, $page, $depth = 0, $args = array(), $current_page = 0)
    {
        if (get_post_status($page->post_parent) == 'private'
            && get_the_title($page->post_parent) == 'Privat: Skicka meddelande') {
            return;
        }

        /**
         * Element indentation
         * @var string
         */
        $indent = '';
        if (isset($depth)) {
            $indent = str_repeat("\t", $depth);
        }

        if (!empty($current_page)) {
            $page_on_front = null;

            if (array_reverse(get_post_ancestors($post->ID))[1]) {
                $page_on_front = array_reverse(get_post_ancestors($post->ID))[1];
            } else {
                $page_on_front = $post->ID;
            }

            /**
             * Get current page object
             * @var object
             */
            $_current_page = get_post($current_page);

            /**
             * Holds list of css classes top apply to menu node
             * @var array
             */
            $css_class_list = array();

            /**
             * Add class "current-node" if this page is the current node
             */
            if (in_array($page->ID, $_current_page->ancestors) && $page->post_parent == $page_on_front) {
                array_push($css_class_list, 'current-node');
            }

            /**
             * Add class "current-ancestor" if this page is the current ancestor
             */
            if (in_array($page->ID, $_current_page->ancestors)) {
                array_push($css_class_list, 'current-ancestor');
            }

            /**
             * Add class "current-ancestor" if this page is the current ancestor
             */
            $args = array(
                'post_type' => 'page',
                'post_status' => 'publish',
                'post_parent' => $_current_page->ID,
            );

            $current_page_children = get_children($args);
            $current_page_has_children = empty($children);

            if ($page->ID == $_current_page->ancestors[0] && !$current_page_has_children) {
                array_push($css_class_list, 'current-ancestor-last');
            }

            /**
             * Add class "current-page" if this is the current page
             * @var [type]
             */
            if ($page->ID == $current_page) {
                array_push($css_class_list, 'current');

                if ($page->post_parent == $page_on_front) {
                    array_push($css_class_list, 'current-node');
                }

                if ($current_page_has_children) {
                    array_push($css_class_list, 'current-ancestor-last');
                }
            }

            /**
             * Query for this page children
             * @var array
             */
            $args = array(
                'post_type' => 'page',
                'post_status' => 'publish',
                'post_parent' => $page->ID,
            );

            $children = get_children($args);
            $has_children = !empty($children);

            /**
             * Check if page got childrens or not, if it does, add has-children class
             * - Exclude list page childrens
             */
            if ($has_children && get_post_meta($page->ID, '_wp_page_template', true) != 'templates/list-page.php') {
                array_push($css_class_list, 'has-children');
            }

            /**
             * If article page parent is list page, then mark the parent as current -> since childs are hidden
             */
            if (in_array($page->ID, $_current_page->ancestors) && get_post_meta($page->ID, '_wp_page_template', true) == 'templates/list-page.php') {
                array_push($css_class_list, 'current');
            }

            /**
             * If the current items parent is set as PRIVATE(and should not be visible in menus)
             * The private parent should be set as current instead.
             *
             * Example with ancestors:
             *     25    5220         5776            5781          5785
             *  (root)  (node)  (set to current)   (private)   (actual current)
             *
             * http://localhost/startsida/omsorg-och-stod/
             * frivilligt-arbete-och-foreningar/info/las-mer-om-socialt-arbete-med-ersattning/
             */
            if (get_post_status($_current_page->post_parent) == 'private'
                && in_array($page->ID, $_current_page->ancestors)) {
                $_current_page_ansectors = $_current_page->ancestors;
                $last_element = count($_current_page_ansectors) - 1; // We want last index

                // -2 for seletion of grandparent
                $selector = $last_element > 1 ? $_current_page_ansectors[$last_element-2] : 0;

                if ($selector && $page->ID == $selector) {
                    array_push($css_class_list, 'current');
                }
            }
        }

        /**
         * $css_class_list into string
         * @var string
         */
        $css_classes = '';
        if (count($css_class_list) > 0) {
            $css_classes = 'class="' . implode(' ', $css_class_list) . '"';
        }

        /**
         * Build the item markup
         */
        $output .= sprintf(
            '<li %s><a href="%s">%s</a>',
            $css_classes,
            get_permalink($page->ID),
            apply_filters('the_title', $page->post_title, $page->ID)
        );
    }

    /**
     * End an element
     * @param  string  &$output
     * @param  string  $page
     * @param  integer $depth
     * @param  array   $args
     * @param  integer $current_page
     * @return void
     */
    public function end_el(&$output, $page, $depth = 0, $args = array())
    {
        $output .= '</li>';
    }
}
