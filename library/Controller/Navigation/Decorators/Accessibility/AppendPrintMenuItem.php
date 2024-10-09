<?php

namespace Municipio\Controller\Navigation\Decorators\Accessibility;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\MenuInterface;

class AppendPrintMenuItem implements MenuInterface
{
    public function __construct(private MenuInterface $inner)
    {
    }

    public function getMenuItems(): array
    {
        $menuItems = $this->inner->getMenuItems();

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

    public function getMenu(): array
    {
        return $this->inner->getMenu();
    }

    public function getConfig(): MenuConfigInterface
    {
        return $this->inner->getConfig();
    }
}