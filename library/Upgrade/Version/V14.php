<?php

namespace Municipio\Upgrade\Version;

use WpService\WpService;
use Municipio\Upgrade\Version\Helper\DeleteThemeMod;
use Municipio\Upgrade\Version\Helper\MigrateThemeMod;

class V14 implements \Municipio\Upgrade\VersionInterface
{
    public function __construct(private \wpdb $db, private WpService $wpService)
    {
        // Initialization code if needed
    }

    /**
     * @inheritDoc
     */
    public function upgradeToVersion(): void
    {
        MigrateThemeMod::migrate('colors', 'color_palette_primary.base', 'field_60361bcb76325');
        MigrateThemeMod::migrate('colors', 'color_palette_primary.dark', 'field_60364d06dc120');
        MigrateThemeMod::migrate('colors', 'color_palette_primary.light', 'field_603fba043ab30');

        MigrateThemeMod::migrate('colors', 'color_palette_secondary.base', 'field_603fba3ffa851');
        MigrateThemeMod::migrate('colors', 'color_palette_secondary.dark', 'field_603fbb7ad4ccf');
        MigrateThemeMod::migrate('colors', 'color_palette_secondary.light', 'field_603fbbef1e2f8');

        MigrateThemeMod::migrate('colors', 'color_link.link', 'field_60868021879b6');
        MigrateThemeMod::migrate('colors', 'color_link.link_hover', 'field_608680ef879b7');
        MigrateThemeMod::migrate('colors', 'color_link.visited', 'field_60868147879b8');
        MigrateThemeMod::migrate('colors', 'color_link.visited_hover', 'field_6086819f879b9');
        MigrateThemeMod::migrate('colors', 'color_link.active', 'field_608681df879ba');

        MigrateThemeMod::migrate('colors', 'color_background.complementary', 'field_60911ccc38857');

        DeleteThemeMod::delete('colors');
    }
}