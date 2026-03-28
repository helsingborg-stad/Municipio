<?php

namespace Municipio\Upgrade\Version;

use WpService\WpService;

class V15 implements \Municipio\Upgrade\VersionInterface
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
        if (get_option('options_header_layout')) {
            set_theme_mod('header_apperance', get_option('options_header_layout'));
        }

        delete_option('options_header_layout');
    }
}