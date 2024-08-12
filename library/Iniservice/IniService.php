<?php

namespace Municipio\IniService;

/**
 * Class IniService
 */
class IniService implements IniServiceInterface
{
    /**
     * Get the value of the specified ini key.
     *
     * @param string $key The ini key.
     * @return string The value of the ini key.
     */
    public function get(string $key): string
    {
        return ini_get($key);
    }

    /**
     * Set the value of the specified ini key.
     *
     * @param string $key The ini key.
     * @param string $value The value to set.
     * @return void
     */
    public function set(string $key, string $value): void
    {
        ini_set($key, $value);
    }
}
