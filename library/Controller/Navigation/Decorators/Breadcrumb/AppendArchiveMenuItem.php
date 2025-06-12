<?php

namespace Municipio\Controller\Navigation\Decorators\Breadcrumb;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\MenuInterface;
use Municipio\Helper\CurrentPostId;
use WpService\Contracts\GetPostType;
use WpService\Contracts\GetPostTypeArchiveLink;
use WpService\Contracts\GetPostTypeObject;
use WpService\Contracts\GetQueriedObject;
use WpService\Contracts\IsArchive;

/**
 * Append archive menu item
 */
class AppendArchiveMenuItem implements MenuInterface
{
    /**
     * Constructor
     */
    public function __construct(private MenuInterface $inner, private GetPostType&GetPostTypeObject&GetPostTypeArchiveLink&IsArchive&GetQueriedObject $wpService)
    {
    }

    /**
     * Retrieves the menu with appended archive menu item.
     *
     * @return array The menu with appended archive menu item.
     */
    public function getMenu(): array
    {
        $menu = $this->inner->getMenu();

        if ($this->wpService->isArchive()) {
            $queriedObject = $this->wpService->getQueriedObject();
            $postType = is_object($queriedObject) ? $queriedObject->name : null;
        } else {
            $postType = $this->wpService->getPostType(CurrentPostId::get());
        }

        $archiveLink = $this->wpService->getPostTypeArchiveLink($postType);

        if ($archiveLink) {
            $defaultLabel = __("Untitled page", 'municipio');

            if ($this->wpService->isArchive()) {
                $label = $this->wpService->getQueriedObject()->label ?? $defaultLabel;
            } else {
                $label = $this->wpService->getPostTypeObject($postType)->label ?? $defaultLabel;
            }

            $menu['items'][] = [
                'label'   => __($label),
                'href'    => $archiveLink,
                'current' => false,
                'icon'    => 'chevron_right'
            ];
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
