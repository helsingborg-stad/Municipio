<?php

namespace Municipio\Controller\Header\Helper;

class HeaderClasses
{
    public function buildVisibilityClasses(array $rawMenuItems): array
    {
        $classes = [];

        $hasDesktopItems = !empty($rawMenuItems[Enums::HEADER_BREAKPOINT::DESKTOP->value]);
        $hasMobileItems = !empty($rawMenuItems[Enums::HEADER_BREAKPOINT::MOBILE->value]);

        $classes[] = 'u-display--' . ($hasDesktopItems ? 'block' : 'none') . '@lg';
        $classes[] = 'u-display--' . ($hasDesktopItems ? 'block' : 'none') . '@xl';
        $classes[] = 'u-display--' . ($hasMobileItems ? 'block' : 'none');

        return $classes;
    }
}