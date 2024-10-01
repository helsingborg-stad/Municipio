<?php

namespace Municipio\Helper;

/**
 * Class GetGlobal
 *
 * This class is responsible for managing the global variables and caching them locally.
 */
class GetGlobal
{
    /**
     * Cache to store the local copy of global variables
     */
    protected static $cachedGlobals = [];

    public static function getGlobal($global): mixed
    {
        if (isset(self::$cachedGlobals[$global])) {
            return self::$cachedGlobals[$global];
        }

        global $$global;

        if (is_null($$global)) {
            return false;
        }

        self::$cachedGlobals[$global] = $$global;

        return self::$cachedGlobals[$global];
    }
}