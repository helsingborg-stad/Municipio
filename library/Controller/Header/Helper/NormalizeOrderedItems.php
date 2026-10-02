<?php

namespace Municipio\Controller\Header\Helper;

/**
 * Normalizes menu item lists from customizer values.
 */
class NormalizeOrderedItems
{
    /**
     * Normalize a menu item collection.
     *
     * @return array<int, string>
     */
    public function normalize(mixed $items): array
    {
        if (is_array($items)) {
            return array_values(array_filter($items, static fn (mixed $item): bool => is_string($item) && $item !== ''));
        }

        if (!is_string($items) || trim($items) === '') {
            return [];
        }

        $decoded = json_decode($items, true);

        if (!is_array($decoded)) {
            return [];
        }

        return array_values(array_filter($decoded, static fn (mixed $item): bool => is_string($item) && $item !== ''));
    }
}