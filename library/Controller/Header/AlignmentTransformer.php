<?php

namespace Municipio\Controller\Header;

class AlignmentTransformer
{
    public function __construct(private object $data)
    {
    }

    // Transforms the menu items to align them to the left, center or right.
    public function transform(array $items, string $setting): array
    {
        $alignedItems = [];
        if (!empty($items['modified'])) {
            foreach ($items['modified'] as $menu => $classes) {
                if (!empty($this->data->{$setting}->{$menu}->align)) {
                    $alignedItems[$this->data->{$setting}->{$menu}->align][$menu] = $classes;
                }
            }
        }

        $items['modified'] = $alignedItems;

        return $items;
    }
}
