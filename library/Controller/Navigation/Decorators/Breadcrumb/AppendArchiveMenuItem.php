<?php

namespace Municipio\Controller\Navigation\Decorators\Breadcrumb;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\MenuInterface;
use Municipio\Helper\CurrentPostId;
use WpService\Contracts\GetPostType;
use WpService\Contracts\GetPostTypeObject;

class AppendArchiveMenuItem implements MenuInterface
{
    public function __construct(private MenuInterface $inner, private GetPostType&GetPostTypeObject $wpService)
    {
    }

    public function getMenuItems(): array
    {
        $menuItems          = $this->inner->getMenuItems();

        $postType           = $this->wpService->getPostType(CurrentPostId::get());
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

    public function getMenu(): array
    {
        return $this->inner->getMenu();
    }

    public function getConfig(): MenuConfigInterface
    {
        return $this->inner->getConfig();
    }
}