<?php

namespace Modularity\Upgrade\Migrators;

/**
 * Interface MigratorInterface
 * 
 * This interface defines the contract for migrators.
 */
interface MigratorInterface
{
    /**
     * Perform migration.
     * Blocks: returns the data attached to the block.
     * Modules: returns a boolean based on field update success.
     * 
     * @return mixed|bool 
     */
    public function migrate():mixed;
}