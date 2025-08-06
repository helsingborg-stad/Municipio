<?php

namespace Municipio\Upgrade\Version;

class V27 implements \Municipio\Upgrade\VersionInterface
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
        $searchLocations = get_theme_mod('search_display');
        if (!empty($searchLocations) && is_array($searchLocations) && in_array('hamburger_menu', $searchLocations) && !in_array('mega_menu', $searchLocations)) {
            array_push($searchLocations, 'mega_menu');
            set_theme_mod('search_display', $searchLocations);
        }
    }
}