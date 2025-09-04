<?php

namespace Municipio\Upgrade\Version;

use WpService\WpService;

class V21 implements \Municipio\Upgrade\VersionInterface
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
        if ($logotype = get_option('options_footer_logotype')) {
            set_theme_mod('footer_logotype', $logotype);
        }

        delete_option('options_footer_logotype');
    }
}