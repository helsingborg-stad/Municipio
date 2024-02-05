<?php

namespace Municipio\Helper;

use Municipio\Helper\Controller as ControllerHelper;
use Municipio\Controller\ContentType\ContentTypeFactory as ContentTypeFactory;
use Municipio\Helper\Listing as ListingHelper;

/**
 * Class SingularContentType
 */
class ContentType
{
    /**
     * Returns an array of all the content types that are registered in the system.
     *
     * @param bool $includeExtras Include additional information about the registered
     * content types in the returned array.
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
                    if (preg_match('[Factory|Interface|Test]', $filename)) {
                        continue;
                    }

                    $namespace              = ControllerHelper::getNamespace($filename);
                    $className              = basename($filename, '.php');
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

        $contentTypeInstance = false;
        $contentTypeKey      = get_option("options_contentType_{$type}", false);

        if ($contentTypeKey) {
            $contentTypeInstance = self::getContentTypeInstance($contentTypeKey);
        }

        return $contentTypeInstance;
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

        $contentType = self::getContentType($typeToCheck);

        if ($contentType) {
            if (true === self::isMainContentType($contentType, $contentTypeToCheckFor)) {
                return true;
            }

            if (!empty($contentType->secondaryContentType)) {
                if (
                    true === self::isSecondaryContentType(
                        (array) $contentType->secondaryContentType,
                        $contentTypeToCheckFor
                    )
                ) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check if the provided main content type matches the specified content type to check for.
     *
     * @param object $contentType The main content type to compare.
     * @param string $contentTypeToCheckFor The content type to check for.
     *
     * @return bool True if the main content type matches the specified content type, false otherwise.
     */
    private static function isMainContentType(object $contentType, string $contentTypeToCheckFor): bool
    {
        if ($contentTypeToCheckFor === $contentType->getKey()) {
            return true;
        }
        return false;
    }

    /**
     * Check if any secondary content type within the provided array matches the specified content type to check for.
     *
     * @param array $secondaryContentTypes An array of objects representing secondary content types.
     * @param string $contentTypeToCheckFor The content type to check for.
     *
     * @return bool True if any secondary content type matches the specified content type, false otherwise.
     */
    private static function isSecondaryContentType(array $secondaryContentTypes, string $contentTypeToCheckFor): bool
    {
        foreach ($secondaryContentTypes as $item) {
            if (!empty($item->secondaryContentType)) {
                foreach ($item->secondaryContentType as $secondaryContentType) {
                    if ($contentTypeToCheckFor === $secondaryContentType->getKey()) {
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

    /**
     *
     * @param ContentTypeFactory $contentType
     * @return boolean
     *
     */
    public static function isComplexContentType(ContentTypeFactory $contentType): bool
    {
        return in_array(
            'Municipio\\Controller\\ContentType\\ContentTypeComplexInterface',
            class_implements($contentType)
        );
    }

    /**
     *
     * Ensure that the content type is not complex.
     *
     * @param ContentTypeFactory $contentType
     * @return boolean
     *
     */
    public static function validateSimpleContentType(ContentTypeFactory $contentType, $parent): bool
    {
        if (self::isComplexContentType($contentType)) {
            $error = new \WP_Error(
                'invalid_content_type',
                sprintf(
                    __('%s tried to add %s as a secondary content type. 
                    Complex content types cannot add other complex content types.', 'municipio'),
                    get_class($parent),
                    get_class($contentType)
                )
            );
            error_log($error->get_error_message());
            return false;
        }
        return true;
    }

    /**
     * TODO Deprecate this function
     *
     * Get structured data for a post based on specified meta keys.
     *
     * @param int $postId The ID of the post.
     * @param array $structuredData An array containing existing structured data (optional).
     * @param array $meta An array of meta keys to retrieve and include in the structured data.
     *
     * @return array The structured data for the post based on the specified meta keys.
     */
    public static function getStructuredData(int $postId, array $structuredData = [], array $meta = []): array
    {

        foreach ($meta as $key) {
            $structuredData[$key] = \Municipio\Helper\WP::getField($key, $postId);

            if (empty($structuredData[$key])) {
                unset($structuredData[$key]);
            }
        }
        return $structuredData;
    }

    /**
     * Complement a place post with additional information if needed.
     *
     * @param mixed $post The post object or post ID to complement.
     * @param bool $complementPost Flag indicating whether to complement the post (default is true).
     *
     * @return mixed The complemented post object.
     */
    public static function complementPlacePost($post, $complementPost = true)
    {
        if ($complementPost) {
            $post = \Municipio\Helper\Post::preparePostObject($post);
        }

        $fields = get_fields($post->id);

        $post->bookingLink = $fields['booking_link'] ?? false;
        $post->placeInfo   = self::createPlaceInfoList($fields);

        return $post;
    }

    /**
     * Create a list of place information based on specified fields.
     *
     * @param array $fields An array of fields containing information about the place.
     *
     * @return array The list of place information.
     */
    public static function createPlaceInfoList($fields)
    {
        $list = [];
        // Phone number
        if (!empty($fields['phone'])) {
            $list['phone'] = ListingHelper::createListingItem(
                $fields['phone'],
                '',
                ['src' => 'call']
            );
        }

        // Website link (with fixed label)
        if (!empty($fields['website'])) {
            $list['website'] = ListingHelper::createListingItem(
                __('Visit website', 'municipio'),
                $fields['website'],
                ['src' => 'language'],
            );
        }

        // Apply filters to listing items
        $list = apply_filters(
            'Municipio/Controller/SingularContentType/listing',
            $list,
            $fields
        );

        return $list;
    }
}
