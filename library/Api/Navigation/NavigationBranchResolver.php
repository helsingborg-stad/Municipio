<?php

declare(strict_types=1);

namespace Municipio\Api\Navigation;

/**
 * Resolves configured navigation menu branches for async child endpoints.
 */
class NavigationBranchResolver
{
    /**
     * Resolve the menu location that should be used for an async identifier.
     *
     * @param string $identifier The async navigation identifier.
     *
     * @return string The menu location, or an empty string when no mapped menu exists.
     */
    public static function resolveMenuName(string $identifier): string
    {
        return match ($identifier) {
            'mobile' => 'secondary-menu',
            'primary', 'extended-dropdown-children' => 'main-menu',
            'mega-menu' => 'mega-menu',
            'mobile-secondary' => 'mobile-drawer',
            default => '',
        };
    }

    /**
     * Find the requested branch children recursively in a nested menu tree.
     *
     * @param array $menuItems Nested menu items.
     * @param int   $pageId    The page ID to find.
     *
     * @return ?array The matching children, an empty array when the node has no children, or null when not found.
     */
    public static function findChildren(array $menuItems, int $pageId): ?array
    {
        foreach ($menuItems as $menuItem) {
            if (!is_array($menuItem) || !isset($menuItem['id']) || (int) $menuItem['id'] !== $pageId) {
                if (!empty($menuItem['children']) && is_array($menuItem['children'])) {
                    $children = self::findChildren($menuItem['children'], $pageId);

                    if ($children !== null) {
                        return $children;
                    }
                }

                continue;
            }

            return !empty($menuItem['children']) && is_array($menuItem['children']) ? $menuItem['children'] : [];
        }

        return null;
    }
}