<?php

namespace Municipio\Upgrade\Version;

use WpService\WpService;

use Municipio\Upgrade\Version\Helper\DeleteThemeMod;
use Municipio\Upgrade\Version\Helper\MigrateThemeMod;

class V7 implements \Municipio\Upgrade\VersionInterface
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
        MigrateThemeMod::migrate('radius', 'radius_xs', 'field_603662f7a16f8');
        MigrateThemeMod::migrate('radius', 'radius_sm', 'field_6038fa31cfac6');
        MigrateThemeMod::migrate('radius', 'radius_md', 'field_6038fa400384b');
        MigrateThemeMod::migrate('radius', 'radius_lg', 'field_6038fa52576ba');

        DeleteThemeMod::delete('radius');

    }
}

