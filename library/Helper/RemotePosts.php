<?php

namespace Municipio\Helper;

use Municipio\Content\ResourceFromApi\ResourceInterface;
use Municipio\Content\ResourceFromApi\ResourceRegistry;
use Municipio\Content\ResourceFromApi\ResourceType;

class RemotePosts
{
    public static function isRemotePostID($postID): bool
    {
        return is_numeric($postID) && (int)$postID < 0;
    }

    public static function getLocalID($id, ResourceInterface $resource): int
    {
        if (self::isRemotePostID($id)) {
            return $id;
        }

        $resourceId = $resource->getResourceID();
        return -(int)"{$resourceId}{$id}";
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

        $resources = ResourceRegistry::getByType(ResourceType::POST_TYPE);
        $resources = array_filter($resources, fn (ResourceInterface $resource) => $resource->getName() === $postType);

        if (empty($resources)) {
            return $id;
        }

        $resource = array_shift($resources);

        return self::getLocalAttachmentId($id, $resource);
    }
}
