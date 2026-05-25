<?php

namespace Municipio\Controller\Navigation\Decorators\Breadcrumb;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\MenuInterface;
use Municipio\Helper\CurrentPostId;
use WpService\Contracts\__ as Translate;
use WpService\Contracts\GetOption;
use WpService\Contracts\GetPostType;
use WpService\Contracts\GetPostTypeArchiveLink;
use WpService\Contracts\GetPostTypeObject;
use WpService\Contracts\GetQueriedObject;
use WpService\Contracts\GetTheTitle;
use WpService\Contracts\IsArchive;

/**
 * Append archive menu item
 */
class AppendArchiveMenuItem implements MenuInterface
{
    /**
     * Constructor
     */
    public function __construct(
        private MenuInterface $inner,
        private GetPostType&GetPostTypeObject&GetPostTypeArchiveLink&IsArchive&GetQueriedObject&GetTheTitle&GetOption&Translate $wpService,
    ) {}

    /**
     * Retrieves the menu with appended archive menu item.
     *
     * @return array The menu with appended archive menu item.
     */
    public function getMenu(): array
    {
        $menu = $this->inner->getMenu();
        $postType = $this->wpService->getPostType(CurrentPostId::get());
        $queriedObject = $this->wpService->getQueriedObject();

        if ($this->wpService->isArchive() && is_object($queriedObject)) {
            $postType = $queriedObject->name;
        }

        $archiveLink = $this->wpService->getPostTypeArchiveLink($postType);

        if ($archiveLink) {
            $defaultLabel = $this->wpService->__('Untitled page', 'municipio');

            if ($this->wpService->isArchive()) {
                $pageTitle = (string) $this->wpService->getTheTitle(CurrentPostId::get());
                $label = $pageTitle !== '' ? $pageTitle : $this->wpService->getQueriedObject()->label ?? $defaultLabel;
            } else {
                //Handle page for post type archive title if set, otherwise
                //fallback to post type label or default label
                $archivePageId = (int) $this->wpService->getOption('page_for_' . $postType);

                $pageTitle = $archivePageId > 0 ? (string) $this->wpService->getTheTitle($archivePageId) : '';
                $label = $pageTitle !== '' ? $pageTitle : $this->wpService->getPostTypeObject($postType)->label ?? $defaultLabel;
            }

            $menu['items'][] = [
                'label' => $this->wpService->__($label, 'municipio'),
                'href' => $archiveLink,
                'current' => false,
                'icon' => 'chevron_right',
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
