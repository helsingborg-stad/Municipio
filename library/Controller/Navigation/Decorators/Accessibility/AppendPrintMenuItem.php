<?php

namespace Municipio\Controller\Navigation\Decorators\Accessibility;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\MenuInterface;
use WpService\Contracts\IsPage;
use WpService\Contracts\IsSingle;

class AppendPrintMenuItem implements MenuInterface
{
    public function __construct(private MenuInterface $inner, private IsPage&IsSingle $wpService)
    {
    }

    public function getMenu(): array
    {
        $menu = $this->inner->getMenu();

        if ($this->wpService->isSingle() || $this->wpService->isPage()) {
            $menu['items']['print'] =  [
                'icon'   => 'print',
                'href'   => '#',
                'script' => 'window.print();return false;',
                'text'   => __('Print', 'municipio'),
                'label'  => __('Print this page', 'municipio')
            ];
        }

        return $menu;
    }

    public function getConfig(): MenuConfigInterface
    {
        return $this->inner->getConfig();
    }
}
