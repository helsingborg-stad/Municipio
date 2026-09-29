<?php

namespace Municipio\Controller\Header\Helper;

class HeaderVisibilityClasses
{
    public function buildVisibilityClasses(array $menuItems, array $modifiers = [""]): array
    {
        $classes = [];
        $visible = !empty($menuItems);
        foreach ($modifiers as $modifier) {
            $classes[] = 'u-display--' . ($visible ? 'flex' : 'none') . $modifier;
        }

        return $classes;
    }
}