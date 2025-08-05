<?php

namespace Municipio\Upgrade\Version;

class V15 implements \Municipio\Upgrade\VersionInterface
{
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