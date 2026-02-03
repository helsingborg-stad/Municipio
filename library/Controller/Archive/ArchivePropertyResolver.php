<?php

declare(strict_types=1);

namespace Municipio\Controller\Archive;

/**
 * Archive property resolver
 *
 * Utility class for resolving archive properties with support for both
 * camelCase and snake_case naming conventions. Centralizes property access
 * logic to handle inconsistent property naming across the codebase.
 */
class ArchivePropertyResolver
{
    /**
     * Resolve property with support for both camelCase and snake_case
     *
     * Checks for property existence in the following order:
     * 1. camelCase variant
     * 2. snake_case variant
     * 3. Default value
     *
     * @param object $props Archive properties object
     * @param string $camelCase Property name in camelCase format
     * @param string $snakeCase Property name in snake_case format
     * @param mixed $default Default value if property not found
     * @return mixed The resolved property value or default
     */
    public static function resolveProperty(
        object $props,
        string $camelCase,
        string $snakeCase,
        mixed $default = null
    ): mixed {
        return $props->$camelCase ?? $props->$snakeCase ?? $default;
    }

    /**
     * Resolve boolean property with support for both camelCase and snake_case
     *
     * Checks for property existence in the following order:
     * 1. camelCase variant
     * 2. snake_case variant
     * 3. Default value (false)
     *
     * @param object $props Archive properties object
     * @param string $camelCase Property name in camelCase format
     * @param string $snakeCase Property name in snake_case format
     * @return bool The resolved boolean value, defaults to false if not found
     */
    public static function resolveBool(
        object $props,
        string $camelCase,
        string $snakeCase
    ): bool {
        return (bool) self::resolveProperty($props, $camelCase, $snakeCase, false);
    }
}
