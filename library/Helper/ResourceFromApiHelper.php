<?php

namespace Municipio\Helper;

use Municipio\Content\ResourceFromApi\ResourceInterface;
use Municipio\Content\ResourceFromApi\ResourceRegistry\ResourceRegistryInterface;
use Municipio\Content\ResourceFromApi\ResourceType;

/**
 * Class ResourceFromApiHelper
 * Helper class for resources obtained from an API.
 */
class ResourceFromApiHelper
{
    private static ?ResourceRegistryInterface $registry = null;

    /**
     * Initializes the helper class.
     *
     * @param ResourceRegistry $registry The registry of resources.
     * @return void
     */
    public static function initialize(ResourceRegistryInterface $registry): void
    {
        self::$registry = $registry;
    }

    /**
     * Checks if the given post ID is a remote post ID.
     *
     * @param mixed $postID The post ID to check.
     * @return bool True if the post ID is a remote post ID, false otherwise.
     */
    public static function isRemotePostID($postID): bool
    {
        return is_numeric($postID) && (int)$postID <= -1;
    }

    /**
     * Retrieves the local ID for a resource based on the provided ID and resource object.
     *
     * @param int $id The ID of the resource.
     * @param ResourceInterface $resource The resource object.
     * @return int The local ID.
     */
    public static function getLocalID($id, ResourceInterface $resource): int
    {
        if (self::isRemotePostID($id)) {
            return $id;
        }

        $resourceId = $resource->getResourceID();
        return -(int)"{$resourceId}{$id}";
    }

    /**
     * Retrieves the remote ID for a given local ID and resource.
     *
     * @param int $localId The local ID to retrieve the remote ID for.
     * @param ResourceInterface $resource The resource object.
     * @return int The remote ID.
     */
    public static function getRemoteId($localId, ResourceInterface $resource): int
    {
        if (!self::isRemotePostID($localId)) {
            return $localId;
        }

        return (int)substr_replace((string)absint($localId), '', 0, strlen((string)$resource->getResourceID()));
    }

    /**
     * Retrieves the local attachment ID based on the provided ID and resource.
     *
     * @param int $id The ID of the attachment.
     * @param ResourceInterface $resource The resource object.
     * @return int The local attachment ID.
     */
    public static function getLocalAttachmentId(int $id, ResourceInterface $resource): int
    {
        if (self::isRemotePostID($id) || $resource->getMediaResource() === null) {
            return $id;
        }

        return self::getLocalID($id, $resource->getMediaResource());
    }

    /**
     * Retrieves the local attachment ID by post type.
     *
     * @param int $id The post ID.
     * @param string $postType The post type.
     * @return int The local attachment ID.
     */
    public static function getLocalAttachmentIdByPostType(int $id, string $postType): int
    {
        if (self::isRemotePostID($id)) {
            return $id;
        }

        $resources = self::$registry->getByType(ResourceType::POST_TYPE);
        $resources = array_filter($resources, fn (ResourceInterface $resource) => $resource->getName() === $postType);

        if (empty($resources)) {
            return $id;
        }

        $resource = array_shift($resources);

        return self::getLocalAttachmentId($id, $resource);
    }

    /**
     * Find the closest size in a given set of sizes based on the provided size parameter.
     *
     * @param string|array $size The size parameter, either as a string (size name) or an array of dimensions.
     * @param object $sizes An object containing size data. E.g { "large": { "width": 1024, "height": 768 } }
     * @return string|null The name of the closest size, or null if no match is found.
     */
    public static function getClosestImageBySize($size, $sizes): ?string
    {
        // Convert shorthand "large" notation to corresponding dimensions
        if (is_string($size) && isset($sizes->$size)) {
            $size = [
                $sizes->$size->width ?? null,
                $sizes->$size->height ?? null,
            ];
        } elseif (is_string($size)) {
            return null;
        }

        // Convert size array to complete format [width, height]
        if (is_array($size)) {
            $size = [
                $size[0] ?? null,
                $size[1] ?? null,
            ];
        }

        // Initialize variables
        $closest_size = null;
        $closest_diff = PHP_INT_MAX;

        // Iterate through available sizes
        foreach ($sizes as $size_name => $size_data) {
            $width  = $size_data->width ?? null;
            $height = $size_data->height ?? null;

            // Check if the current size is larger than the target size
            if ($width >= $size[0] && $height >= $size[1]) {
                // Calculate the total difference
                $total_diff = abs($width - $size[0]) + abs($height - $size[1]);

                // Check if the current size is closer than the previous closest
                if ($total_diff < $closest_diff) {
                    $closest_diff = $total_diff;
                    $closest_size = $size_name;
                }
            }
        }

        return $closest_size;
    }
}
