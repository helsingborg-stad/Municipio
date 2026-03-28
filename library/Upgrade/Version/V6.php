<?php

namespace Municipio\Upgrade\Version;

use WpService\WpService;

use Municipio\Upgrade\Version\Helper\DeleteThemeMod;
use Municipio\Upgrade\Version\Helper\MigrateThemeMod;

class V6 implements \Municipio\Upgrade\VersionInterface
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
        MigrateThemeMod::migrate('general', 'secondary_navigation_position', 'field_60cb4dd897cb8');

        DeleteThemeMod::delete('general');
        DeleteThemeMod::delete('mobilemenu');
    }
}

