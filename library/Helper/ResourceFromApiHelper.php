<?php

namespace Municipio\Helper;

use Municipio\Content\ResourceFromApi\ResourceInterface;
use Municipio\Content\ResourceFromApi\ResourceRegistry;
use Municipio\Content\ResourceFromApi\ResourceType;

class ResourceFromApiHelper
{
    private static ?ResourceRegistry $registry = null;

    public static function initialize(ResourceRegistry $registry): void
    {
        self::$registry = $registry;
    }

    public static function isRemotePostID($postID): bool
    {
        return is_numeric($postID) && (int)$postID <= -1;
    }

    public static function getLocalID($id, ResourceInterface $resource): int
    {
        if (self::isRemotePostID($id)) {
            return $id;
        }

        $resourceId = $resource->getResourceID();
        return -(int)"{$resourceId}{$id}";
    }

    public static function getRemoteId($localId, ResourceInterface $resource): int
    {
        if (!self::isRemotePostID($localId)) {
            return $localId;
        }

        return (int)substr_replace((string)absint($localId), '', 0, strlen((string)$resource->getResourceID()));
    }

    public static function getLocalAttachmentId(int $id, ResourceInterface $resource): int
    {
        if (self::isRemotePostID($id) || $resource->getMediaResource() === null) {
            return $id;
        }

        return self::getLocalID($id, $resource->getMediaResource());
    }

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
     *                           If it's a string, it can be a size name or a shorthand notation like "large".
     *                           If it's an array, it should contain width and height.
     *                           If either width or height is left out, it should be set to null.
     * @param object $sizes An object containing size data. Each property should represent a size with 'width' and 'height'.
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
        } else if (is_string($size)) {
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
            $width = $size_data->width ?? null;
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
