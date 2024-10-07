<?php

namespace Municipio\Controller\Navigation\Decorators\Accessibility;

use Municipio\Controller\Navigation\Config\NewMenuConfigInterface;
use Municipio\Controller\Navigation\NewMenuInterface;

class AppendPrintMenuItem implements NewMenuInterface
{
    public function __construct(private NewMenuInterface $inner)
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

    public function getConfig(): NewMenuConfigInterface
    {
        return $this->inner->getConfig();
    }
}