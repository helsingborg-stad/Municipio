<?php

namespace Municipio\Upgrade\Version;

class V1 implements \Municipio\Upgrade\VersionInterface
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
        // Copy and update code here
        return;
    }
}

