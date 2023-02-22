<?php

namespace Municipio\Helper;

use Municipio\Helper\Controller as ControllerHelper;

class Purpose
{
    /**
     * Returns an array of all the purposes that are registered in the system.
     *
     * @param bool $includeExtras Include additional information about the registered purposes in the returned array.
     *
     * @return array Array of registered purposes.
     * If $includeExtras is true, each item in the array will be an array containing
     * the purpose class instance, the class name with namespace and the file path of the purpose class.
     * If $includeExtras is false, each item in the array will be the label of the purpose instance.
     */
    public static function getRegisteredPurposes(bool $includeExtras = false): array
    {
        $cache_key = 'registered_purposes';
        $purposes = get_transient($cache_key);

        if (false === $purposes) {
            $purposes = [];

            foreach (ControllerHelper::getControllerPaths() as $path) {
                if (is_dir($dir = $path . DIRECTORY_SEPARATOR . 'Purpose')) {
                    foreach (glob("$dir/*.php") as $filename) {
                        // Skip files with Factory or Interface in the filename
                        if (preg_match('[Factory|Interface]', $filename)) {
                            continue;
                        }

                        $namespace = ControllerHelper::getNamespace($filename);
                        $className = basename($filename, '.php');
                        $classNameWithNamespace = $namespace . '\\' . $className;

                        $instance = new $classNameWithNamespace();

                        if ($includeExtras) {
                            $purposes[$instance->getKey()] = [
                            'class'     => $instance,
                            'className' => $classNameWithNamespace,
                            'path'      => $filename
                            ];
                        } else {
                            $purposes[$instance->getKey()] = $instance->getLabel();
                        }
                    }
                }
            }

            set_transient($cache_key, $purposes, 0); // No expiration time
        }

        return apply_filters('Municipio/Purpose/getRegisteredPurposes', $purposes);
    }

/**
 * It returns the purpose for a given type.
 *
 * @param string $type The type of data to get the purpose for. This can be a post type or taxonomy.
 *
 * @return string|false The purpose as a string or false if the purpose does not exist.
 */
    public static function getPurpose(string $type = '')
    {
        if ('' === $type) {
            $type = self::getCurrentType();
        }

        $purpose = self::getPurposeString($type);
        if (!$purpose) {
            return false;
        }

        return apply_filters('Municipio/Purpose/getPurpose', $purpose, $type, $current);
    }
    public static function hasPurpose(): string
    {
        return self::getPurpose();
    }


    private static function getPurposeString(string $type)
    {
        $mainPurposeKey = get_option("options_purposes_{$type}", false);
        if (!$mainPurposeKey) {
            return false;
        }

        $registeredPurposes = self::getRegisteredPurposes(true);
        $mainPurpose = $registeredPurposes[$mainPurposeKey]['class'] ?? null;

        return $mainPurpose;
    }

    private static function getCurrentType(string $current = ''): string
    {
        if ('' === $current) {
            $current = get_queried_object();
        }

        if (is_a($current, 'WP_Post_Type')) {
            $type = $current->name;
        } elseif (is_a($current, 'WP_Post')) {
            $type = $current->post_type;
        } elseif (is_a($current, 'WP_Term')) {
            $type = $current->taxonomy;
        } else {
            return '';
        }

        return $type;
    }


    /**
     * If the user has opted to skip the purpose template, return true. Otherwise, return false.
     *
     * @param string type The type of template to check.
     *
     * @return bool A boolean value.
     */
    public static function skipPurposeTemplate(string $type = ''): bool
    {
        return (bool) get_option("skip_purpose_template_{$type}", false);
    }
}
