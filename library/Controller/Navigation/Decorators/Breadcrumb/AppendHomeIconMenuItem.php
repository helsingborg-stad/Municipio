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

    public function getMenu(): array
    {
        $menu = $this->inner->getMenu();
        $homeItemKey = $this->wpService->getOption('page_on_front');

        if (!empty($homeItemKey)) {
            $menu['items'][$homeItemKey] = array(
                'label'   => __('Home', 'municipio'),
                'href'    => get_home_url(),
                'current' => is_front_page() ? true : false,
                'icon'    => 'home'
            );
        }

        return $menu;
    }

    public function getConfig(): MenuConfigInterface
    {
        return $this->inner->getConfig();
    }
}