<?php

namespace Municipio\Controller\Navigation\Decorators\Breadcrumb;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\MenuInterface;
use WpService\Contracts\GetHomeUrl;
use WpService\Contracts\GetOption;
use WpService\Contracts\IsFrontPage;

/**
 * Append home icon menu item
 */
class AppendHomeIconMenuItem implements MenuInterface
{
    /**
     * Constructor
     */
    public function __construct(private MenuInterface $inner, private GetOption&GetHomeUrl&IsFrontPage $wpService)
    {
    }

    /**
     * Retrieves the menu with applied home icon menu item.
     *
     * @return array The menu with applied home icon menu item.
     */
    public function getMenu(): array
    {
        $menu        = $this->inner->getMenu();
        $homeItemKey = $this->wpService->getOption('page_on_front');

        if (!empty($homeItemKey)) {
            $menu['items'][$homeItemKey] = array(
                'label'   => __('Home', 'municipio'),
                'href'    => $this->wpService->getHomeUrl(),
                'current' => $this->wpService->isFrontPage() ? true : false,
                'icon'    => 'home'
            );
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
