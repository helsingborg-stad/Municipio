<?php

namespace Modularity\Helper;

/**
 * Keeping track of a context var set by each initzialation of sidebar.
 * This is used to determine if we're in a sidebar or not. And what sidebar.
 *
 * All views have a magic var named $sidebarContext formatted as: sidebar.{$sidebar-id}
 *
 * @author Sebastian Thulin
 */
class Context
{
    private static $context = false;

    /**
     * Get a context
     *
     * @return void
     */
    public static function get()
    {
        return self::$context;
    }

    /**
     * Set a context
     *
     * @param string $context
     * @return void
     */
    public static function set(string $context)
    {
        self::$context = $context;
    }
}
