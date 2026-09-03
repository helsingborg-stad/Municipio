<?php

declare(strict_types=1);

/**
 * Records WP-CLI calls made by command tests.
 *
 * @mago-expect lint:class-name
 */
class WP_CLI
{
    /** @var array<int, array{string, array<int, mixed>}> */
    public static array $calls = [];

    /**
     * Record a WP-CLI method call.
     */
    public static function __callStatic(string $method, array $arguments): void
    {
        self::$calls[] = [$method, $arguments];
    }
}