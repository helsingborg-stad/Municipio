<?php

namespace Municipio\Controller\Navigation\Decorators\Menu;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\MenuInterface;

class AddColorStyles implements MenuInterface
{
    public function __construct(private MenuInterface $inner)
    {
    }

    public function getMenu(): array
    {
        $menu = $this->inner->getMenu();

        if (empty($menu['fields']['menu_advanced_color_settings'])) {
            return $menu;
        }

        $styleString = '';
        $fields = $menu['fields'];

        if (!empty($fields['menu_background_color'])) {
            $styleString = $this->buildStyleForVerticalAndHorizontalMenu($styleString, 'background-color', $fields['menu_background_color']);
        }

        return $menu;
    }

    private function buildStyleForVerticalAndHorizontalMenu(string $styleString, string $name, string $value): string
    {
        echo '<pre>' . print_r( $styleString, true ) . '</pre>';
        echo '<pre>' . print_r( $name, true ) . '</pre>';
        echo '<pre>' . print_r( $value, true ) . '</pre>';
        $styleString .= '--c-nav-h-' . $name . ': ' . $value . ';';

        return $styleString;
    }

    public function getConfig(): MenuConfigInterface
    {
        return $this->inner->getConfig();
    }
}