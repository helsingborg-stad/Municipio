<?php

namespace Municipio\Controller\Navigation\Decorators\Default;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;

use AcfService\Contracts\GetFields;

class AppendAcfFieldValuesDecorator implements DefaultMenuItemDecoratorInterface
{
    public function __construct(private GetFields $acfService)
    {
        
    }
    /**
     * Decorates a menu item with additional field values.
     *
     * @param array|object $menuItem The menu item to decorate.
     * @param MenuConfigInterface $menuConfig The menu configuration.
     * @param array $ancestors The ancestors of the menu item.
     * @return array The decorated menu item.
     */
    public function decorate(array|object $menuItem, MenuConfigInterface $menuConfig, array $ancestors): array
    {
        $fields = $this->acfService->getFields($menuItem['id']);

        $menuItem['icon'] = [
            'icon' => $fields['menu_item_icon'] ?? null,
            'size'      => 'md',
            'classList' => ['c-nav__icon']
        ];

        $menuItem['style'] = $fields['menu_item_style'] ?? 'default';
        $menuItem['description'] = $fields['menu_item_description'] ?? '';

        return $menuItem;
    }
}