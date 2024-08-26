<?php

namespace Municipio\Controller\Header;

class MenuVisibilityTransformer
{
    // Transforms the menu items to show or hide them on desktop and mobile.
    public function transform(array $items)
    {
        if (empty($items['modified'])) {
            return $items;
        }

        foreach ($items['modified'] as $menu => $classes) {
            if (isset($items['desktop'][$menu]) && isset($items['mobile'][$menu])) {
                continue;
            }

            if (isset($items['desktop'][$menu])) {
                $items['modified'][$menu][] = 'u-display--none';
                $items['modified'][$menu]   = \Municipio\Controller\Header\Helper\ShowHideClasses::getShowDesktopClasses($items['modified'][$menu]);
            }

            if (isset($items['mobile'][$menu])) {
                $items['modified'][$menu] = \Municipio\Controller\Header\Helper\ShowHideClasses::getHideDesktopClasses($items['modified'][$menu]);
            }
        }

        return $items;
    }
}
