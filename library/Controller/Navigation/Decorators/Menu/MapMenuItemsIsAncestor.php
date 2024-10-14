<?php

namespace Municipio\Controller\Navigation\Decorators\Menu;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\Helper\GetAncestorIds;
use Municipio\Controller\Navigation\MenuInterface;

/**
 * Map menu items is ancestor
 */
class MapMenuItemsIsAncestor implements MenuInterface
{
    /*
    * Constructor
    */
    public function __construct(private MenuInterface $inner)
    {
    }

    /**
     * Retrieves the menu with appended data from ancestor IDs.
     *
     * @return array The menu with appended data.
     */
    public function getMenu(): array
    {
        $menu = $this->inner->getMenu();

        $ancestors = GetAncestorIds::getAncestorIds($menu['items'], $this->getConfig()->getIdentifier());

        if (empty($menu['items'])) {
            return $menu;
        }

        foreach ($menu['items'] as &$menuItem) {
            if (!isset($menuItem['id']) || empty($ancestors)) {
                $menuItem['ancestor'] = false;
                continue;
            }

            $menuItem['ancestor'] = in_array($menuItem['id'], $ancestors);
        }


        return $menu;
    }

    /**
     * Retrieves the menu configuration.
     *
     * @return MenuConfigInterface The menu configuration.
     */
    public function getConfig(): MenuConfigInterface
    {
        return $this->inner->getConfig();
    }
}
