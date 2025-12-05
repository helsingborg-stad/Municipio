<?php

namespace Municipio\StickyPost\Config;

/**
 * StickyPostConfig class.
 *
 * Represents the configuration for sticky posts in the theme.
 * Implements the StickyPostConfigInterface.
 *
 */
class StickyPostConfig implements StickyPostConfigInterface
{
    private string $prefix = 'sticky_post';

    /**
     * Get the prefix for the option key.
     *
     * @return string
     */
    public function getOptionKeyPrefix(): string
    {
        return $this->prefix;
    }
}
