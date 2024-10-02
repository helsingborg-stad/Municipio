<?php

namespace Municipio\Controller\Navigation\Decorators\Accessibility;

use Municipio\Controller\Navigation\Decorators\MenuItemsDecoratorInterface;
use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use WpService\Contracts\ApplyFilters;

class ApplyAccessibilityItemsFilterDecorator implements MenuItemsDecoratorInterface
{
    public function __construct(private ApplyFilters $wpService)
    {
    }

    public function decorate(array $menuItems, MenuConfigInterface $menuConfig): array
    {
        return $this->wpService->applyFilters('Municipio/Accessibility/Items', $menuItems);
    }
}