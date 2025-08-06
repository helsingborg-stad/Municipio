<?php

namespace Municipio\Upgrade\Version;

class V31 implements \Municipio\Upgrade\VersionInterface
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
        $this->db->query("DELETE FROM {$this->db->postmeta} WHERE meta_key LIKE '_oembed%'");
    }
}