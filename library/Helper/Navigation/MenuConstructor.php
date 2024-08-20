<?php

namespace Municipio\Helper\Navigation;

class MenuConstructor {
    public function __construct(private string $identifier = "")
    {}

    /**
     * Get WordPress menu items (from default menu management)
     *
     * @param string $menu The menu id to get
     * @return bool|array
     */
    public function structureMenuItems(array $menuItems, int $pageId = null): array
    {
        $result = [];

        if (!empty($menuItems)) {
            $ancestors = $this->getWpMenuAncestors(
                $menuItems, 
                $this->pageIdToMenuID($menuItems, $pageId)
            );

            foreach ($menuItems as $item) {
                $isAncestor = in_array($item->ID, $ancestors);
                $result[$item->ID] = $this->prepareMenuItem($item, $isAncestor, $pageId, $this->identifier);
            }
        }

        return $result;
    }


    /**
     * Recusivly traverse flat array and make a nested variant
     *
     * @param   array   $elements    A list of pages
     * @param   integer $parentId    Parent id
     *
     * @return  array               Nested array representing page structure
     */
    public function buildStructuredMenu(array $structuredMenuItems, $parentId = 0): array
    {
        $branch = array();

        if (is_array($structuredMenuItems) && !empty($structuredMenuItems)) {
            foreach ($structuredMenuItems as $structuredMenuItem) {
                if (!isset($structuredMenuItem['post_parent']) || !isset($structuredMenuItem['id'])) {
                    continue;
                }

                if ($structuredMenuItem['post_parent'] == $parentId) {
                    $children = $this->buildStructuredMenu($structuredMenuItems, $structuredMenuItem['id']);

                    if ($children) {
                        $structuredMenuItem['children'] = $children;
                    }

                    $branch[] = $structuredMenuItem;
                }
            }
        }

        return $branch;
    }

    /**
     * Prepare menu item
     * 
     * @param object $item
     * @param bool $isAncestor
     * @param int $pageId
     * 
     * @return array
     */
    private function prepareMenuItem($item, $isAncestor, $pageId): array
    {
        return apply_filters('Municipio/Navigation/Item', [
            'id'          => $item->ID,
            'post_parent' => $item->menu_item_parent,
            'post_type'   => $item->object,
            'page_id'     => $item->object_id,
            'active'      => ($item->object_id == $pageId) || \Municipio\Helper\IsCurrentUrl::isCurrentUrl($item->url),
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
            'xfn'         => $item->xfn ?? false
        ], $this->identifier, true);
    }

    /**
     * Translates a page id to a menu id
     *
     * @param array $menu
     * @param integer $pageId
     * @return integer
     */
    private function pageIdToMenuID($menu, $pageId)
    {
        $index = array_search($pageId, array_column($menu, 'object_id'));

        if ($index !== false) {
            return $menu[$index]->ID;
        }

        return false;
    }

    /**
     * Get a list of menu items with an ancestor relation to page id.
     *
     * @param string $menu The menu id to get
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