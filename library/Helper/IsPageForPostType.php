<?php

namespace Municipio\Helper;

use Municipio\Controller\Navigation\Helper\GetPageForPostTypeIds;

class IsPageForPostType
{
    /**
     * Get post type from page id.
     *
     * @param int $pageId
     * @return string|false
     */
    public static function isPageForPostType(int $pageId): string|false
    {
        $pageForPostTypeIds = GetPageForPostTypeIds::getPageForPostTypeIds();
        if (array_key_exists($pageId, $pageForPostTypeIds)) {
            return $pageForPostTypeIds[$pageId];
        } 

        return false;
    }
}