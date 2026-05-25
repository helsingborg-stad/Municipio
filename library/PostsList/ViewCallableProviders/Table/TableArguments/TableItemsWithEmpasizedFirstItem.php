<?php

namespace Municipio\PostsList\ViewCallableProviders\Table\TableArguments;

class TableItemsWithEmpasizedFirstItem
{
    public function __construct(
        private array $items,
    ) {}

    public function emphasize(): array
    {
        $this->normalizeItems($this->items);

        if (empty($this->items)) {
            return [];
        }

        // wrap all column items but the first one in <span> tags
        foreach ($this->items as &$item) {
            foreach ($item['columns'] as $index => $column) {
                if ($index === 0) {
                    continue; // Skip the first column
                }
                $item['columns'][$index] = $this->wrapString($column);
            }
        }

        return $this->items;
    }

    private function wrapString(string $string): string
    {
        return '<span class="c-typography c-typography__variant--meta">' . $string . '</span>';
    }

    private function normalizeItems(array &$items): void
    {
        foreach ($items as &$item) {
            $item['columns'] = array_values($item['columns']);
        }
    }
}
