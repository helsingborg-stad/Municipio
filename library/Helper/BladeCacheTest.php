<?php

namespace Municipio\Helper;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class BladeCacheTest extends TestCase
{
    #[TestDox('isCliExecution returns true for CLI SAPIs')]
    public function testIsCliExecutionReturnsTrueForCliSapis(): void
    {
        $this->assertTrue(BladeCache::isCliExecution('cli'));
        $this->assertTrue(BladeCache::isCliExecution('phpdbg'));
    }

    #[TestDox('isCliExecution returns false for HTTP SAPIs')]
    public function testIsCliExecutionReturnsFalseForHttpSapis(): void
    {
        $this->assertFalse(BladeCache::isCliExecution('fpm-fcgi'));
        $this->assertFalse(BladeCache::isCliExecution('apache2handler'));
    }

    #[TestDox('getConfiguredCachePaths returns the default and legacy Blade cache directories')]
    public function testGetConfiguredCachePathsReturnsDefaultAndLegacyDirectories(): void
    {
        $themeRootPath = '/var/www/wp-content/themes/municipio';

        $paths = BladeCache::getConfiguredCachePaths($themeRootPath);

        $this->assertSame(
            [
                sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'blade-cache',
                '/var/www/wp-content/uploads/cache/blade-cache',
            ],
            $paths,
        );
    }

    #[TestDox('getConfiguredCachePaths includes an explicit configured Blade cache path once')]
    public function testGetConfiguredCachePathsIncludesExplicitConfiguredPathOnce(): void
    {
        $themeRootPath = '/var/www/wp-content/themes/municipio';
        $configuredBladeCachePath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'blade-cache';

        $paths = BladeCache::getConfiguredCachePaths($themeRootPath, $configuredBladeCachePath);

        $this->assertSame(
            [
                sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'blade-cache',
                '/var/www/wp-content/uploads/cache/blade-cache',
            ],
            $paths,
        );
    }

    #[TestDox('clearDirectory removes nested files and directories but keeps the cache directory')]
    public function testClearDirectoryRemovesNestedContentsButKeepsDirectory(): void
    {
        $directoryPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'municipio-blade-cache-test-' . uniqid();
        $nestedDirectoryPath = $directoryPath . DIRECTORY_SEPARATOR . 'nested';
        mkdir($nestedDirectoryPath, 0775, true);
        file_put_contents($directoryPath . DIRECTORY_SEPARATOR . 'compiled.php', 'compiled');
        file_put_contents($nestedDirectoryPath . DIRECTORY_SEPARATOR . 'view.php', 'view');

        $result = BladeCache::clearDirectory($directoryPath);

        $this->assertSame('cleared', $result);
        $this->assertDirectoryExists($directoryPath);
        $this->assertSame(['.', '..'], scandir($directoryPath));

        rmdir($directoryPath);
    }

    #[TestDox('clearConfiguredCacheDirectories reports missing directories without failing')]
    public function testClearConfiguredCacheDirectoriesReportsMissingDirectories(): void
    {
        $themeRootPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'municipio-theme-' . uniqid();
        mkdir($themeRootPath . DIRECTORY_SEPARATOR . 'library', 0775, true);

        $results = BladeCache::clearConfiguredCacheDirectories($themeRootPath);

        $this->assertSame(
            [
                sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'blade-cache' => is_dir(sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'blade-cache') ? 'cleared' : 'missing',
                dirname($themeRootPath, 2) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'blade-cache' => 'missing',
            ],
            $results,
        );

        rmdir($themeRootPath . DIRECTORY_SEPARATOR . 'library');
        rmdir($themeRootPath);
    }
}
