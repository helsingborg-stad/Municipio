<?php

namespace Municipio\Helper\Navigation;

class MenusSettings
{
    private array $menusSettings = [];
    private const MENU_SETTINGS_KEY = 'nav_menus_settings';
    private const ADDITIONAL_MENUS_KEY = 'additional_menus';

    public function __construct()
    {
        add_action('wp_update_nav_menu', array($this, 'updateMenuSettings'), 10, 2);
        add_filter('acf/prepare_field/name=menu_location', array($this, 'addMenuLocationField'), 10, 1);
    }

    public function updateMenuSettings($menuId, $menuData = null)
    {
        $fields = get_fields(wp_get_nav_menu_object($menuId));
        $this->menusSettings = get_option(self::MENU_SETTINGS_KEY) ?: [];
        $this->updateAdditionalMenuItems($menuId, $fields);

        update_option(self::MENU_SETTINGS_KEY, $this->menusSettings);
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

    public function updateAdditionalMenuItems($menuId, $fields): void
    {
        $additionalMenus = $this->menusSettings[self::ADDITIONAL_MENUS_KEY] ?? [];

        if (empty($fields['menu_location'])) {
            return;
        }

        foreach ($fields['menu_location'] as $showOnLocation) {
            if (!isset($additionalMenus[$showOnLocation]) || !is_array($additionalMenus[$showOnLocation])) {
                $additionalMenus[$showOnLocation] = [];
            }

            $additionalMenus[$showOnLocation][$menuId] = $menuId;
        }

        $this->menusSettings[self::ADDITIONAL_MENUS_KEY] = $additionalMenus;
    }
}
