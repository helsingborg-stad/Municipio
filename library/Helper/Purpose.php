<?php

namespace Municipio\Helper;

use Municipio\Helper\Controller as ControllerHelper;

class Purpose
{
    /**
     * @return array An array of all the purposes that are registered in the system.
     */
    public static function getRegisteredPurposes(bool $includeExtras = false): array
    {
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

                    if ($includeExtras) {
                        $purposes[$className] = [
                            'class' => $classNameWithNamespace,
                            'path' => $filename
                        ];
                    } else {
                        $purposes[$className] = $classNameWithNamespace;
                    }
                }
            }
        }

        return apply_filters('Municipio/Purpose/getRegisteredPurposes', $purposes);
    }

   /**
    * It returns an array of purposes for a given type.
    *
    * @param string $type The type of data to get the purposes for. This can be a post type or taxonomy.
    *
    * @return array An array of purposes.
    */
    public static function getPurposes(string $type = '')
    {
        $current = array();

        if (function_exists('get_queried_object') && !empty(get_queried_object())) {
            $current = get_queried_object();
        } else {
            return false;
        }

        if ('' === $type && !$current) {
            return false;
        }

        if ('' === $type && is_a($current, 'WP_Post_Type')) {
            $type = $current->name;
        } elseif ('' === $type && is_a($current, 'WP_Post')) {
            $type = $current->post_type;
        } elseif ('' === $type && is_a($current, 'WP_Term')) {
            $type = $current->taxonomy;
        }

        $purposes = self::getPurposesArray($type);
        if (!$purposes) {
            return false;
        }

        return apply_filters('Municipio/Purpose/getPurposes', $purposes, $type, $current);
    }

    private static function getPurposesArray(string $type)
    {
        $mainPurpose = ucfirst(get_option("options_purposes_{$type}", false));
        if (!$mainPurpose) {
            return false;
        }

        $purposes = [];
        $registeredPurposes = self::getRegisteredPurposes(true);
        $mainPurposeClass = $registeredPurposes[$mainPurpose]['class'] ?? null;

        if (!$mainPurposeClass || !class_exists($mainPurposeClass)) {
            return false;
        }

        // Instantiate the main purpose
        $instance = new $mainPurposeClass();
        $purposes['main'] = $instance;

        // Instantiate secondary purposes
        $secondaryPurpose = $instance->getSecondaryPurpose();
        if (!empty($secondaryPurpose)) {
            foreach ($secondaryPurpose as $key => $value) {
                $class = $registeredPurposes[$key]['class'] ?? null;
                if ($class && class_exists($class)) {
                    $purposes['secondary'][] = new $class();
                }
            }
        }

        return $purposes;
    }

    public static function hasPurpose(string $type = ''): bool
    {
        $current = get_queried_object();

        if ('' === $type) {
            $type = self::getCurrentType($current);
            if (!$type) {
                return false;
            }
        }

        return self::hasPurposes($type);
    }

    private static function getCurrentType($current): ?string
    {
        if (!$current) {
            return null;
        }

        if (is_a($current, 'WP_Post_Type')) {
            $type = $current->name;
        } elseif (is_a($current, 'WP_Post')) {
            $type = $current->post_type;
        } elseif (is_a($current, 'WP_Term')) {
            $type = $current->taxonomy;
        }

        return $type;
    }

    private static function hasPurposes(string $type): bool
    {
        $purposes = self::getPurposes($type);
        return (bool) $purposes;
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
