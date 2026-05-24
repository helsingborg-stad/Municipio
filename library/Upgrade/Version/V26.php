<?php

namespace Municipio\Upgrade\Version;

use WpService\WpService;

class V26 implements \Municipio\Upgrade\VersionInterface
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
       $drawerSizes = get_theme_mod('drawer_screen_sizes');
        if (!empty($drawerSizes) && is_array($drawerSizes) && in_array('lg', $drawerSizes)) {
            array_push($drawerSizes, 'xl');
            set_theme_mod('drawer_screen_sizes', $drawerSizes);
        }
    }
}