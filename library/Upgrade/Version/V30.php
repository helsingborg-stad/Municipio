<?php

namespace Municipio\Upgrade\Version;

class V30 implements \Municipio\Upgrade\VersionInterface
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
        $nativeFaviconKey = "site_icon";
        $nativeFavicon    = get_option($nativeFaviconKey, false);
        if (!$nativeFavicon) {
            foreach (['152', '144', 'fav'] as $type) {
                for ($i = 0; $i < 10; $i++) {
                    $iconType = get_option('options_favicons_' . $i . '_favicon_type');
                    if ($iconType == $type) {
                        $iconId = get_option('options_favicons_' . $i . '_favicon_icon');
                        if (is_numeric($iconId)) {
                            update_option($nativeFaviconKey, $iconId);
                        }
                        break 2;
                    }
                }
            }
        }
    }
}