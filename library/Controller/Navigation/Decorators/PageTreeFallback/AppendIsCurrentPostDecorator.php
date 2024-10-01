<?php

namespace Municipio\Controller\Navigation\Decorators\PageTreeFallback;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;

class AppendIsCurrentPostDecorator implements PageTreeFallbackMenuItemDecoratorInterface
{
    /**
     * Decorates a menu item with the "active" flag based on the current page or URL.
     *
     * @param array $menuItem The menu item to decorate.
     * @param MenuConfigInterface $menuConfig The menu configuration.
     * @param ComplementPageTreeDecorator $parentInstance The parent decorator instance.
     * @return array The decorated menu item.
     */
    public function decorate(array $menuItem, MenuConfigInterface $menuConfig, ComplementPageTreeDecorator $parentInstance): array
    {
        if ($menuItem['id'] == $menuConfig->getPageId()) {
            $menuItem['active'] = true;
        } elseif (\Municipio\Helper\IsCurrentUrl::isCurrentUrl($menuItem['href'])) {
            $menuItem['active'] = true;
        } else {
            $menuItem['active'] = false;
        }

        return $menuItem;
    }
}
