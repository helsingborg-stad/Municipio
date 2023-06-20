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
     * It returns an array of objects that represent the purpose of the current page
     *
     * @param string type The type of purpose to get. Defaults to the current type.
     * @param bool includeSecondary If you want to include secondary purposes.
     *
     * @return array An array of objects.
     */
    public static function getPurpose(string $type = ''): array
    {
        if (!$type) {
            $type = self::getCurrentType();
        }

        $purpose = [];
        $purposeStr = get_option("options_purpose_{$type}", false);

        if ($purposeStr) {
            $instance = self::getPurposeInstance($purposeStr);
            $purpose[] = $instance;

            if (!empty($instance->secondaryPurpose)) {
                foreach ($instance->secondaryPurpose as $key => $className) {
                    $secondaryInstance = self::getPurposeInstance($key);
                    if ($secondaryInstance) {
                        $purpose[] = $secondaryInstance;
                    }
                }
            }
        }

        return apply_filters('Municipio/Purpose/getPurpose', $purpose, $type);
    }
    /**
     * hasPurpose
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
     * hasAnyPurpose
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
     * getPurposeInstance
     * Get an instance of a specific purpose
     *
     * @param string purpose The purpose you want to get the instance of.
     *
     * @return The class instance of the purpose.
     */
    public static function getPurposeInstance(string $purpose)
    {
        $registeredPurposes = self::getRegisteredPurposes(true);

        if (isset($registeredPurposes[$purpose]) && isset($registeredPurposes[$purpose]['instance'])) {
            $instance = $registeredPurposes[$purpose]['instance'];

            return $instance;
        }
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
