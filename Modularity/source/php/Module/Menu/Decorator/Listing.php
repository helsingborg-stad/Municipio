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
        $columnsAtLargeBreakpoint = max(1, min($maxColumns, $amountOfItems));

        $classes[] = 'o-layout-grid--cols-' . 1;
        $classes[] = 'o-layout-grid--cols-' . $columnsAtMediumBreakpoint . '@md';
        $classes[] = 'o-layout-grid--cols-' . $columnsAtLargeBreakpoint . '@lg';

        return $classes;
    }
}
