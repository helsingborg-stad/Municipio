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
     */
    public static function getRegisteredContentTypes(bool $includeExtras = false): array
    {
        $contentTypes = [];
        $subDirs      = ['Simple', 'Complex']; // Define the subdirectories to search in

        foreach (ControllerHelper::getControllerPaths() as $path) {
            foreach ($subDirs as $subDir) {
                $contentTypes = array_merge(
                    $contentTypes,
                    self::processSubDir($path, $subDir, $includeExtras)
                );
            }
        }

        return apply_filters('Municipio/ContentType/getRegisteredContentTypes', $contentTypes);
    }

    /**
     * Processes a subdirectory and returns content types found within.
     *
     * @param string $path Base path to the controller.
     * @param string $subDir Name of the subdirectory.
     * @param bool $includeExtras Whether to include extra information.
     * @return array Content types found within the subdirectory.
     */
    protected static function processSubDir(string $path, string $subDir, bool $includeExtras): array
    {
        $contentTypes = [];
        $dirPath      = $path . DIRECTORY_SEPARATOR . 'ContentType' . DIRECTORY_SEPARATOR . $subDir;

        if (is_dir($dirPath)) {
            foreach (glob("$dirPath/*.php") as $filename) {
                $contentTypes = array_merge(
                    $contentTypes,
                    self::processContentTypeFile($filename, $includeExtras)
                );
            }
        }

        return $contentTypes;
    }

    /**
     * Processes a content type file and returns an array representing the content type.
     *
     * @param string $filename Path to the content type class file.
     * @param bool $includeExtras Whether to include extra information.
     * @return array An array representing the content type.
     */
    protected static function processContentTypeFile(string $filename, bool $includeExtras): array
    {
        $namespace              = ControllerHelper::getNamespace($filename);
        $className              = basename($filename, '.php');
        $classNameWithNamespace = $namespace . '\\' . $className;
        $instance               = new $classNameWithNamespace();

        if ($includeExtras) {
            return [
            $instance->getKey() => [
                'instance'  => $instance,
                'className' => $classNameWithNamespace,
                'path'      => $filename
            ]
            ];
        }

        return [$instance->getKey() => $instance->getLabel()];
    }/**
    * Get the content type instance for a given post type.
    *
    * @param string $postType The post type of content type to get. Defaults to the current post type if not specified.
    * @param boolean $returnInstance Whether to return the content type instance or just the key.
    *
    * @return mixed The content type instance for the given post type, or false if no content type is set.
    */
    public static function getContentType(string $postType = '', $returnInstance = true)
    {
        if (!$postType && !$postType = self::getCurrentType()) {
            return false;
        }

        $themeModName = "municipio_customizer_panel_content_types_{$postType}_content_type";
        $contentTypeKey = get_theme_mod($themeModName, false);

        if ($contentTypeKey) {
            if($returnInstance) {
                return self::getContentTypeInstance($contentTypeKey);
            } else {
                return $contentTypeKey;
            }
        }

        return false;
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

            if (
                !empty($contentType->secondaryContentType) && true === self::isSecondaryContentType(
                    (array) $contentType->secondaryContentType,
                    $contentTypeToCheckFor
                )
            ) {
                    return true;
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

    public static function getCurrentType(): ?string
    {
        return \Municipio\Helper\WP::getCurrentPostType();
    }

    /**
     * Checks if the user has opted to skip the content type template for a specific post type.
     *
     * @param string $postType The post type to check. Defaults to the current post type if not specified.
     *
     * @return bool A boolean value indicating whether the user has opted to skip the content type template for the specified post type.
     */
    public static function skipContentTypeTemplate(string $postType = ''): bool
    {
        if (!$postType) {
            $postType = self::getCurrentType();
        }

        $themeModName = "municipio_customizer_panel_content_types_{$postType}_skip_content_type_template";
        return (bool) get_theme_mod($themeModName, false);
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
                    __('%s tried to add %s as a secondary content type.\n\n
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
