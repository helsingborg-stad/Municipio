<?php

namespace Municipio\Controller\Navigation\Decorators\Menu;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\MenuInterface;
use WpService\Contracts\GetPermalink;

/**
 * Append menu items href
 */
class PageTreeAppendMenuItemsHref implements MenuInterface
{
    /**
     * Constructor
     */
    public function __construct(private MenuInterface $inner, private GetPermalink $wpService)
    {
    }

    /**
     * Retrieves the menu with appended menu items href.
     *
     * @return array The menu with appended menu items href.
     */
    public function getMenu(): array
    {
        $menu = $this->inner->getMenu();

        if (empty($menu['items'])) {
            return $menu;
        }

        foreach ($menu['items'] as &$menuItem) {
            if (!empty($menuItem['isCached']) || !empty($menuItem['href'])) {
                continue;
            }

            $menuItem['href'] = $this->wpService->getPermalink($menuItem['id'], false);
        }

        return $menu;
    }

    /**
     * Retrieves the menu configuration.
     *
     * @return MenuConfigInterface The menu configuration.
     */
    public function getConfig(): MenuConfigInterface
    {
        return $this->inner->getConfig();
    }
}
