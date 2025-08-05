<?php

namespace Municipio\Upgrade\Version;

use Municipio\Upgrade\Version\Helper\SetAssociativeThemeMod;

class V18 implements \Municipio\Upgrade\VersionInterface
{
    /**
     * @inheritDoc
     */
    public function upgradeToVersion(): void
    {
        $previousSetting = get_option('activate_gutenberg_editor');

        if ($previousSetting) {
            update_option('gutenberg_editor_mode', 'all');
        } else {
            update_option('gutenberg_editor_mode', 'disabled');
        }

        delete_option('activate_gutenberg_editor');
    }
}