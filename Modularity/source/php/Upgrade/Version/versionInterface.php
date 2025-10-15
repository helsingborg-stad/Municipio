<?php

namespace Modularity\Upgrade\Version;

/**
 * Interface MigratorInterface
 * 
 * This interface defines the contract for migrators.
 */
interface versionInterface
{
    /**
     * Upgrades to a new version
     * 
     * @return bool
     */
    public function upgrade():bool;
}