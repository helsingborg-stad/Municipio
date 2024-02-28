<?php

namespace Municipio\Helper;

class Controller
{
    /**
     * Tries to locate a controller
     * @param  string $controller Controller name
     * @return string             Controller path
     */
    public static function locateController($controller)
    {
        preg_match_all('/^(single|archive)-/i', $controller, $matches);

        $controllers = array(
            str_replace('.blade.php', '', self::camelCase(basename($controller, '.php')))
        );

        if (isset($matches[1][0])) {
            $controllers[] = self::camelCase($matches[1][0]);
        }

        foreach (self::getControllerPaths() as $path) {
            foreach ($controllers as $controller) {
                $file = $path . '/' . $controller . '.php';

                if (!file_exists($file)) {
                    continue;
                }

                return $file;
            }
        }

        return false;
    }

  /**
 * Creates view paths dynamically
 * @param  array    $viewPaths   All view paths that are statically entered.
 * @return array    $viewPaths  Contains all view paths available.
 */
    public static function getControllerPaths($controllerPaths = array())
    {
        $versions = apply_filters('Municipio/blade/controllerVersions', array_reverse(array("", "v3")));

        foreach ($versions as $versionKey => $version) {
            $controllerPaths[] = rtrim(
                get_stylesheet_directory()
                . DIRECTORY_SEPARATOR
                . "library"
                . DIRECTORY_SEPARATOR
                . "Controller"
                . DIRECTORY_SEPARATOR
                . $version,
                DIRECTORY_SEPARATOR
            );
        }

        // Temporary array to hold ContentType paths
        $contentTypePaths = [];

        // Check all registered controller paths for subdirectory "ContentType"
        foreach ($controllerPaths as $controllerPath) {
            $contentTypeBasePath = $controllerPath . DIRECTORY_SEPARATOR . "ContentType";
            if (is_dir($contentTypeBasePath)) {
                // Add ContentType base directory
                $contentTypePaths[] = $contentTypeBasePath;

                // Check for 'Complex' and 'Simple' subdirectories
                $subDirs = ['Complex', 'Simple'];
                foreach ($subDirs as $subDir) {
                    $subDirPath = $contentTypeBasePath . DIRECTORY_SEPARATOR . $subDir;
                    if (is_dir($subDirPath)) {
                        // Add 'Complex' and 'Simple' directories to paths
                        $contentTypePaths[] = $subDirPath;
                    }
                }
            }
        }

        // Merge and remove duplicates
        $controllerPaths = array_unique(array_merge($controllerPaths, $contentTypePaths));

        return apply_filters('Municipio/controllerPaths', $controllerPaths);
    }

    /**
     * String (words or slug) to camel case
     * e.g taxonomy-department -> TaxonomyDepartment
     * e.g taxonomy_department -> TaxonomyDepartment
     * e.g taxonomy department -> TaxonomyDepartment
     * @param  string $string Hyphen string
     * @return string         Camel cased string
     */
    public static function camelCase($string)
    {
        $cc = preg_replace_callback('/(?:^|-|_|\s)(.?)/', array(self::class, 'camelCaseParts'), $string);

        if (!empty($cc)) {
            return $cc;
        }

        return $string;
    }

    public static function camelCaseParts($parts)
    {
        return strtoupper($parts[1]);
    }

    /**
     * Get a class's namespace
     * @param  string $classPath Path to the class php file
     * @return string            Namespace or null
     */
    public static function getNamespace(string $classPath = ''): ?string
    {
        if ('' === $classPath) {
            return null;
        }
        $src = file_get_contents($classPath);

        if (preg_match('/namespace\s+(.+?);/', $src, $m)) {
            return $m[1];
        }

        return null;
    }
}
