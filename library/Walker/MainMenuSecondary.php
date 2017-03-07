<?php

namespace Municipio\Walker;

class MainMenuSecondary extends \Walker_Nav_Menu
{
    public function walk($elements, $max_depth)
    {
        global $post;

        $current = null;
        $currentTopLevel = null;

        $output = '';
        $args = array_slice(func_get_args(), 2);
        $argsObj = $args[0];

        $topLevel = array();
        $subLevel = array();
        $emptyArr = array();

        $postAncestors = get_post_ancestors($post->ID);

        // Find toplevel and sublevel
        foreach ($elements as $element) {
            if ($element->object_id == $post->ID) {
                $current = $element;
            }

            if ($element->menu_item_parent == 0) {
                if ($element->object_id == $post->ID) {
                    $currentTopLevel = $element->ID;
                }

                $topLevel[] = $element;
                continue;
            }

            $subLevel[$element->menu_item_parent][] = $element;
        }

        foreach ($topLevel as $item) {
            $this->display_element($item, $emptyArr, $max_depth, 0, $args, $output);
        }

        // Find the current top level item
        $i = 0;
        $thisParent = null;
        $nextParent = $current->menu_item_parent;
        while (is_null($currentTopLevel)) {
            $i++;

            if ($i === 20) {
                break;
            }

            if ($nextParent == 0) {
                $currentTopLevel = $thisParent;
                break;
            }

            $thisParent = get_post($nextParent);
            $nextParent = get_post_meta($thisParent->ID, '_menu_item_menu_item_parent', true);
        }

        if (is_a($currentTopLevel, 'WP_Post')) {
            $currentTopLevel = $currentTopLevel->ID;
        }

        // Classes
        $classes = esc_attr($argsObj->menu_class);
        if (!is_null($currentTopLevel) && isset($subLevel[$currentTopLevel])) {
            $classes .= ' nav-has-sublevel';
        }

        $output = sprintf(
            $argsObj->items_section_wrap,
            esc_attr($argsObj->menu_id),
            $classes,
            $output
        );

        // Split where items should be.
        // [0] => Start tag
        // [1] => End tag
        $subWrap = explode('%3$s', $argsObj->items_section_wrap);

        if (!is_null($currentTopLevel) && isset($subLevel[$currentTopLevel])) {
            if ($current->ID === $currentTopLevel) {
                global $isSublevel;
                $isSublevel = true;
            }

            $output .= sprintf(
                $subWrap[0],
                esc_attr($argsObj->menu_id) . '-sublevel',
                esc_attr($argsObj->menu_class) . ' nav-sublevel'
            );

            foreach ($subLevel[$currentTopLevel] as $item) {
                $this->display_element($item, $emptyArr, $max_depth, 0, $args, $output);
            }

            $output .= sprintf(
                $subWrap[1],
                esc_attr($argsObj->menu_id) . '-sublevel',
                esc_attr($argsObj->menu_class) . ' nav-sublevel'
            );
        }

        return $output;
    }
}
