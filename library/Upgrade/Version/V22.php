<?php

namespace Municipio\Upgrade\Version;

use Municipio\Upgrade\Version\Helper\MigrateAcfOptionImageIdToThemeModUrl;
use Municipio\Upgrade\Version\Helper\MigrateAcfOptionToThemeMod;

class V22 implements \Municipio\Upgrade\VersionInterface
{
    /**
     * @inheritDoc
     */
    public function upgradeToVersion(): void
    {
        MigrateAcfOptionImageIdToThemeModUrl::migrate('logotype', 'logotype');
        MigrateAcfOptionImageIdToThemeModUrl::migrate('logotype_negative', 'logotype_negative');
        MigrateAcfOptionImageIdToThemeModUrl::migrate('logotype_emblem', 'logotype_emblem');

        MigrateAcfOptionToThemeMod::migrate('header_logotype', 'header_logotype');
    }
}