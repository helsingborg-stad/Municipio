<?php

namespace Municipio\Helper\Navigation;

class GetMenuData
{
    private static $menuLocations  = null;
    private static $menuItemsArray = [];

    public static function getNavMenuItems(false|int|string $identifier = null): array|false
    {
        if (is_string($identifier)) {
            $identifier = self::getNavMenuId($identifier);
        }

        if (empty($identifier)) {
            return false;
        }

        if (!isset(self::$menuItemsArray[$identifier])) {
            self::$menuItemsArray[$identifier] = wp_get_nav_menu_items($identifier);
        }

        return self::$menuItemsArray[$identifier];
    }

    public static function getNavMenuId(?string $identifier = null): int|false
    {
        $menuLocations = self::getNavMenuLocations();

        return !empty($menuLocations[$identifier]) ?
            $menuLocations[$identifier] :
            false;
    }

    public static function getNavMenuLocations()
    {
        if (is_null(self::$menuLocations)) {
            self::$menuLocations = get_nav_menu_locations();
        }

        return self::$menuLocations;
    }

    public static function getNavMenuObject(?string $identifier = null): WP_Term|false
    {
        $menuLocations = self::getNavMenuLocations();

        return !empty($menuLocations[$identifier]) ?
            wp_get_nav_menu_object($menuLocations[$identifier]) :
            false;
    }
}
