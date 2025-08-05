<?php

namespace Municipio\Upgrade\Version;

use Municipio\Upgrade\Version\Helper\DeleteThemeMod;
use Municipio\Upgrade\Version\Helper\MigrateThemeMod;

class V9 implements \Municipio\Upgrade\VersionInterface
{
    /**
     * @inheritDoc
     */
    public function upgradeToVersion(): void
    {
        MigrateThemeMod::migrate('padding', 'main_content_padding', 'field_611e43ec4dfa5');

        DeleteThemeMod::delete('padding');
    }
}

