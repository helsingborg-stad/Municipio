<?php

declare(strict_types=1);

namespace Modularity\Module\Menu\Decorator;

use WpService\Implementations\NativeWpService;

/**
 * Decorates menu listing data with responsive grid classes.
 */
class Listing implements DataDecoratorInterface
{
    private static $index = 0;

    public function __construct(
        private array $fields,
        private NativeWpService|null $wpService
    ) {
        self::$index++;
    }

    /**
     * Add responsive column classes based on the number of menu items.
     *
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    public function decorate(array $data): array
    {
        $data['spaced'] = !empty($this->fields['mod_menu_spaced']);
        $data['wrapped'] = !empty($this->fields['mod_menu_wrapped']);

        $data['wrapperClasses'] = $this->getWrapperClasses($data);
        $data['classList'] = $data['classList'] ?? [];
        $data['classList'][] = 'mod-menu__listing';

        $data['menu'] = $this->structureMenu($data['menu'] ?? []);

        if ($data['wrapped'] && !$data['spaced']) {
            $data['classList'][] = 'mod-menu__listing--wrapped';
        }

        if ($data['spaced']) {
            $data['classList'][] = 'mod-menu__listing--spaced';
        }

        $data['lang'] = [
            'showAll' => $this->wpService?->__('Show all', 'municipio'),
            'hide' => $this->wpService?->__('Hide', 'municipio'),
        ];
        $data['menuIndex'] = self::$index;

        return $data;
    }

    private function getWrapperClasses(array $data): array
    {
        $amountOfItems = !empty($data['menu']['items']) ? count($data['menu']['items']) : 0;

        $classes = $this->getColumnsClasses($amountOfItems, $data['spaced'] ? 3 : 4);

        if ($data['wrapped'] && !$data['spaced']) {
            $classes[] = 'u-shadow--1';
        }

        return $classes;
    }

    private function structureMenu(array $menu): array
    {
        foreach ($menu['items'] as &$item) {
            if (!empty($item['label'])) {
                [$item['label'], $item['lastWord']] = $this->splitLabel($item);
            }

            if (!empty($item['children'])) {
                foreach($item['children'] as &$child) {
                    if (!empty($child['label'])) {
                        [$child['label'], $child['lastWord']] = $this->splitLabel($child);
                    }
                }
            }
        }

        return $menu;
    }

    private function splitLabel(array $item): array
    {
        $words = explode(' ', $item['label']);
        $lastWord = array_pop($words);
        $firstPart = implode(' ', $words);

        return [$firstPart, $lastWord];
    }

    /**
     * Get responsive column classes with a maximum of four columns per row.
     *
     * @param int $amountOfItems Number of menu items in the listing.
     *
     * @return array<int, string>
     */
    private function getColumnsClasses(int $amountOfItems, int $maxColumns = 4): array
    {
        $classes = [];
        $columnsAtMediumBreakpoint = max(1, min(2, $amountOfItems));
        $columnsAtLargeBreakpoint = $this->getLargeBreakpointColumns($amountOfItems, $maxColumns);

        $classes[] = 'o-layout-grid--cols-' . 1;
        $classes[] = 'o-layout-grid--cols-' . $columnsAtMediumBreakpoint . '@md';
        $classes[] = 'o-layout-grid--cols-' . $columnsAtLargeBreakpoint . '@lg';

        return $classes;
    }

    /**
     * Get the most suitable number of columns for the large breakpoint.
     *
     * Prefer a 3-column layout for item counts that divide evenly by 3,
     * then fall back to the configured maximum number of columns.
     *
     * @param int $amountOfItems Number of menu items in the listing.
     * @param int $maxColumns Maximum number of columns allowed at the large breakpoint.
     *
     * @return int
     */
    private function getLargeBreakpointColumns(int $amountOfItems, int $maxColumns = 4): int
    {
        if ($amountOfItems <= $maxColumns) {
            return max(1, $amountOfItems);
        }

        if ($maxColumns >= 3 && $amountOfItems % 3 === 0 && $amountOfItems % 4 !== 0) {
            return 3;
        }

        return $maxColumns;
    }
}
