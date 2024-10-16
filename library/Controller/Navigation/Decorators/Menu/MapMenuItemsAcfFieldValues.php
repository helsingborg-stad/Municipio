<?php

namespace Municipio\Controller\Navigation\Decorators\Menu;

use AcfService\Contracts\GetFields;
use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\MenuInterface;

/**
 * Map menu items ACF field values
 */
class MapMenuItemsAcfFieldValues implements MenuInterface
{
    /**
     * Constructor
     */
    public function __construct(private MenuInterface $inner, private GetFields $acfService)
    {
    }

    /**
     * Retrieves the menu with applied nested menu items filters.
     *
     * @return array The menu with applied nested menu items filters.
     */
    public function getMenu(): array
    {
        $menu = $this->inner->getMenu();

        if (empty($menu['items'])) {
            return $menu;
        }

        foreach ($menu['items'] as &$menuItem) {
            $fields = $this->acfService->getFields($menuItem['id']);

            $menuItem['icon'] = [
                'icon'      => $fields['menu_item_icon'] ?? null,
                'size'      => 'md',
                'classList' => ['c-nav__icon']
            ];

            $menuItem['style']       = $fields['menu_item_style'] ?? 'default';
            $menuItem['description'] = $fields['menu_item_description'] ?? '';
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
