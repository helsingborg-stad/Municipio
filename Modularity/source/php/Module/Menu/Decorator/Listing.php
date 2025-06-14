<?php

namespace Modularity\Module\Menu\Decorator;

class Listing implements DataDecoratorInterface
{
    public function __construct(private $fields)
    {
    }

    public function decorate(array $data): array
    {
        $amountOfItems       = !empty($data['menu']['items']) ? count($data['menu']['items']) : 0;
        $columns             = (int) ($this->fields['mod_menu_columns'] ?? 3);
        $data['columns']     = $this->tryGetColumns($data['wrapped'], $columns, $amountOfItems);
        $data['gridClasses'] = $this->getGridColumnsCompabilityClasses(
            $data['wrapped'],
            $columns,
            $amountOfItems
        ) ?? [];

        return $data;
    }

    /**
     * Tries to calculate the number of columns for a menu listing.
     *
     * This method takes into consideration whether the menu items should be wrapped or not,
     * the desired number of columns, and the total amount of menu items.
     *
     * @param bool $wrapped Whether the menu items should be wrapped or not.
     * @param int $columns The desired number of columns.
     * @param int $amountOfItems The total amount of menu items.
     * @return int The calculated number of columns.
     */
    private function tryGetColumns(bool $wrapped, int $columns, int $amountOfItems): int
    {
        if (!$wrapped || $amountOfItems % $columns === 0) {
            return $columns;
        }

        if ($amountOfItems < $columns) {
            return $amountOfItems;
        }

        foreach (range($columns - 1, 1) as $column) {
            if ($amountOfItems % $column === 0) {
                return $column;
            }
        }

        return $columns;
    }

    /**
     * Adds grid classes based on the number of columns and the amount of items.
     *
     * @param int $columns The number of columns.
     * @param int $amountOfItems The amount of items.
     * @return array The array of grid classes.
     */
    private function getGridColumnsCompabilityClasses(bool $wrapped, int $columns, int $amountOfItems): array
    {
        if (!$wrapped) {
            return [];
        }
        // to work with 3 columns since its an odd number.
        $modifiedColumns = $columns === 3 ? 6 : $columns;

        $classList = [];

        if ($amountOfItems % $modifiedColumns !== 0) {
            $classList[] = 'c-group--single-column-sm';
            $classList[] = 'c-group--single-column-md';
        }

        return $classList;
    }
}
