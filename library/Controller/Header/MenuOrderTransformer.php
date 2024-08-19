<?php

namespace Municipio\Controller\Header;

class MenuOrderTransformer
{
    public function __construct(private string $modifier)
    {
    }

    public function transform(array $desktopItems, array $mobileItems): array
    {
        $items    = [];
        $modifier = $this->modifier;

        if (empty($desktopItems)) {
            return $items;
        }

        if (!empty($mobileItems)) {
            foreach ($mobileItems as $key => $mobileItem) {
                $items[$mobileItem][] = 'u-order--' . $key;
            }
        } else {
            $modifier = '';
        }

        foreach ($desktopItems as $key => $desktopItem) {
            $items[$desktopItem][] = 'u-order--' . $key . $modifier;
        }


        return $items;
    }
}
