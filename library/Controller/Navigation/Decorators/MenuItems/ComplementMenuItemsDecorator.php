<?php

namespace Municipio\Controller\Navigation\Decorators\MenuItems;

class ComplementMenuItemsDecorator implements MenuItemsDecoratorInterface
{

    public function __construct(
        private string $identifier, 
        private int|false $id, 
        private string|false $name, 
        private int $pageId,
        private $db
    ) {
    }

    /**
     * Get WordPress menu items (from default menu management)
     *
     * @param string $menu The menu id to get
     * @return bool|array
     */
    public function decorate(array $menuItems, bool $fallbackToPageTree, bool $includeTopLevel, bool $onlyKeepFirstLevel): array
    {
        $result = [];

        if (!empty($menuItems)) {
            $ancestors = $this->getWpMenuAncestors(
                $menuItems,
                $this->pageIdToMenuID($menuItems)
            );

            foreach ($menuItems as $item) {
                $isAncestor        = in_array($item->ID, $ancestors);
                $result[$item->ID] = $this->prepareMenuItem($item, $isAncestor);
            }
        }

        return $result;
    }

    /**
     * Prepare menu item
     *
     * @param object $item
     * @param bool $isAncestor
     * @param int $this->pageId
     *
     * @return array
     */
    private function prepareMenuItem($item, $isAncestor): array
    {
        return apply_filters('Municipio/Navigation/Item', [
            'id'          => $item->ID,
            'post_parent' => $item->menu_item_parent,
            'post_type'   => $item->object,
            'page_id'     => $item->object_id,
            'active'      => ($item->object_id == $this->pageId) || \Municipio\Helper\IsCurrentUrl::isCurrentOrAncestorUrl($item->url),
            'ancestor'    => $isAncestor,
            'label'       => $item->title,
            'href'        => $item->url,
            'children'    => false,
            'icon'        => [
                'icon'      => get_field('menu_item_icon', $item->ID),
                'size'      => 'md',
                'classList' => ['c-nav__icon']
            ],
            'style'       => get_field('menu_item_style', $item->ID) ?? 'default',
            'description' => get_field('menu_item_description', $item->ID) ?? '',
            'top_level'   => $item->menu_item_parent == 0,
            'xfn'         => $item->xfn ?? false
        ], $this->identifier, true);
    }

       /**
     * Translates a page id to a menuItems id
     *
     * @param array $menuItems
     * @param integer $this->pageId
     * @return integer
     */
    private function pageIdToMenuID($menuItems)
    {
        $index = array_search($this->pageId, array_column($menuItems, 'object_id'));

        if ($index !== false) {
            return $menuItems[$index]->ID;
        }

        return false;
    }


    /**
     * Get a list of menu items with an ancestor relation to page id.
     *
     * @param array $menu The menu id to get
     * @return bool|array
     */
    private function getWpMenuAncestors($menu, $id)
    {
        if (!$id) {
            return [];
        }

        //Definitions
        $fetchAncestors = true;
        $ancestorStack  = [$id];

        //Fetch ancestors
        while ($fetchAncestors) {
            //Get index where match exists
            $parentIndex = array_search($id, array_column($menu, 'ID'));

            //Top level, exit
            if ($menu[$parentIndex]->menu_item_parent == 0) {
                $fetchAncestors = false;
            } else {
                //Add to stack (with duplicate prevention)
                $ancestorStack[] = (int) $menu[$parentIndex]->menu_item_parent;

                //Prepare for next iteration
                $id = (int) $menu[$parentIndex]->menu_item_parent;
            }
        }

        return $ancestorStack;
    }
}
