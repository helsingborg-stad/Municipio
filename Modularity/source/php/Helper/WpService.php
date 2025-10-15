<?php

namespace Modularity\Helper;

use WpService\WpService as OriginalWpService;

/**
 * Class WpService
 *
 * Static class to hold the WpService instance.
 * Use this class when you are unable to pass the WpService instance as a parameter.
 */
class WpService
{
    private static ?OriginalWpService $wpService = null;

    /**
     * Set the WpService instance.
     *
     * @param OriginalWpService $wpService
     */
    public static function set(OriginalWpService $wpService): void
    {
        self::$wpService = $wpService;
    }

    /**
     * Get the WpService instance.
     *
     * @return OriginalWpService|null
     */
    public static function get(): ?OriginalWpService
    {
        if (self::$wpService === null) {
            throw new \RuntimeException('WpService not set');
        }

        return self::$wpService;
    }
}
