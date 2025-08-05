<?php

namespace Municipio\Upgrade\Version;

use Municipio\Upgrade\Version\Helper\SetAssociativeThemeMod;

class V17 implements \Municipio\Upgrade\VersionInterface
{
    /**
     * @inheritDoc
     */
    public function upgradeToVersion(): void
    {
        if (
            !empty(get_theme_mod('color_palette_primary')) &&
            empty(get_theme_mod('color_palette_primary')['contrasting'])
        ) {
            SetAssociativeThemeMod::set('color_palette_primary.contrasting', '#ffffff');
        }

        if (
            !empty(get_theme_mod('color_palette_secondary')) &&
            empty(get_theme_mod('color_palette_secondary')['contrasting'])
        ) {
            SetAssociativeThemeMod::set('color_palette_secondary.contrasting', '#ffffff');
        }
    }
}