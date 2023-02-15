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
    public static function getPurposes(string $type = ''): array|bool
    {
        if ('' === $type) {
            if ($current = get_queried_object()) {
                if (is_a($current, 'WP_Post_Type')) {
                    $type = $current->name;
                } elseif (is_a($current, 'WP_Post')) {
                    $type = $current->post_type;
                } elseif (is_a($current, 'WP_Term')) {
                    $type = $current->taxonomy;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        $mainPurpose = ucfirst(get_option("options_purposes_{$type}", false));
        if (!$mainPurpose) {
            return false;
        }

        $purposes = [];
        $registeredPurposes = self::getRegisteredPurposes(true);

        if (empty($registeredPurposes[$mainPurpose])) {
            return false;
        }

        if (isset($registeredPurposes[$mainPurpose]['class']) && class_exists($registeredPurposes[$mainPurpose]['class'])) {
            // Instantiate the main purpose
            $instance = new $registeredPurposes[$mainPurpose]['class']();
            $purposes['main'] = $instance;

            // Instantiate secondary purposes
            $secondaryPurpose = $instance->getSecondaryPurpose();
            if (!empty($secondaryPurpose)) {
                foreach ($secondaryPurpose as $key => $value) {
                    if (isset($registeredPurposes[$key]['class']) && class_exists($registeredPurposes[$key]['class'])) {
                        $purposes['secondary'][] = new $registeredPurposes[$key]['class']();
                    }
                }
            }
        }

        // Filter the purposes and return the result
        return apply_filters('Municipio/Purpose/getPurposes', $purposes, $type, $current);
    }
    /**
     * > If the current page is a post type, post or term, return true if the post type, post, or term taxonomy
     * has a purpose
     *
     * @param string type The type of object you want to check.
     * @return bool
     */
    public static function hasPurpose(string $type = ''): bool
    {
        if ('' === $type) {
            if ($current = get_queried_object()) {
                if (is_a($current, 'WP_Post_Type')) {
                    $type = $current->name;
                } elseif (is_a($current, 'WP_Post')) {
                    $type = $current->post_type;
                } elseif (is_a($current, 'WP_Term')) {
                    $type = $current->taxonomy;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
        return (bool) self::getPurposes($type);
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
