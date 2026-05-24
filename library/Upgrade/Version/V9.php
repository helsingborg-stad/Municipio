<?php

namespace Municipio\Upgrade\Version;

use WpService\WpService;

use Municipio\Upgrade\Version\Helper\DeleteThemeMod;
use Municipio\Upgrade\Version\Helper\MigrateThemeMod;

class V9 implements \Municipio\Upgrade\VersionInterface
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
        MigrateThemeMod::migrate('padding', 'main_content_padding', 'field_611e43ec4dfa5');

        DeleteThemeMod::delete('padding');
    }
}

