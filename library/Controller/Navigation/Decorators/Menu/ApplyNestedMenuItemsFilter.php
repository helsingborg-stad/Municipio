<?php

namespace Municipio\Controller\Navigation\Decorators\Menu;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\MenuInterface;
use WpService\Contracts\ApplyFilters;

class ApplyNestedMenuItemsFilter implements MenuInterface
{
    public function __construct(private MenuInterface $inner, private ApplyFilters $wpService)
    {
    }

    public function getMenu(): array
    {
        $menu = $this->inner->getMenu();

        if (empty($menu['items'])) {
            return $menu;
        }

        $menu['items'] = $this->wpService->applyFilters('Municipio/Navigation/Nested', $menu['items'], $this->getConfig()->getIdentifier(), $this->getConfig()->getMenuName());

        return $menu;
    }

    public function getConfig(): MenuConfigInterface
    {
        return $this->inner->getConfig();
    }
}