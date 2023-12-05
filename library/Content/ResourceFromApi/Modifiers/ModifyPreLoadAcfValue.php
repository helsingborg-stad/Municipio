<?php

namespace Municipio\Content\ResourceFromApi\Modifiers;

use Municipio\Content\ResourceFromApi\PostType\PostTypeResourceRequest;
use Municipio\Helper\RemotePosts;

class ModifyPreLoadAcfValue
{
    public function handle($value, $postId, $field)
    {
        if (!isset($field['name']) || !RemotePosts::isRemotePostID($postId)) {
            return $value;
        }

        $registeredPostType = ModifiersHelper::getResourceFromPostId($postId);
        $remotePostId = RemotePosts::getRemoteId($postId, $registeredPostType);

        return PostTypeResourceRequest::getMeta($remotePostId, $field['name'], $registeredPostType) ?? $value;
    }
}
