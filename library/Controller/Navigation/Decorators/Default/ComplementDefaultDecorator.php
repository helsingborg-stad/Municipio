<?php

namespace Municipio\Controller\Navigation\Decorators\Default;

use Municipio\Controller\Navigation\Decorators\Default\TransformMenuItemDecorator;
use Municipio\Controller\Navigation\Decorators\MenuItemsDecoratorInterface;
use Municipio\Controller\Navigation\Helper\GetAncestorIds;


class ComplementDefaultDecorator implements MenuItemsDecoratorInterface
{

    public function __construct(
        private string $identifier, 
        private int $pageId,
        private $db,
        private TransformMenuItemDecorator $transformMenuItemDecoratorInstance,
        private GetAncestorIds $getAncestorIds,
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
        
        $ancestors = $this->getAncestorIds->get($menuItems);

        foreach ($menuItems as &$menuItem) {
            // Converting the menu item object to an array as the first step.
            $menuItem = $this->transformMenuItemDecoratorInstance->decorate($menuItem, $ancestors);

            foreach ($this->decorators as $decorator) {
                $menuItem = $decorator->decorate($menuItem, $ancestors);
            }
        }

        return $menuItems;
    }
}
