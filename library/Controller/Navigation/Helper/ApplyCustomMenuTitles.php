<?php

namespace Municipio\Controller\Navigation\Helper;

class ApplyCustomMenuTitles
{
    public static function apply(array $menuItems): array
    {
        // Direct branches bypass the custom-title decorator used by the initial menu.
        update_meta_cache('post', array_column($menuItems, 'id'));
        foreach ($menuItems as &$menuItem) {
            foreach (get_post_meta($menuItem['id'], 'custom_menu_title', false) as $customTitle) {
                if (!empty($customTitle)) {
                    $menuItem['label'] = $customTitle;
                }
            }
        }

        return $menuItems;
    }
}
