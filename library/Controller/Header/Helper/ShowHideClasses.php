<?php

namespace Municipio\Controller\Header\Helper;

class ShowHideClasses
{
    public static function getShowDesktopClasses(array $classes = []): array
    {
        return array_merge($classes, [
            'u-display--block@md',
            'u-display--block@lg',
            'u-display--block@xl'
        ]);
    }

    public static function getHideDesktopClasses(array $classes = []): array
    {
        return array_merge($classes, [
            'u-display--none@md',
            'u-display--none@lg',
            'u-display--none@xl'
        ]);
    }
}
