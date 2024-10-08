<?php

namespace Municipio\Helper\Navigation;

/**
 * Class GetMenuData
 *
 * This class provides methods to retrieve menu data in WordPress.
 */
class GetMenuData
{
    private static $menuLocations      = null;
    private static $menuItemsArray     = [];
    private static $navMenuObjectArray = [];

    /**
     * Retrieves the menu items for a given navigation menu identifier.
     *
     * @param false|int|string $identifier The identifier of the navigation menu.
     * @return array|false The array of menu items or false if the identifier is empty.
     */
    public static function getNavMenuItems(null|false|int|string $identifier = null): array|false
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

    /**
     * Retrieves the ID of a navigation menu based on its identifier.
     *
     * @param string|null $identifier The identifier of the navigation menu. Default is null.
     * @return int|false The ID of the navigation menu if found, false otherwise.
     */
    public static function getNavMenuId(?string $identifier = null): int|false
    {
        $menuLocations = self::getNavMenuLocations();

        return !empty($menuLocations[$identifier]) ?
            $menuLocations[$identifier] :
            false;
    }

    /**
     * Retrieves the registered navigation menu locations.
     *
     * This method returns an array of registered navigation menu locations.
     * If the locations have not been retrieved yet, it calls the `get_nav_menu_locations()` function
     * to fetch the locations and stores them in a static variable for future use.
     *
     * @return array The registered navigation menu locations.
     */
    public static function getNavMenuLocations()
    {
        if (is_null(self::$menuLocations)) {
            self::$menuLocations = get_nav_menu_locations();
        }

        return self::$menuLocations;
    }

    /**
     * Retrieves the navigation menu object based on the provided identifier.
     *
     * @param string|null $identifier The identifier of the navigation menu. Default is null.
     * @return \WP_Term|false The navigation menu object if found, false otherwise.
     */
    public static function getNavMenuObject(false|int|string $identifier = ""): \WP_Term|false
    {
        $menuId = is_string($identifier) ? self::getNavMenuId($identifier) : $identifier;

        if (empty($menuId)) {
            return false;
        }

        if (!isset(self::$navMenuObjectArray[$menuId])) {
            self::$navMenuObjectArray[$menuId] = wp_get_nav_menu_object($menuId);
        }

        return self::$navMenuObjectArray[$menuId];
    }
}
