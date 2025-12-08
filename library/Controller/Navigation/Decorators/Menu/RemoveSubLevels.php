<?php

namespace Municipio\Controller\Navigation\Decorators\Menu;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\MenuInterface;

/**
 * Remove sub levels
 */
class RemoveSubLevels implements MenuInterface
{
    /**
     * Constructor
     */
    public function __construct(private MenuInterface $inner)
    {
    }

    /**
     * Retrieves the menu with removed sub levels.
     *
     * @return array The menu with removed sub levels.
     */
    public function getMenu(): array
    {
        $menu = $this->inner->getMenu();

        if ($this->getConfig()->getRemoveSubLevels()) {
            foreach ($menu['items'] as &$menuItem) {
                $menuItem['children'] = false;
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
