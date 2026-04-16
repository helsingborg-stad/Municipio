<?php

namespace Municipio\Test;

use Composer\InstalledVersions;

/**
 * Trait to get all filters used in the theme, for testing purposes.
 */
trait GetThemeFilters
{
    public static function getThemeFilters(): array
    {
        static $filters = null;
        if ($filters !== null) {
            return $filters;
        }

        $filters = [];
        $rootPath = self::getThemeRootPath();
        $prefixes = ['Municipio', 'Modularity'];

        foreach (self::getPhpFiles($rootPath) as $filePath) {
            $content = file_get_contents($filePath);
            $foundFilters = self::extractFiltersFromContent($content, $prefixes);
            $filters = array_merge($filters, $foundFilters);
        }

        $filters = array_unique($filters);
        return $filters;
    }

    private static function getThemeRootPath(): string
    {
        $rootPackage = InstalledVersions::getRootPackage();
        return InstalledVersions::getInstallPath($rootPackage['name']);
    }

    private static function getPhpFiles(string $directory): \Generator
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory),
        );
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                yield $file->getRealPath();
            }
        }
    }

    private static function extractFiltersFromContent(string $content, array $prefixes): array
    {
        $filters = [];
        if (preg_match_all('/(apply_filters|applyFilters)\(\s*[\'"]([^\'"]+)[\'"]/', $content, $matches)) {
            foreach ($matches[2] as $filter) {
                foreach ($prefixes as $prefix) {
                    if (str_starts_with($filter, $prefix)) {
                        $filters[] = $filter;
                        break;
                    }
                }
            }
        }
        return $filters;
    }
}
