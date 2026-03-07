<?php

namespace Municipio\Upgrade\Version;

use WpService\WpService;

use Municipio\Upgrade\Version\Helper\Hex2Rgb;

class V12 implements \Municipio\Upgrade\VersionInterface
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
        $overlays = get_theme_mod('overlay');
        if ($overlays) {
            $color   = $overlays['field_615c1bc3772c6']['field_615c1bc3780b0'];
            $opacity = $overlays['field_615c1bc3772c6']['field_615c1bc3780b6'];
            $overlay = Hex2Rgb::convert($color, "0." . (int)$opacity);
            set_theme_mod('overlay', $overlay);
        }
    }
}