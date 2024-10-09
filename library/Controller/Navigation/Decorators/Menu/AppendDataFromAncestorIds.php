<?php

namespace Municipio\Controller\Navigation\Decorators\Menu;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\Helper\GetPostsByParent;
use Municipio\Controller\Navigation\MenuInterface;
use WpService\Contracts\GetPostType;

class AppendDataFromAncestorIds implements MenuInterface
{
    private $masterPostType = 'page';

    public function __construct(private MenuInterface $inner, private GetPostType $wpService)
    {
    }

    public function getMenuItems(): array
    {
        $menuItems = $this->inner->getMenuItems();
        $menuItems = GetPostsByParent::getPostsByParent($menuItems, [$this->masterPostType, $this->wpService->getPostType()]);

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