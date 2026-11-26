<?php

namespace Municipio\Controller\Navigation\Helper;

use Municipio\Helper\GetGlobal;
use WpService\Contracts\IsPostTypeHierarchical;

class GetChildrenIndicators
{
    /**
     * Retrieve child-existence indicators for non-current, uncached menu items.
     *
     * @param array $menuItems The menu items to check.
     * @param int $currentPostId The current post ID, whose children are fetched separately.
     * @param IsPostTypeHierarchical $wpService WordPress post-type service.
     * @return array Child-existence indicators keyed by menu item ID.
     */
    public static function getChildrenIndicators(
        array $menuItems,
        int $currentPostId,
        IsPostTypeHierarchical $wpService
    ): array {
        $menuItemIds = [];
        foreach ($menuItems as $menuItem) {
            if (
                !empty($menuItem['isCached']) ||
                !empty($menuItem['children']) ||
                $menuItem['id'] == $currentPostId
            ) {
                continue;
            }

            $menuItemIds[] = $menuItem['id'];
        }

        if (empty($menuItemIds)) {
            return [];
        }

        $localWpdb = GetGlobal::getGlobal('wpdb');
        $postStatus = IsUserLoggedIn::isUserLoggedIn() ?
            "post_status IN('publish', 'private')" : "post_status = 'publish'";
        $hiddenPostIds = implode(", ", GetHiddenPostIds::getHiddenPostIds());
        $postIds = array_values(array_unique(array_map('intval', $menuItemIds)));
        $placeholders = implode(', ', array_fill(0, count($postIds), '%d'));

        $parentIdsWithChildren = $localWpdb->get_col(
            $localWpdb->prepare("
                SELECT DISTINCT post_parent
                FROM {$localWpdb->posts}
                WHERE post_parent IN ({$placeholders})
                AND {$postStatus}
                AND ID NOT IN({$hiddenPostIds})
            ", ...$postIds)
        );

        $pageForPostTypeIds = GetPageForPostTypeIds::getPageForPostTypeIds();
        $mappedPostTypes = [];
        foreach ($menuItemIds as $postId) {
            if (
                array_key_exists($postId, $pageForPostTypeIds) &&
                $wpService->isPostTypeHierarchical($pageForPostTypeIds[$postId])
            ) {
                $mappedPostTypes[$postId] = $pageForPostTypeIds[$postId];
            }
        }

        $postTypesWithChildren = [];
        if (!empty($mappedPostTypes)) {
            $postTypes = array_values(array_unique($mappedPostTypes));
            $postTypeSelects = implode(' UNION ALL ', array_fill(0, count($postTypes), 'SELECT %s AS post_type'));
            $postTypesWithChildren = $localWpdb->get_col(
                $localWpdb->prepare("
                    SELECT DISTINCT mapped.post_type
                    FROM ({$postTypeSelects}) AS mapped
                    JOIN {$localWpdb->posts} AS p ON p.post_type = mapped.post_type
                    WHERE p.post_parent = 0
                    AND {$postStatus}
                    AND p.ID NOT IN({$hiddenPostIds})
                ", ...$postTypes)
            );
        }

        $parentIdsWithChildren = array_fill_keys($parentIdsWithChildren, true);
        $postTypesWithChildren = array_fill_keys($postTypesWithChildren, true);
        $indicators = [];
        foreach ($menuItemIds as $postId) {
            $indicators[$postId] = isset($parentIdsWithChildren[(int) $postId]) ||
                (isset($mappedPostTypes[$postId]) && isset($postTypesWithChildren[$mappedPostTypes[$postId]]));
        }

        return $indicators;
    }
}
