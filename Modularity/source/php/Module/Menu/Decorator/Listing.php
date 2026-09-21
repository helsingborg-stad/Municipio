<?php

declare(strict_types=1);

namespace Modularity\Module\Menu\Decorator;

/**
 * Decorates menu listing data with responsive grid classes.
 */
class Listing implements DataDecoratorInterface
{
    /**
     * Add responsive column classes based on the number of menu items.
     *
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    public function decorate(array $data): array
    {
        $amountOfItems = !empty($data['menu']['items']) ? count($data['menu']['items']) : 0;

        $data['columnClasses'] = $this->getColumnsClasses($amountOfItems);

        return $data;
    }

    /**
     * Get responsive column classes with a maximum of four columns per row.
     *
     * @param int $amountOfItems Number of menu items in the listing.
     *
     * @return array<int, string>
     */
    private function getColumnsClasses(int $amountOfItems): array
    {
        $classes = [];
        $columnsAtMediumBreakpoint = max(1, min(2, $amountOfItems));
        $columnsAtLargeBreakpoint = max(1, min(4, $amountOfItems));

        $classes[] = 'o-layout-grid--cols-' . 1;
        $classes[] = 'o-layout-grid--cols-' . $columnsAtMediumBreakpoint . '@md';
        $classes[] = 'o-layout-grid--cols-' . $columnsAtLargeBreakpoint . '@lg';

        return $classes;
    }
}
