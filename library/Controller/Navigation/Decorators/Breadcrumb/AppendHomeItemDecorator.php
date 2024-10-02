<?php

namespace Municipio\Controller\Navigation\Decorators\Breadcrumb;

use Municipio\Controller\Navigation\Decorators\MenuItemsDecoratorInterface;
use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use WpService\Contracts\GetOption;

class AppendHomeItemDecorator implements MenuItemsDecoratorInterface
{
    public function __construct(private GetOption $wpService)
    {
    }

    public function decorate(array $menuItems, MenuConfigInterface $menuConfig): array
    {
        $homeItemKey = $this->wpService->getOption('page_on_front');

        if (!empty($homeItemKey)) {
            $menuItems[$homeItemKey] = array(
                'label'   => __("Home"),
                'href'    => get_home_url(),
                'current' => is_front_page() ? true : false,
                'icon'    => 'home'
            );
        }

        return $menuItems;
    }
}