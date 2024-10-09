<?php

namespace Municipio\Controller\Navigation\Decorators\Breadcrumb;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\MenuInterface;
use WpService\Contracts\GetOption;

class AppendHomeIconMenuItem implements MenuInterface
{
    public function __construct(private MenuInterface $inner, private GetOption $wpService)
    {
    }

    public function getMenuItems(): array
    {
        $menuItems = $this->inner->getMenuItems();
        $homeItemKey = $this->wpService->getOption('page_on_front');

        if (!empty($homeItemKey)) {
            $menuItems[$homeItemKey] = array(
                'label'   => __('Home', 'municipio'),
                'href'    => get_home_url(),
                'current' => is_front_page() ? true : false,
                'icon'    => 'home'
            );
        }

        return $menuItems;
    }

    public function getMenu(): array
    {
        return $this->inner->getMenu();
    }

    public function getConfig(): MenuConfigInterface
    {
        return $this->inner->getConfig();
    }
}