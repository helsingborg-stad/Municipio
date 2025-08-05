<?php

namespace Municipio\Upgrade\Version;

class V21 implements \Municipio\Upgrade\VersionInterface
{
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