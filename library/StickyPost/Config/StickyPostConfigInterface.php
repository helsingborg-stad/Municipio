<?php

namespace Municipio\StickyPost\Config;

/**
 * Represents a StickyPostConfigInterface interface.
 *
 * This interface is responsible for defining the methods that a sticky post configuration class must implement.
 */
interface StickyPostConfigInterface
{
    /**
     * Get the prefix for the option key.
     *
     * @return string
     */
    public function getOptionKeyPrefix(): string;
}
