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
     * Decorates an array of menu items with the given decorators based on the provided menu configuration.
     *
     * @param array $menuItems The array of menu items to decorate.
     * @param MenuConfigInterface $menuConfig The menu configuration object.
     * @return array The decorated array of menu items.
     */
    public function decorate(array $menuItems, MenuConfigInterface $menuConfig): array
    {
        if (empty($menuItems)) {
            return $menuItems;
        }

        $ancestors = GetAncestorIds::getAncestorIds($menuItems, $menuConfig->getIdentifier());

        foreach ($menuItems as &$menuItem) {
            foreach ($this->decorators as $decorator) {
                $menuItem = $decorator->decorate($menuItem, $menuConfig, $ancestors);
            }
        }

        return $menuItems;
    }

    /**
     * Factory method for creating a ComplementDefaultDecorator instance.
     *
     * @param array $decorators An array of decorators.
     * @return self The created ComplementDefaultDecorator instance.
     */
    public static function factory(array $decorators): self
    {
        return new self($decorators);
    }
}
