<?php

namespace Municipio\Controller\Header;

class AlignmentTransformer
{
    public function __construct(private object $data)
    {
    }

    public function transform(array $items, string $setting): array
    {
        $alignedItems = [];
        if (!empty($items)) {
            foreach ($items as $menu => $classes) {
                if (!empty($this->data->{$setting}->{$menu})) {
                    foreach ($this->data->{$setting}->{$menu} as $name => $value) {
                        $alignedItems[$value][$menu] = $classes;
                    }
                }
            }
        }

        return $alignedItems;
    }
}
