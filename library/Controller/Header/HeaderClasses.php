<?php

namespace Municipio\Controller\Header;

use Municipio\Controller\Header\Helper\ShowHideClasses;

class HeaderClasses
{
    public function __construct(private object $customizer)
    {
    }

    public function getHeaderClasses(array $items, array $logoScrollShrink): array
    {
        $classList = ['c-header--flexible', 'site-header'];

        if ($logoScrollShrink['enabled']) {
            $classList[] = 'c-header--logotype-scroll-shrink';
        }

        if ($this->customizer->megaMenuMobile) {
            $classList[] = 'mega-menu-mobile';
        }

        if (empty($items['mobile'])) {
            $classList[] = 'u-display--none';
        }

        if (!empty($items['desktop'])) {
            $classList = ShowHideClasses::getShowDesktopClasses($classList);
        } else {
            $classList = ShowHideClasses::getHideDesktopClasses($classList);
        }

        return $classList;
    }
}
