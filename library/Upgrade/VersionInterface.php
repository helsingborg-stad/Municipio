<?php

namespace Municipio\Upgrade;

interface VersionInterface
{
    /**
     * Upgrade the system.
     *
     * This method should contain the logic to upgrade the system to this version.
     *
     * @return void
     * @throws \Exception If upgrade was not successful.
     */
    public function upgradeToVersion(): void;
}
