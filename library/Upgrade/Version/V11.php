<?php

namespace Municipio\Upgrade\Version;

use Municipio\Upgrade\Version\Helper\DeleteThemeMod;

class V11 implements \Municipio\Upgrade\VersionInterface
{
    /**
     * @inheritDoc
     */
    public function upgradeToVersion(): void
    {
        $overlays = get_theme_mod('hero');

        $defaultColor   = $overlays['field_614c713ae73ea']['field_614c7189e73eb'];
        $vibrantColor   = $overlays['field_614c720fb65a4']['field_614c720fb65a5'];

        if ($vibrantColor || $defaultColor) {
            if ($vibrantColor == 'rgb(0,0,0)' && $defaultColor == 'rgb(0,0,0)') {
                set_theme_mod('hero_overlay_enable', 0);
            } else {
                set_theme_mod('hero_overlay_enable', 1);
            }
        } else {
            set_theme_mod('hero_overlay_enable', 0);
        }

        DeleteThemeMod::delete('hero');
    }
}