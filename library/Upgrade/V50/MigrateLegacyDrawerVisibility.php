<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V50;

use WpService\Contracts\GetThemeMod;
use WpService\Contracts\SetThemeMod;

/**
 * Preserves legacy drawer breakpoint visibility in flexible header areas.
 */
class MigrateLegacyDrawerVisibility
{
    private const DRAWER_SCREEN_SIZES_SETTING = 'drawer_screen_sizes';
    private const UPPER_SECTION_SETTING = 'header_sortable_section_main_upper';
    private const UPPER_RESPONSIVE_SECTION_SETTING = 'header_sortable_section_main_upper_responsive';
    private const DEFAULT_DRAWER_SCREEN_SIZES = ['xs', 'sm', 'md'];
    private const MOBILE_SCREEN_SIZES = ['xs', 'sm', 'md'];
    private const DESKTOP_SCREEN_SIZES = ['lg', 'xl'];

    /**
     * @param GetThemeMod&SetThemeMod $wpService WordPress service.
     */
    public function __construct(
        private readonly GetThemeMod&SetThemeMod $wpService,
    ) {}

    /**
     * Remove the drawer from migrated header areas where it was not visible.
     *
     * @param array<int, string> $desktopItems Migrated desktop header items.
     * @param array<int, string> $mobileItems Migrated mobile header items.
     */
    public function migrate(array $desktopItems, array $mobileItems): void
    {
        $screenSizes = $this->normalizeScreenSizes(
            $this->wpService->getThemeMod(self::DRAWER_SCREEN_SIZES_SETTING, self::DEFAULT_DRAWER_SCREEN_SIZES),
        );

        if (empty(array_intersect(self::DESKTOP_SCREEN_SIZES, $screenSizes))) {
            $desktopItems = $this->removeDrawer($desktopItems);
        }

        if (empty(array_intersect(self::MOBILE_SCREEN_SIZES, $screenSizes))) {
            $mobileItems = $this->removeDrawer($mobileItems);
        }

        $this->wpService->setThemeMod(self::UPPER_SECTION_SETTING, $desktopItems);
        $this->wpService->setThemeMod(self::UPPER_RESPONSIVE_SECTION_SETTING, $mobileItems);
    }

    /**
     * @param array<int, string> $items
     *
     * @return array<int, string>
     */
    private function removeDrawer(array $items): array
    {
        return array_values(array_diff($items, ['drawer']));
    }

    /**
     * @return array<int, string>
     */
    private function normalizeScreenSizes(mixed $screenSizes): array
    {
        if (is_string($screenSizes)) {
            $decoded = json_decode($screenSizes, true);
            $screenSizes = is_array($decoded) ? $decoded : [];
        }

        if (!is_array($screenSizes)) {
            return [];
        }

        return array_values(array_filter($screenSizes, static fn(mixed $size): bool => is_string($size)));
    }
}