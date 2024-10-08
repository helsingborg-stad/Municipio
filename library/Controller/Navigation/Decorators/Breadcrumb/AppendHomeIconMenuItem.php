<?php

namespace Municipio\Controller\Navigation\Decorators\Breadcrumb;

use Municipio\Controller\Navigation\Config\NewMenuConfigInterface;
use Municipio\Controller\Navigation\NewMenuInterface;
use WpService\Contracts\GetOption;

class AppendHomeIconMenuItem implements NewMenuInterface
{
    public function __construct(private NewMenuInterface $inner, private GetOption $wpService)
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

    public function getConfig(): NewMenuConfigInterface
    {
        return $this->inner->getConfig();
    }
}