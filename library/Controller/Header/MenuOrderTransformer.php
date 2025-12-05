<?php

namespace Municipio\Controller\Header;

class MenuOrderTransformer
{
    public function __construct(private string $modifier)
    {
    }

    public function transform(array $items): array
    {
        $modifier          = $this->modifier;
        $desktopItems      = $items['desktop'];
        $mobileItems       = $items['mobile'];
        $items['modified'] = [];

        if (empty($desktopItems) && empty($mobileItems)) {
            return $items;
        }

        if (!empty($mobileItems)) {
            foreach ($mobileItems as $mobileItem => $order) {
                $items['modified'][$mobileItem][] = 'u-order--' . $order;
            }
        } else {
            $modifier = '';
        }

        if (!empty($desktopItems)) {
            foreach ($desktopItems as $desktopItem => $order) {
                $items['modified'][$desktopItem][] = 'u-order--' . $order . $modifier;
            }
        }

        return $items;
    }
}
