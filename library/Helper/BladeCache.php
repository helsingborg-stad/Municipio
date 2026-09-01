<?php

namespace Municipio\Helper;

/**
 * Utility methods for resolving and clearing Blade cache directories.
 */
class BladeCache
{
    /**
     * Determines whether the current execution context is CLI.
     *
     * @param string|null $phpSapi The PHP SAPI to evaluate.
     * @return bool
     */
    public static function isCliExecution(?string $phpSapi = null): bool
    {
        $phpSapi = $phpSapi ?? PHP_SAPI;

        return in_array($phpSapi, ['cli', 'phpdbg'], true);
    }

    /**
     * Returns the configured Blade cache directories used by the theme.
     *
     * @param string|null $themeRootPath The absolute path to the theme root.
     * @param string|null $configuredBladeCachePath An explicit Blade cache path override.
     * @return array<int, string>
     */
    public static function getConfiguredCachePaths(?string $themeRootPath = null, ?string $configuredBladeCachePath = null): array
    {
        $themeRootPath = $themeRootPath ?? dirname(__DIR__, 2);
        $wpContentPath = dirname($themeRootPath, 2);

        $paths = [
            self::resolveConfiguredBladeCachePath($configuredBladeCachePath),
            sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'blade-cache',
            $wpContentPath . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'blade-cache',
        ];

        $paths = array_filter(array_map(fn(string $path): string => rtrim($path, DIRECTORY_SEPARATOR), array_filter($paths)));

        return array_values(array_unique($paths));
    }

    /**
     * Clears each configured Blade cache directory while preserving the directory itself.
     *
     * @param string|null $themeRootPath The absolute path to the theme root.
     * @param string|null $configuredBladeCachePath An explicit Blade cache path override.
     * @return array<string, string>
     */
    public static function clearConfiguredCacheDirectories(?string $themeRootPath = null, ?string $configuredBladeCachePath = null): array
    {
        $results = [];

        foreach (self::getConfiguredCachePaths($themeRootPath, $configuredBladeCachePath) as $cachePath) {
            $results[$cachePath] = self::clearDirectory($cachePath);
        }

        return $results;
    }

    /**
     * Clears a directory if it exists.
     *
     * @param string $directoryPath The directory whose contents should be deleted.
     * @return string
     */
    public static function clearDirectory(string $directoryPath): string
    {
        if (!is_dir($directoryPath)) {
            return 'missing';
        }

        $contents = scandir($directoryPath);

        if ($contents === false) {
            return 'failed';
        }

        foreach ($contents as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            if (!self::deletePath($directoryPath . DIRECTORY_SEPARATOR . $item)) {
                return 'failed';
            }
        }

        return 'cleared';
    }

    /**
     * Resolves the configured Blade cache path.
     *
     * @param string|null $configuredBladeCachePath An explicit Blade cache path override.
     * @return string|null
     */
    private static function resolveConfiguredBladeCachePath(?string $configuredBladeCachePath = null): ?string
    {
        if (defined('BLADE_CACHE_PATH') && is_string(constant('BLADE_CACHE_PATH')) && constant('BLADE_CACHE_PATH') !== '') {
            return constant('BLADE_CACHE_PATH');
        }

        if (is_string($configuredBladeCachePath) && $configuredBladeCachePath !== '') {
            return $configuredBladeCachePath;
        }

        return null;
    }

    /**
     * Deletes a file system path recursively.
     *
     * @param string $path The path to delete.
     * @return bool
     */
    private static function deletePath(string $path): bool
    {
        if (is_link($path) || is_file($path)) {
            return unlink($path);
        }

        if (!is_dir($path)) {
            return true;
        }

        $contents = scandir($path);

        if ($contents === false) {
            return false;
        }

        foreach ($contents as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            if (!self::deletePath($path . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }

        return rmdir($path);
    }
}
