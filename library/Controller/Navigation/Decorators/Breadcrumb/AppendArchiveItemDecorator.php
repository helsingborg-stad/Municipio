<?php

namespace Municipio\Controller\Navigation\Decorators\Breadcrumb;

use Municipio\Controller\Navigation\Decorators\MenuItemsDecoratorInterface;
use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\Helper\GetPageForPostTypeIds;
use WpService\Contracts\GetOption;
use WpService\Contracts\GetPostTypeObject;
use WpService\Contracts\GetPostType;

class AppendArchiveItemDecorator implements MenuItemsDecoratorInterface
{
    public function __construct(private GetOption&GetPostTypeObject&GetPostType $wpService)
    {
    }

    public function decorate(array $menuItems, MenuConfigInterface $menuConfig): array
    {
        $postType           = $this->wpService->getPostType($menuConfig->getPageId());
        $archiveLink        = get_post_type_archive_link($postType);
        
        if ($archiveLink) {
            $defaultLabel = __("Untitled page", 'municipio');
            
            if (is_archive()) {
                $label = get_queried_object()->label ?? $defaultLabel;
            } else {
                $label = $this->wpService->getPostTypeObject($postType)->label ?? $defaultLabel;
            }

            $menuItems[] = [
                'label'   => __($label),
                'href'    => $archiveLink,
                'current' => false,
                'icon'    => 'chevron_right'
            ];
        }

        return $menuItems;
    }
}