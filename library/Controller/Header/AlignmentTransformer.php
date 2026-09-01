<?php

namespace Municipio\Controller\Header;

class AlignmentTransformer
{
    public function __construct(
        private object $data,
    ) {}

    // Transforms the menu items to align them to the left, center or right.
    public function transform(array $items, string $setting): array
    {
        $alignedItems = [];
        if (!empty($items['modified'])) {
            foreach ($items['modified'] as $menu => $classes) {
                $alignedItems[$this->getAlignment($items, $setting, $menu)][$menu] = $classes;
            }
        }

        $items['modified'] = $alignedItems;

        return $items;
    }

    private function getAlignment(array $items, string $setting, string $menu): string
    {
        $responsiveSetting = $setting . '_responsive';
        if (!isset($items['desktop'][$menu]) && isset($items['mobile'][$menu]) && !empty($this->data->{$responsiveSetting}->{$menu}->align)) {
            return $this->data->{$responsiveSetting}->{$menu}->align;
        }

        if (!empty($this->data->{$setting}->{$menu}->align)) {
            return $this->data->{$setting}->{$menu}->align;
        }

        return 'right';
    }
}
