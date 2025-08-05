<?php

namespace Municipio\Upgrade\Version;

use Municipio\Upgrade\Version\Helper\DeleteThemeMod;
use Municipio\Upgrade\Version\Helper\MigrateThemeMod;

class V5 implements \Municipio\Upgrade\VersionInterface
{
    /**
     * @inheritDoc
     */
    public function upgradeToVersion(): void
    {
        MigrateThemeMod::migrate('widths', 'container', 'field_609bdcc8348d6');
        MigrateThemeMod::migrate('widths', 'container_frontpage', 'field_60928f237c070');
        MigrateThemeMod::migrate('widths', 'container_archive', 'field_609bdcad348d5');
        MigrateThemeMod::migrate('widths', 'container_content', 'field_609298276e5b2');

        MigrateThemeMod::migrate('widths', 'column_size_left', 'field_60d339b60049e');
        MigrateThemeMod::migrate('widths', 'column_size_right', 'field_60d3393d1231a');

        DeleteThemeMod::delete('widths');
    }
}

