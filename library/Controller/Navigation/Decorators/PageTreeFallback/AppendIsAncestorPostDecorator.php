<?php

namespace Municipio\Controller\Navigation\Decorators\PageTreeFallback;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\Helper\GetAncestors;

class AppendIsAncestorPostDecorator implements PageTreeFallbackMenuItemDecoratorInterface
{
    /**
     * Decorates a menu item with information about whether it is an ancestor post.
     *
     * @param array $menuItem The menu item to decorate.
     * @param MenuConfigInterface $menuConfig The menu configuration.
     * @param ComplementPageTreeDecorator $parentInstance The parent instance of the decorator.
     * @return array The decorated menu item.
     */
    public function decorate(array $menuItem, MenuConfigInterface $menuConfig, ComplementPageTreeDecorator $parentInstance): array
    {
        if (in_array($menuItem['id'], GetAncestors::getAncestors())) {
            $menuItem['ancestor'] = true;
        } else {
            $menuItem['ancestor'] = false;
        }

        return $menuItem;
    }
}
