<?php

namespace Municipio\Controller\Navigation\Decorators\Menu;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\MenuInterface;

class StructureMenuItems implements MenuInterface
{
    public function __construct(private MenuInterface $inner)
    {
    }

    public function getMenuItems(): array
    {
        $menuItems = $this->inner->getMenuItems();
        if (is_bool($this->getConfig()->getFallbackToPageTree())) {
            $menuItems = $this->structureMenuItems($menuItems);
        }

        return $menuItems;

    }

    private function structureMenuItems(array $menuItems, int $parentId = 0): array {
    $structuredMenuItems = [];
    
    if (is_array($menuItems) && !empty($menuItems)) {
            foreach ($menuItems as $menuItem) {
                
                if (!isset($menuItem['post_parent']) || !isset($menuItem['id'])) {
                    continue;
                }

                if ($menuItem['post_parent'] == $parentId) {
                    $children = $this->structureMenuItems($menuItems, $menuItem['id']);

                    if ($children) {
                        $menuItem['children'] = $children;
                    }
                    
                    $structuredMenuItems[] = $menuItem;
                }
            }
        }

        return $structuredMenuItems;
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