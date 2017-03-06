<?php

namespace Municipio\Walker;

class MainMenuSecondary extends \Walker_Nav_Menu
{
    public function walk($elements, $max_depth)
    {
        global $post;

        $current = null;

        $output = '';
        $args = array_slice(func_get_args(), 2);
        $argsObj = $args[0];

        $topLevel = array();
        $subLevel = array();
        $emptyArr = array();

        // Find toplevel adn sublevel
        foreach ($elements as $element) {
            if ($element->object_id == $post->ID) {
                $current = $element;
            }

            if ($element->menu_item_parent == 0) {
                $topLevel[] = $element;
                continue;
            }

            $subLevel[$element->menu_item_parent][] = $element;
        }

        foreach ($topLevel as $item) {
            $this->display_element($item, $emptyArr, $max_depth, 0, $args, $output);
        }

        $output = sprintf(
            $argsObj->items_section_wrap,
            esc_attr($argsObj->menu_id),
            esc_attr($argsObj->menu_class),
            $output
        );

        // Split where items should be.
        // [0] => Start tag
        // [1] => End tag
        $subWrap = explode('%3$s', $argsObj->items_section_wrap);

        if (isset($subLevel[$current->ID])) {
            $output .= sprintf(
                $subWrap[0],
                esc_attr($argsObj->menu_id) . '-sublevel',
                esc_attr($argsObj->menu_class) . ' nav-sublevel'
            );

            foreach ($subLevel[$current->ID] as $item) {
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
