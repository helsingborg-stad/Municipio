<?php

namespace Municipio\Upgrade\Version;

use WpService\WpService;

class V35 implements \Municipio\Upgrade\VersionInterface
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
        $header = get_theme_mod('header_apperance');
        if ($header == '' || !$header) {
            set_theme_mod('header_width', 'wide');
        }
    }
}