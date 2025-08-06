<?php

namespace Municipio\Upgrade\Version;

use Municipio\Upgrade\Version\Helper\DeleteThemeMod;
use Municipio\Upgrade\Version\Helper\MigrateThemeMod;

class V10 implements \Municipio\Upgrade\VersionInterface
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
        MigrateThemeMod::migrate('quicklinks', 'quicklinks_background_type', 'field_61570dd479d9b');
        MigrateThemeMod::migrate('quicklinks', 'quicklinks_custom_background', 'field_61570e6979d9c');
        MigrateThemeMod::migrate('quicklinks', 'quicklinks_background', 'field_6123844e0f0bb');
        MigrateThemeMod::migrate('quicklinks', 'quicklinks_color', 'field_6127571bcc76e');
        MigrateThemeMod::migrate('quicklinks', 'quicklinks_sticky', 'field_61488b616937c');
        MigrateThemeMod::migrate('quicklinks', 'quicklinks_location', 'field_61488c4f6b4fd');

        DeleteThemeMod::delete('quicklinks');
    }
}