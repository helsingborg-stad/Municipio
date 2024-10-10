<?php

namespace Municipio\Controller\Navigation\Helper;

use Municipio\Helper\CurrentPostId;

class GetAncestorIds
{
    private static array $ancestorIds = [];

    public function __construct()
    {
    }

    public static function getAncestorIds(array $menuItems, string $identifier): array
    {
        if (empty(self::$ancestorIds[$identifier])) {
            return self::$ancestorIds;
        }

        self::$ancestorIds[$identifier] = self::getWpMenuAncestors(
            $menuItems,
            self::pageIdToMenuID($menuItems, CurrentPostId::get())
        );

        return self::$ancestorIds[$identifier];
    }

    /**
     * Translates a page id to a menuItems id
     *
     * @param array $menuItems
     * @param integer self::pageId
     * @return integer
     */
    private static function pageIdToMenuID($menuItems, int $pageId)
    {
        $index = array_search($pageId, array_column($menuItems, 'object_id'));

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
    private static function getWpMenuAncestors($menuItems, $id)
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
            $parentIndex = array_search($id, array_column($menuItems, 'ID'));

            //Top level, exit
            if ($menuItems[$parentIndex]->menu_item_parent == 0) {
                $fetchAncestors = false;
            } else {
                //Add to stack (with duplicate prevention)
                $ancestorStack[] = (int) $menuItems[$parentIndex]->menu_item_parent;

                //Prepare for next iteration
                $id = (int) $menuItems[$parentIndex]->menu_item_parent;
            }
        }

        return $ancestorStack;
    }
}
