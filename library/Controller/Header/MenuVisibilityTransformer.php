<?php

namespace Municipio\Controller\Header;

class MenuVisibilityTransformer
{
    public function __construct()
    {
    }

    public function transform(array $items)
    {
        if (empty($items['modified'])) {
            return $items;
        }

        foreach ($items['modified'] as $menu => $classes) {
            if (isset($items['desktop'][$menu]) && isset($items['mobile'][$menu])) {
                continue;
            }

            if (isset($items['desktop'][$menu])) {
                $items['modified'][$menu][] = 'u-display--none';
                $items['modified'][$menu][] = 'u-display--block@md';
                $items['modified'][$menu][] = 'u-display--block@lg';
                $items['modified'][$menu][] = 'u-display--block@xl';
            }

            if (isset($items['mobile'][$menu])) {
                $items['modified'][$menu][] = 'u-display--none@md';
                $items['modified'][$menu][] = 'u-display--none@lg';
                $items['modified'][$menu][] = 'u-display--none@xl';
            }
        }

        return $items;
    }
}
