<?php

namespace Municipio\Controller\Navigation\Decorators\NewMenu;

use AcfService\Contracts\GetFields;
use Municipio\Controller\Navigation\Config\NewMenuConfigInterface;
use Municipio\Controller\Navigation\NewMenuInterface;

class MapMenuItemsAcfFieldValues implements NewMenuInterface
{
    public function __construct(private NewMenuInterface $inner, private GetFields $acfService)
    {
    }

    public function getMenuItems(): array
    {
        $menuItems = $this->inner->getMenuItems();

        if (empty($menuItems)) {
            return $menuItems;
        }

        foreach ($menuItems as &$menuItem) {
            $fields = $this->acfService->getFields($menuItem['id']);

            $menuItem['icon'] = [
                'icon'      => $fields['menu_item_icon'] ?? null,
                'size'      => 'md',
                'classList' => ['c-nav__icon']
            ];
    
            $menuItem['style']       = $fields['menu_item_style'] ?? 'default';
            $menuItem['description'] = $fields['menu_item_description'] ?? '';
        }


        return $menuItems;
    }

    public function getConfig(): NewMenuConfigInterface
    {
        return $this->inner->getConfig();
    }
}