<?php

namespace Municipio\Helper;

/**
 * Class Wpdb
 *
 * Static class to hold the $wpdb instance.
 * Use this class when you are unable to pass the global $wpdb instance as a parameter.
 */
class Wpdb
{
    private static \wpdb $wpdb;

    /**
     * Set the global $wpdb instance.
     *
     * @param \wpdb $wpdb
     */
    public static function set(\wpdb $wpdb): void
    {
        self::$wpdb = $wpdb;
    }

    /**
     * Get the $wpdb instance.
     *
     * @return \wpdb
     */
    public static function get(): \wpdb
    {
        if (self::$wpdb === null) {
            throw new \Exception('wpdb not set');
        }

        return self::$wpdb;
    }
}
