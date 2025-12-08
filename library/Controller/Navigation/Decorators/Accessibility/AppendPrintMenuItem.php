<?php

namespace Municipio\Controller\Navigation\Decorators\Accessibility;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\MenuInterface;
use WpService\Contracts\IsPage;
use WpService\Contracts\IsSingle;

/**
 * Append print menu item
 */
class AppendPrintMenuItem implements MenuInterface
{
    /**
     * Constructor
     */
    public function __construct(private MenuInterface $inner, private IsPage&IsSingle $wpService)
    {
    }

    /**
     * Retrieves the menu with appended print menu item.
     *
     * @return array The menu with appended print menu item.
     */
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
