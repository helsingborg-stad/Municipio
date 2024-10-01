<?php

namespace Municipio\Controller\Navigation\Decorators\PageTreeFallback;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use WpService\Contracts\GetPermalink;

class AppendHrefDecorator implements PageTreeFallbackMenuItemDecoratorInterface
{
    public function __construct(private GetPermalink $wpService)
    {
    }

    /**
     * Decorates a menu item by appending the href attribute.
     *
     * @param array $menuItem The menu item to decorate.
     * @param MenuConfigInterface $menuConfig The menu configuration.
     * @param ComplementPageTreeDecorator $parentInstance The parent instance of the decorator.
     * @return array The decorated menu item.
     */
    public function decorate(array $menuItem, MenuConfigInterface $menuConfig, ComplementPageTreeDecorator $parentInstance): array
    {
        $menuItem['href'] = $this->wpService->getPermalink($menuItem['id'], false);

        return $menuItem;
    }
}
