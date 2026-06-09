<?php

namespace Municipio\Controller\Header\Helper;

class ShowHideClasses
{
    // Classes for showing an element on desktop.
    public static function getShowDesktopClasses(array $classes = [], $flex = false): array
    {
        $display = $flex ? 'flex' : 'block';

        return array_merge($classes, [
            "u-display--{$display}@lg",
            "u-display--{$display}@xl"
        ]);
    }

    // Classes for hiding an element on desktop
    public static function getHideDesktopClasses(array $classes = []): array
    {
        return array_merge($classes, [
            'u-display--none@lg',
            'u-display--none@xl'
        ]);
    }
}
