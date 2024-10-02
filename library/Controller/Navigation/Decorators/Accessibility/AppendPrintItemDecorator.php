<?php

namespace Municipio\Controller\Navigation\Decorators\Accessibility;

use Municipio\Controller\Navigation\Decorators\MenuItemsDecoratorInterface;
use Municipio\Controller\Navigation\Config\MenuConfigInterface;

class AppendPrintItemDecorator implements MenuItemsDecoratorInterface
{
    public function __construct()
    {
    }

    public function decorate(array $menuItems, MenuConfigInterface $menuConfig): array
    {
        if (is_single() || is_page()) {
            $menuItems['print'] =  [
                'icon'   => 'print',
                'href'   => '#',
                'script' => 'window.print();return false;',
                'text'   => __('Print', 'municipio'),
                'label'  => __('Print this page', 'municipio')
            ];
        }

        return $menuItems;
    }
}