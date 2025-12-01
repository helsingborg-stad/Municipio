<?php

namespace Municipio\Controller\Header;

class HeaderVisibilityClasses
{
    public function getHeaderClasses(array $items): array
    {
        $classes = [];
        if (empty($items['mobile'])) {
            $classes[] = 'u-display--none';
        }

        if (!empty($items['desktop'])) {
            $classes = \Municipio\Controller\Header\Helper\ShowHideClasses::getShowDesktopClasses($classes);
        } else {
            $classes = \Municipio\Controller\Header\Helper\ShowHideClasses::getHideDesktopClasses($classes);
        }

        return $classes;
    }
}
