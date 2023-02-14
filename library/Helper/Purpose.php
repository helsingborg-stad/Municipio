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

                    $className = ControllerHelper::getNamespace($filename) . '\\' . basename($filename, '.php');
                    $class = new $className();

                    if ($includeExtras) {
                        $purposes[$class->getKey()] = [
                            'label' => $class->getLabel(),
                            'class' => $className,
                            'path' => $filename
                        ];
                    } else {
                        $purposes[$class->getKey()] = $class->getLabel();
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
    public static function getPurposes(string $type = ''): array
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
                    return [];
                }
            } else {
                return [];
            }
        }

        $purposes = (array) get_option("options_purposes_{$type}", []);
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
        return (bool) count(self::getPurposes($type));
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
