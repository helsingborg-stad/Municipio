<?php

namespace Municipio\Controller\Header\Helper;

use Municipio\Controller\Header\Helper\Enums;

class MenuItemClasses
{
    private array $modifiers;

    public function __construct()
    {
        $this->modifiers = [
            Enums::HEADER_BREAKPOINT::DESKTOP->value => ["@lg", "@xl"],
            Enums::HEADER_BREAKPOINT::MOBILE->value => [""],
        ];
    }

    public function buildVisibilityClasses(array $rawMenuItem): array
    {
        $classList = [];

        foreach ($rawMenuItem as $breakpoint => $item) {
            foreach ($this->modifiers[$breakpoint] as $modifier) {
                $classList[] = 'u-display--' . (!empty($item) ? 'block' : 'none') . $modifier;
            }
        }

        return $classList;
    }

    public function buildOrderClasses(array $rawMenuItem)
    {
        $classList = [];

        echo '<pre>' . print_r( $rawMenuItem['desktop']['align'], true ) . '</pre>';
        
        die;
        $desktopOrder = intval($rawMenuItem[Enums::HEADER_BREAKPOINT::DESKTOP->value]['order'] ?? 0);
        $mobileOrder = intval($rawMenuItem[Enums::HEADER_BREAKPOINT::MOBILE->value]['order'] ?? 0);


        $classList[] = 'u-order--' . $desktopOrder . '@lg';
        $classList[] = 'u-order--' . $desktopOrder . '@xl';
        $classList[] = 'u-order--' . $mobileOrder;

        return $classList;
    }
}