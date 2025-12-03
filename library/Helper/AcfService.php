<?php

namespace Municipio\Helper;

use AcfService\AcfService as OriginalAcfService;

/**
 * Class AcfService
 *
 * Static class to hold the AcfService instance.
 * Use this class when you are unable to pass the AcfService instance as a parameter.
 */
class AcfService
{
    private static ?OriginalAcfService $acfService = null;

    /**
     * Set the AcfService instance.
     *
     * @param OriginalAcfService $acfService
     */
    public static function set(OriginalAcfService $acfService): void
    {
        if (self::$acfService === null) {
            // Allow setting once to prevent accidental overwriting.
            self::$acfService = $acfService;
        }
    }

    /**
     * Get the AcfService instance.
     *
     * @return OriginalAcfService
     * @throws \Exception
     */
    public static function get(): OriginalAcfService
    {
        if (self::$acfService === null) {
            throw new \Exception('AcfService not set');
        }

        return self::$acfService;
    }
}
