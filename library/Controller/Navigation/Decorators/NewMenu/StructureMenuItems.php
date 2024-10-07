<?php

namespace Municipio\Controller\Navigation\Decorators\NewMenu;

use Municipio\Controller\Navigation\Config\NewMenuConfigInterface;
use Municipio\Controller\Navigation\NewMenuInterface;

class StructureMenuItems implements NewMenuInterface
{
    public function __construct(private NewMenuInterface $inner)
    {
    }

    public function getMenuItems(): array
    {
        $menuItems = $this->inner->getMenuItems();

        return $this->structureMenuItems($menuItems);

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

    public function getConfig(): NewMenuConfigInterface
    {
        return $this->inner->getConfig();
    }
}