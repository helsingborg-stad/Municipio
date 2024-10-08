<?php

namespace Municipio\Controller\Navigation\Decorators\NewMenu;

use Municipio\Controller\Navigation\Config\NewMenuConfigInterface;
use Municipio\Controller\Navigation\Helper\GetPostsByParent;
use Municipio\Controller\Navigation\NewMenuInterface;
use WpService\Contracts\GetPostType;

class AppendDataFromAncestorIds implements NewMenuInterface
{
    private $masterPostType = 'page';

    public function __construct(private NewMenuInterface $inner, private GetPostType $wpService)
    {
    }

    public function getMenuItems(): array
    {
        $menuItems = $this->inner->getMenuItems();
        $menuItems = GetPostsByParent::getPostsByParent($menuItems, [$this->masterPostType, $this->wpService->getPostType()]);

        return $menuItems;
    }

    public function getConfig(): NewMenuConfigInterface
    {
        return $this->inner->getConfig();
    }
}