<?php

namespace Municipio\Upgrade\Version;

class V24 implements \Municipio\Upgrade\VersionInterface
{
    /**
     * @inheritDoc
     */
    public function upgradeToVersion(): void
    {
        do_action('municipio_store_theme_mod');
    }
}