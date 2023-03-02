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

        return apply_filters('Municipio/Purpose/getRegisteredPurposes', $purposes);
    }

/**
 * It returns the purpose for a given type.
 *
 * @param string $type The type of data to get the purpose for. This can be a post type or taxonomy.
 *
 * @return string The purpose as a string. Retruns an empty string if no purpose is found.
 */
    public static function getPurpose(string $type = '', bool $includeSecondary = false): array
    {
        if ('' === $type) {
            $type = self::getCurrentType();
        }

        $purpose = [];
        $purposeStr = get_option("options_purpose_{$type}", '');

        if ('' !== $purposeStr) {
            $instance = self::getPurposeInstance($purposeStr);
            $purpose[] = $instance;

            if ($includeSecondary && !empty($instance->secondaryPurpose)) {
                foreach ($instance->secondaryPurpose as $key => $className) {
                    $secondaryInstance = self::getPurposeInstance($key, false);
                    $purpose[] = $secondaryInstance;
                }
            }
        }

        return apply_filters('Municipio/Purpose/getPurpose', $purpose, $type);
    }
    /**
     * > Get the instance of a registered purpose
     *
     * @param string purpose The purpose you want to get the instance of.
     * @param bool init If true, the purpose will be initialized via it's init() method.
     *
     * @return The class instance of the purpose.
     */
    public static function getPurposeInstance(string $purpose, bool $init = false)
    {
        $registeredPurposes = self::getRegisteredPurposes(true);
        if (isset($registeredPurposes[$purpose]) && isset($registeredPurposes[$purpose]['class'])) {
            $instance = $registeredPurposes[$purpose]['class'];
            if (true === $init) {
                $instance->init();
            }
            return $instance;
        }

        return false;
    }

    /**
     * It checks if the purpose is empty or not.
     *
     * @param string type The type of the purpose.
     */
    public static function hasPurpose(string $type = ''): bool
    {
        $purpose = self::getPurpose();
        if (!empty($purpose)) {
            return true;
        }
        return false;
    }
    /**
     * Get the current type.
     *
     * @param string $current The current type.
     *
     * @return string The current type.
     */
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
