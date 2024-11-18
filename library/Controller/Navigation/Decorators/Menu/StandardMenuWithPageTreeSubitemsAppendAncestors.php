<?php

namespace Municipio\Controller\Navigation\Decorators\Menu;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\Helper\GetAncestors;
use Municipio\Controller\Navigation\Helper\GetPageForPostTypeIds;
use Municipio\Controller\Navigation\Helper\GetPostsByParent;
use Municipio\Controller\Navigation\MenuInterface;
use Municipio\Helper\CurrentPostId;
use WpService\Contracts\GetPostType;

class StandardMenuWithPageTreeSubitemsAppendAncestors implements MenuInterface
{
    private string $masterPostType = 'page';
    
    public function __construct(private MenuInterface $inner, private GetPostType $wpService)
    {
    }

    public function getMenu(): array
    {
        $menu = $this->inner->getMenu();

        if (empty($menu['items'])) {
            return $menu;
        }

        $ancestorIds = $this->getAncestorIds($menu['items']);

        if (empty($ancestorIds)) {
            return $menu;
        }

        $pageForPostTypes = GetPageForPostTypeIds::getPageForPostTypeIds();
        [$ancestorIds, $postTypesArray] = $this->getPostTypesArray($ancestorIds, $pageForPostTypes, $menu['items']);

        $ancestorPosts = GetPostsByParent::getPostsByParent($ancestorIds, $postTypesArray);

        $menu['items'] = array_merge($menu['items'], $ancestorPosts);

        return $menu;
    }

    private function getPostTypesArray(array $ancestorIds, array $pageForPostTypes, array $menuItems): array
    {
        $ancestorPageForPostType = array_intersect_key($pageForPostTypes, $ancestorIds);
        $postTypesArray = [];

        foreach ($menuItems as $menuItem) {
            if (isset($ancestorPageForPostType[$menuItem['id']]) && empty($menuItem['children'])) {
                $postTypesArray[] = $ancestorPageForPostType[$menuItem['id']];
                $ancestorIds[0] = 0;
                unset($ancestorIds[$menuItem['id']]);
            }
        }

        return [
            $ancestorIds, 
            !empty($postTypesArray) ?
            $postTypesArray : 
            [$this->wpService->getPostType(), $this->masterPostType]
        ];
    }

    private function getAncestorIds(array $menuItems): array
    {
        $ancestors = GetAncestors::getAncestors();
        $ancestors = array_combine($ancestors, $ancestors);
        unset($ancestors[0]);

        foreach ($menuItems as $menuItem) {
            if ($menuItem['children']) {
                unset($ancestors[$menuItem['id']]);
            }
        }

        return $ancestors;
    }

    public function getConfig(): MenuConfigInterface
    {
        return $this->inner->getConfig();
    }
}