<?php

namespace Municipio\Content\ResourceFromApi\Modifiers;

use Municipio\Content\ResourceFromApi\PostType\PostTypeResourceRequest;
use Municipio\Helper\RemotePosts;

class ModifyDefaultPostMetaData
{
    public function handle($value, int $objectId, $metaKey, $single, $metaType)
    {
        if( !RemotePosts::isRemotePostID($objectId) ) {
            return $value;
        }

        $registeredPostType = ModifiersHelper::getResourceFromPostId($objectId);

        if (is_null($registeredPostType)) {
            return $value;
        }

        $objectId = RemotePosts::getRemoteId($objectId, $registeredPostType);

        return PostTypeResourceRequest::getMeta($objectId, $metaKey, $registeredPostType, $single) ?? $value;
    }
}
