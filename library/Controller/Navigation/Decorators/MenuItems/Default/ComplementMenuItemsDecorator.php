<?php

namespace Municipio\Controller\Navigation\Decorators\MenuItems\Default;

use Municipio\Controller\Navigation\Decorators\MenuItem\Default\TransformToArrayDecorator;
use Municipio\Controller\Navigation\Decorators\MenuItems\MenuItemsDecoratorInterface;
use Municipio\Controller\Navigation\Helper\GetMenuItemAncestors;

class ComplementMenuItemsDecorator implements MenuItemsDecoratorInterface
{

    public function __construct(
        private string $identifier, 
        private int|false $id, 
        private int $pageId,
        private $db,
        private TransformToArrayDecorator $transformToArrayDecoratorInstance,
        private GetMenuItemAncestors $getMenuItemAncestors,
        private array $decorators = []
    ) {
    }

    /**
     * Get WordPress menu items (from default menu management)
     *
     * @param string $menu The menu id to get
     * @return bool|array
     */
    public function decorate(array $menuItems, bool $fallbackToPageTree, bool $includeTopLevel, bool $onlyKeepFirstLevel): array
    {
        
        if (empty($menuItems)) {
            return $menuItems;
        }
        
        $ancestors = $this->getMenuItemAncestors->getMenuItemAncestors($menuItems);

        foreach ($menuItems as &$menuItem) {
            // Converting the menu item object to an array as the first step.
            $menuItem = $this->transformToArrayDecoratorInstance->decorate($menuItem, $ancestors);

            foreach ($this->decorators as $decorator) {
                $menuItem = $decorator->decorate($menuItem, $ancestors);
            }
        }

        return $menuItems;
    }
}
