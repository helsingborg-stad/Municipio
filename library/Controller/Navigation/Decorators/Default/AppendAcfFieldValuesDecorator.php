<?php

namespace Municipio\Controller\Navigation\Decorators\Default;

class AppendAcfFieldValuesDecorator implements DefaultMenuItemDecoratorInterface
{
    public function decorate(array|object $menuItem, array $ancestors): array
    {
        $fields = get_fields($menuItem['id']);

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