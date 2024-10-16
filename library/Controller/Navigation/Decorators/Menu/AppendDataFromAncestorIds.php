<?php

namespace Municipio\Controller\Navigation\Decorators\Menu;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\Helper\GetPageForPostTypeIds;
use Municipio\Controller\Navigation\Helper\GetPostsByParent;
use Municipio\Controller\Navigation\MenuInterface;
use WpService\Contracts\GetPostType;

/**
 * Append data from ancestor ids
 */
class AppendDataFromAncestorIds implements MenuInterface
{
    private $masterPostType = 'page';

    /**
     * Constructor
     */
    public function __construct(private MenuInterface $inner, private GetPostType $wpService)
    {
    }

    /**
     * Retrieves the menu with appended data from ancestor IDs.
     *
     * @return array The menu with appended data.
     */
    public function getMenu(): array
    {
        $menu = $this->inner->getMenu();

        $pageForPostTypes = GetPageForPostTypeIds::getPageForPostTypeIds();
        $pageId           = $this->getConfig()->getFallbackToPageTree();

        $postType = is_int($pageId) ?
            ($pageForPostTypes[$pageId] ?? $this->wpService->getPostType($pageId)) :
            $this->wpService->getPostType();

        if (isset($pageForPostTypes[$pageId])) {
            $menu['items'] = [0];
        }

        $menu['items'] = GetPostsByParent::getPostsByParent($menu['items'], [!empty($postType) ? $postType : $this->masterPostType]);

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
