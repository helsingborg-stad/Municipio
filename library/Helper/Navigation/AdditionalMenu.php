<?php

namespace Municipio\Helper\Navigation;

class AdditionalMenu
{
    private ?array $additionalMenusOption = null;
    public function __construct()
    {
        add_action('wp_update_nav_menu', array($this, 'updateAdditionalMenuItems'), 10, 2);
        add_filter('acf/prepare_field/name=menu_location', array($this, 'addMenuLocationField'), 10, 1);
    }

    public function addMenuLocationField($field)
    {
        $activeMenus      = get_nav_menu_locations();
        $field['choices'] = [];

        if (!empty($activeMenus)) {
            $allMenuLocations = \Municipio\Theme\Navigation::getMenuLocations();

            foreach ($activeMenus as $menuLocation => $menuId) {
                if (isset($allMenuLocations[$menuLocation])) {
                    $field['choices'][$menuLocation] = $allMenuLocations[$menuLocation];
                }
            }
        }

        return $field;
    }

    public function updateAdditionalMenuItems($menuId, $menuData = null)
    {
        $fields = get_fields(wp_get_nav_menu_object($menuId));

        if (empty($fields['menu_location'])) {
            return;
        }

        $additionalMenuItems = get_option('nav_menu_additional_items') ?? [];
        foreach ($fields['menu_location'] as $showOnLocation) {
            if (!isset($additionalMenuItems[$showOnLocation]) || !is_array($additionalMenuItems[$showOnLocation])) {
                $additionalMenuItems[$showOnLocation] = [];
            }

            $additionalMenuItems[$showOnLocation][$menuId] = $menuId;
        }

        update_option('nav_menu_additional_items', $additionalMenuItems);
    }
}
