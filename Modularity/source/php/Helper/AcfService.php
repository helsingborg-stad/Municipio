<?php

namespace Modularity\Helper;

use AcfService\AcfService as OriginalAcfService;

/**
 * Class AcfService
 *
 * Static class to hold the AcfService instance.
 * Use this class when you are unable to pass the AcfService instance as a parameter.
 */
class AcfService
{
    private static null|OriginalAcfService $acfService = null;

    /**
     * Set the acfService instance.
     *
     * @param OriginalAcfService $acfService
     */
    public static function set(OriginalAcfService $acfService): void
    {
        self::$acfService = $acfService;
    }

    /**
     * Get the acfService instance.
     *
     * @return OriginalAcfService|null
     */
    public static function get(): null|OriginalAcfService
    {
        if (self::$acfService === null) {
            throw new \RuntimeException('AcfService not set');
        }

        return self::$acfService;
    }
}
