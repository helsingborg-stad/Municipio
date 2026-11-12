<?php

namespace Municipio\Controller\Header\Helper;

use Municipio\Controller\Header\Helper\Enums;

class HeaderAttributes
{
    private string $style = '';

    public function buildStyleAttributes(array $menuItems): array
    {
        $this->style = $this->buildGridStyles($menuItems);
        return ['style' => $this->style];
    }

    private function buildGridStyles(array $menuItems): string
    {
        $amountOfItems = [
            Enums::HEADER_BREAKPOINT::DESKTOP->value => 0,
            Enums::HEADER_BREAKPOINT::MOBILE->value => 0,
        ];

        foreach ($menuItems as $menuItem) {
            if (!$menuItem->isEmpty(Enums::HEADER_BREAKPOINT::DESKTOP->value)) {
                $amountOfItems[Enums::HEADER_BREAKPOINT::DESKTOP->value]++;
            }

            if (!$menuItem->isEmpty(Enums::HEADER_BREAKPOINT::MOBILE->value)) {
                $amountOfItems[Enums::HEADER_BREAKPOINT::MOBILE->value]++;
            }
        }

        $style = '--amount-of-items-desktop: ' . $amountOfItems[Enums::HEADER_BREAKPOINT::DESKTOP->value] . ';';
        $style .= '--amount-of-items-mobile: ' . $amountOfItems[Enums::HEADER_BREAKPOINT::MOBILE->value] . ';';

        return $style;
    }
}