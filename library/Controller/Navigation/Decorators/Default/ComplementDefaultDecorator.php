<?php

namespace Municipio\Controller\Navigation\Decorators\Default;

use Municipio\Controller\Navigation\Decorators\Default\TransformMenuItemDecorator;
use Municipio\Controller\Navigation\Decorators\MenuItemsDecoratorInterface;
use Municipio\Controller\Navigation\Helper\GetAncestorIds;
use Municipio\Controller\Navigation\Config\MenuConfigInterface;


class ComplementDefaultDecorator implements MenuItemsDecoratorInterface
{

    public function __construct(
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
    public function decorate(array $menuItems, MenuConfigInterface $menuConfig): array
    {
        
        if (empty($menuItems)) {
            return $menuItems;
        }
        
        $ancestors = $this->getAncestorIds->get($menuItems, $menuConfig);

        foreach ($menuItems as &$menuItem) {
            // Converting the menu item object to an array as the first step.
            $menuItem = $this->transformMenuItemDecoratorInstance->decorate($menuItem, $menuConfig, $ancestors);

            foreach ($this->decorators as $decorator) {
                $menuItem = $decorator->decorate($menuItem, $menuConfig, $ancestors);
            }
        }

        return $menuItems;
    }
}
