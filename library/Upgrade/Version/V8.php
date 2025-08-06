<?php

namespace Municipio\Upgrade\Version;

use Municipio\Upgrade\Version\Helper\DeleteThemeMod;
use Municipio\Upgrade\Version\Helper\MigrateThemeMod;

class V8 implements \Municipio\Upgrade\VersionInterface
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
        if (get_theme_mod('header')) {
            MigrateThemeMod::migrate('header', 'header_sticky', 'field_61434d3478ef7');
            MigrateThemeMod::migrate('header', 'header_background', 'field_61446365d1c7e');
            MigrateThemeMod::migrate('header', 'header_color', 'field_614467575de00');
            MigrateThemeMod::migrate('header', 'header_modifier', 'field_6070186956c15');
        }

        DeleteThemeMod::delete('header');
    }
}

