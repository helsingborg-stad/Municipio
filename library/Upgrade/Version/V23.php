<?php

namespace Municipio\Upgrade\Version;

use Municipio\Upgrade\Version\Helper\MigrateAcfOptionToThemeMod;

class V23 implements \Municipio\Upgrade\VersionInterface
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
        MigrateAcfOptionToThemeMod::migrate('search_display', 'search_display');
    }
}