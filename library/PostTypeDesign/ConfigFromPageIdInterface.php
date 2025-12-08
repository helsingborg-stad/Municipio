<?php

namespace Municipio\PostTypeDesign;

/**
 * Interface ConfigFromPageIdInterface
 *
 * This interface defines the contract for classes that provide configuration data based on a design ID.
 */
interface ConfigFromPageIdInterface
{
    /**
     * Get the configuration data for a given design ID.
     *
     * @param string $designId The ID of the design.
     * @return array The configuration data for the design.
     */
    public function get(string $designId): array;
}
