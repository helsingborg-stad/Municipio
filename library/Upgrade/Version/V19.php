<?php

namespace Municipio\Upgrade\Version;

class V19 implements \Municipio\Upgrade\VersionInterface
{
    /**
     * @inheritDoc
     */
    public function upgradeToVersion(): void
    {
        $previousSetting = get_option('options_activate_gutenberg_editor');

        if ($previousSetting) {
            update_option('gutenberg_editor_mode', 'all');
        } else {
            update_option('gutenberg_editor_mode', 'disabled');
        }

        delete_option('options_activate_gutenberg_editor');
        delete_option('_options_activate_gutenberg_editor');
    }
}