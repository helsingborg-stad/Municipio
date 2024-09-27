<?php

namespace Municipio\Controller\Navigation\Decorators\Default;

use Municipio\Controller\Navigation\Decorators\MenuItemsDecoratorInterface;
use Municipio\Controller\Navigation\Helper\GetAncestorIds;
use Municipio\Controller\Navigation\Config\MenuConfigInterface;

class ComplementDefaultDecorator implements MenuItemsDecoratorInterface
{

    public function __construct(
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

        $ancestors = GetAncestorIds::getAncestorIds($menuItems, $menuConfig);
        die;

        foreach ($menuItems as &$menuItem) {
            foreach ($this->decorators as $decorator) {
                $menuItem = $decorator->decorate($menuItem, $menuConfig, $ancestors);
            }
        }

        return $menuItems;
    }

    public static function factory(array $decorators): self
    {
        return new self($decorators);
    }
}
