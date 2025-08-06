<?php

namespace Municipio\Upgrade\Version;

use Municipio\Upgrade\Version\Helper\DeleteThemeMod;

class V16 implements \Municipio\Upgrade\VersionInterface
{
    public function __construct(private \wpdb $db)
    {
        // Initialization code if needed
    }

    /**
     * @inheritDoc
     */
    public function upgradeToVersion(): void
    {
        $overlay = get_theme_mod(
            'hero_overlay_vibrant',
            get_theme_mod(
                'hero_overlay_neutral',
                get_theme_mod('overlay', "rgba(0,0,0,0.55)")
            )
        );

        if ($overlay) {
            set_theme_mod('color_alpha', array('base' => $overlay));
        }

        DeleteThemeMod::delete('hero_overlay_enable');
        DeleteThemeMod::delete('hero_overlay_vibrant');
        DeleteThemeMod::delete('hero_overlay_neutral');
        DeleteThemeMod::delete('overlay');
    }
}