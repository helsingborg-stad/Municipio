<?php

namespace Municipio\Controller\Header\Helper;

use Municipio\Controller\Header\Helper\HeaderBreakpoint;

class MenuItemClasses
{
    private array $modifiers = [
        HeaderBreakpoint::DESKTOP->value => ["@lg", "@xl"],
        HeaderBreakpoint::MOBILE->value => [""],
    ];

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
}