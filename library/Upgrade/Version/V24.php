<?php

namespace Municipio\Upgrade\Version;

use WpService\WpService;

class V24 implements \Municipio\Upgrade\VersionInterface
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
        do_action('municipio_store_theme_mod');
    }
}