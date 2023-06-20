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
                            'instance'  => $instance,
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
     * Get the purpose instance for a given type.
     *
     * @param string $type The type of purpose to get.
     *
     * @return mixed The purpose instance for the given type, or false if no purpose is set.
     */
    public static function getPurpose(string $type = '')
    {
        if (!$type) {
            $type = self::getCurrentType();
        }

        $purpose = false;
        $purposeStr = get_option("options_purpose_{$type}", false);

        if ($purposeStr) {
            $purpose = self::getPurposeInstance($purposeStr);
        }

        return apply_filters('Municipio/Purpose/getPurpose', $purpose, $type);
    }

    /**
     * Get an instance of a purpose
     *
     * @param string purpose The purpose you want to get the instance of.
     *
     * @return The class instance of the purpose.
     */
    private static function getPurposeInstance(string $purpose)
    {
        $registeredPurposes = self::getRegisteredPurposes(true);

        if (isset($registeredPurposes[$purpose]) && isset($registeredPurposes[$purpose]['instance'])) {
            return $registeredPurposes[$purpose]['instance'];
        }
    }
    /**
     * Checks if a $type has a specific purpose set.
     *
     * @param string $purposeToCheckFor The purpose to check for.
     * @param string $typeToCheck The type of purpose to check.
     * @param boolean $includeSecondary If you want to include secondary purposes in the check.
     *
     * @return boolean
     */

    public static function hasPurpose(
        string $purposeToCheckFor = '',
        string $typeToCheck = ''
    ): bool {
        $purpose = self::getPurpose($typeToCheck);
        if (!empty($purpose)) {
            foreach ($purpose as $key => $value) {
                // Check if the main purpose matches the purpose we're checking for.
                if ($purposeToCheckFor === $value->key) {
                    return true;
                }
                // Check if any of the secondary purposes matches the purpose we're checking for.
                if (!empty($value->secondaryPurpose)) {
                    foreach ($value->secondaryPurpose as $secondaryPurpose) {
                        if ($purposeToCheckFor === $secondaryPurpose->key) {
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }
    /**
     * Checks if a $type has any purpose set.
     *
     * @param string type The type to check (post type or taxonomy). Defaults to the current type if left empty.
     */
    public static function hasAnyPurpose(string $type = ''): bool
    {
        $purpose = self::getPurpose($type);
        if (!empty($purpose)) {
            return true;
        }
        return false;
    }

    /**
     * Get the current type based on the queried object.
     *
     * @param string $current The current queried object. Defaults to an empty string.
     *
     * @return string The current type (post type or taxonomy).
     */
    private static function getCurrentType(string $current = ''): string
    {
        if (!$current) {
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
     * Checks if the user has opted to skip the purpose template for a specific type.
     *
     * @param string $type The type of template to check (post type or taxonomy). Defaults to an empty string.
     *
     * @return bool A boolean value indicating whether the user has opted to skip the purpose template.
     */
    public static function skipPurposeTemplate(string $type = ''): bool
    {
        return (bool) get_option("skip_purpose_template_{$type}", false);
    }
}
