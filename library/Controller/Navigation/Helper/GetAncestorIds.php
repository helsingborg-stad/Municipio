<?php

namespace Municipio\Controller\Navigation\Helper;

class GetAncestorIds
{
    private ?array $ancestorIds = null;

    public function __construct(
        private int $pageId
    ) {}

    public function get(array $menuItems): array
    {
        if (!is_null($this->ancestorIds)) {
            return $this->ancestorIds;
        }

        $this->ancestorIds = $this->getWpMenuAncestors(
            $menuItems,
            $this->pageIdToMenuID($menuItems)
        );

        return $this->ancestorIds;
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
    private function getWpMenuAncestors($menuItems, $id)
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