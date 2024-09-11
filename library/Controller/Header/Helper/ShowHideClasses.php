<?php

namespace Municipio\Controller\Header\Helper;

class ShowHideClasses
{
    // Classes for showing an element on desktop.
    public static function getShowDesktopClasses(array $classes = []): array
    {
        return array_merge($classes, [
            'u-display--block@lg',
            'u-display--block@xl'
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
