<?php

namespace Municipio\Helper;

use Municipio\Helper\Controller as ControllerHelper;

class ContentType
{
    /**
     * Returns an array of all the content types that are registered in the system.
     *
     * @param bool $includeExtras Include additional information about the registered content types in the returned array.
     *
     * @return array Array of registered content types.
     * If $includeExtras is true, each item in the array will be an array containing
     * the content type class instance, the class name with namespace and the file path of the content type class.
     * If $includeExtras is false, each item in the array will be the label of the content type instance.
     */
    public static function getRegisteredContentTypes(bool $includeExtras = false): array
    {
        $contentTypes = [];

        foreach (ControllerHelper::getControllerPaths() as $path) {
            if (is_dir($dir = $path . DIRECTORY_SEPARATOR . 'ContentType')) {
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
                        $contentTypes[$instance->getKey()] = [
                            'instance'  => $instance,
                            'className' => $classNameWithNamespace,
                            'path'      => $filename
                        ];
                    } else {
                        $contentTypes[$instance->getKey()] = $instance->getLabel();
                    }
                }
            }
        }

        return apply_filters('Municipio/ContentType/getRegisteredContentTypes', $contentTypes);
    }

    /**
     * Get the content type instance for a given type.
     *
     * @param string $type The type of content type to get.
     *
     * @return mixed The content type instance for the given type, or false if no content type is set.
     */
    public static function getContentType(string $type = '')
    {
        if (!$type) {
            $type = self::getCurrentType();
        }

        $contentType = false;
        $contentTypeStr = get_option("options_content_type_{$type}", false);

        if ($contentTypeStr) {
            $contentType = self::getContentTypeInstance($contentTypeStr);
        }

        return apply_filters('Municipio/ContentType/getContentType', $contentType, $type);
    }

    /**
     * Get an instance of a content type
     *
     * @param string content type The content type you want to get the instance of.
     *
     * @return The class instance of the content type.
     */
    private static function getContentTypeInstance(string $contentType)
    {
        $registeredContentTypes = self::getRegisteredContentTypes(true);

        if (isset($registeredContentTypes[$contentType]) && isset($registeredContentTypes[$contentType]['instance'])) {
            return $registeredContentTypes[$contentType]['instance'];
        }
    }
    /**
     * Checks if a $type has a specific content type set.
     *
     * @param string $content typeToCheckFor The content type to check for.
     * @param string $typeToCheck The type of content type to check.
     * @param boolean $includeSecondary If you want to include secondary content types in the check.
     *
     * @return boolean
     */

    public static function hasContentType(
        string $contentTypeToCheckFor = '',
        string $typeToCheck = ''
    ): bool {
        if ($contentType = self::getContentType($typeToCheck)) {
            if (self::checkMainContentType($contentType, $contentTypeToCheckFor)) {
                return true;
            }
            if (self::checkSecondaryContentType($contentType, $contentTypeToCheckFor)) {
                return true;
            }
        }
        return false;
    }

    private static function checkMainContentType(array $contentType, string $contentTypeToCheckFor): bool
    {
        foreach ($contentType as $item) {
            if ($contentTypeToCheckFor === $item->key) {
                return true;
            }
        }
        return false;
    }

    private static function checkSecondaryContentType(array $contentType, string $contentTypeToCheckFor): bool
    {
        foreach ($contentType as $item) {
            if (!empty($item->secondaryContentType)) {
                foreach ($item->secondaryContentType as $secondaryContentType) {
                    if ($contentTypeToCheckFor === $secondaryContentType->key) {
                        return true;
                    }
                }
            }
        }
        return false;
    }
    /**
     * Checks if a $type has any content type set.
     *
     * @param string type The type to check (post type or taxonomy). Defaults to the current type if left empty.
     */
    public static function hasAnyContentType(string $type = ''): bool
    {
        $contentType = self::getContentType($type);
        if (!empty($contentType)) {
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
     * Checks if the user has opted to skip the content type template for a specific type.
     *
     * @param string $type The type of template to check (post type or taxonomy). Defaults to an empty string.
     *
     * @return bool A boolean value indicating whether the user has opted to skip the content type template.
     */
    public static function skipContentTypeTemplate(string $postType = ''): bool
    {
        return (bool) get_option("skip_content_type_template_{$postType}", false);
    }
}
