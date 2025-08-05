<?php

namespace Municipio\Upgrade\Version;

class V31 implements \Municipio\Upgrade\VersionInterface
{
    /**
     * @inheritDoc
     */
    public function upgradeToVersion(): void
    {
        $db->query("DELETE FROM {$db->postmeta} WHERE meta_key LIKE '_oembed%'");
    }
}