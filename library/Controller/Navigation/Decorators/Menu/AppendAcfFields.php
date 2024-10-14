<?php

namespace Municipio\Controller\Navigation\Decorators\Menu;

use AcfService\Contracts\GetFields;
use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\MenuInterface;

class AppendAcfFields implements MenuInterface
{
    public function __construct(private MenuInterface $inner, private GetFields $acfService)
    {
    }

    public function getMenu(): array
    {
        $menu = $this->inner->getMenu();

        if (empty($menu['items'])) {
            return $menu;
        }

        $menuObject = wp_get_nav_menu_object($this->getConfig()->getMenuName());

        if (empty($menuObject)) {
            return $menu;
        }

        $menu['fields'] = $this->acfService->getFields($menuObject);

        return $menu;
    }

    public function getConfig(): MenuConfigInterface
    {
        return $this->inner->getConfig();
    }
}