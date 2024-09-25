<?php

namespace Municipio\Controller\Navigation\Decorators;

use Municipio\Controller\Navigation\Decorators\MenuItemsDecoratorInterface;

class StructureMenuItemsDecorator implements MenuItemsDecoratorInterface
{
    /**
     * Recusivly traverse flat array and make a nested variant
     *
     * @param   array   $elements    A list of pages
     * @param   integer $parentId    Parent id
     *
     * @return  array               Nested array representing page structure
     */
    public function decorate(array $menuItems, bool $fallbackToPageTree, bool $includeTopLevel, bool $onlyKeepFirstLevel): array
    {
        return $this->structuredMenuItems($menuItems);
    }

    private function structuredMenuItems(array $menuItems, int $parentId = 0): array
    {
        $structuredMenuItems = [];

        if (is_array($menuItems) && !empty($menuItems)) {
            foreach ($menuItems as $menuItem) {
                if (!isset($menuItem['post_parent']) || !isset($menuItem['id'])) {
                    continue;
                }

                if ($menuItem['post_parent'] == $parentId) {
                    $children = $this->structuredMenuItems($menuItems, $menuItem['id']);

                    if ($children) {
                        $menuItem['children'] = $children;
                    }

                    $structuredMenuItems[] = $menuItem;
                }
            }
        }

        return $structuredMenuItems;
    }
}