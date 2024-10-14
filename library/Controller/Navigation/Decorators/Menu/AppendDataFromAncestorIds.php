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

    public function getMenu(): array
    {
        $menu = $this->inner->getMenu();

        $postType = is_int($this->getConfig()->getFallbackToPageTree()) ? $this->wpService->getPostType($this->getConfig()->getFallbackToPageTree()) : $this->wpService->getPostType();

        $menu['items'] = GetPostsByParent::getPostsByParent($menu['items'], [!empty($postType) ? $postType : $this->masterPostType]);

        return $menu;
    }

    public function getConfig(): MenuConfigInterface
    {
        return $this->inner->getConfig();
    }
}
