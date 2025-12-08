<?php

namespace Municipio\Controller\Navigation\Helper;

use Municipio\Helper\CurrentPostId;

/**
 * Get ancestor ids
 */
class GetAncestorIds
{
    private static array $ancestorIds = [];

    /**
     * Get ancestor ids
     *
     * @param array $menuItems
     * @param string $identifier
     * @return array
     */
    public static function getAncestorIds(array $menuItems, string $identifier): array
    {
        if (empty($menuItems)) {
            return [];
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
        $foundPage = null;
        foreach ($menuItems as $menuItem) {
            if ($menuItem['page_id'] == $pageId) {
                $foundPage = $menuItem['id'];
                break;
            }
        }

        if (!is_null($foundPage)) {
            return $foundPage;
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

        while ($fetchAncestors) {
            $matchingMenuItem = array_filter($menuItems, function ($item) use ($id) {
                return $item['id'] == $id;
            });

            $matchingMenuItem = reset($matchingMenuItem);

            if ($matchingMenuItem['post_parent'] == 0) {
                $fetchAncestors = false;
            } else {
                $ancestorStack[] = (int) $matchingMenuItem['post_parent'];
                $id              = (int) $matchingMenuItem['post_parent'];
            }
        }

        return $ancestorStack;
    }
}
