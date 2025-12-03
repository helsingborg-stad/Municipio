<?php

namespace Municipio\Controller\Navigation\Decorators\Menu;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\Helper\GetAncestors;
use Municipio\Controller\Navigation\MenuInterface;

/**
 * Append menu items ancestors
 */
class PageTreeAppendMenuItemsAncestors implements MenuInterface
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

        if (empty($menu['items'])) {
            return $menu;
        }

        foreach ($menu['items'] as &$menuItem) {
            if (!isset($menuItem['id']) || !empty($menuItem['isCached']) || $menuItem['ancestor'] === true) {
                continue;
            }

            if (in_array($menuItem['id'], GetAncestors::getAncestors())) {
                $menuItem['ancestor'] = true;
            } else {
                $menuItem['ancestor'] = false;
            }
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
