<?php

namespace Municipio\Content\ResourceFromApi\Modifiers;

use Municipio\Helper\RemotePosts;

class ModifyDefaultPostMetaData
{
    private ModifiersHelperInterface $modifiersHelper;

    public function __construct(ModifiersHelperInterface $modifiersHelper)
    {
        $this->modifiersHelper = $modifiersHelper;
    }

    public function handle($value, int $objectId, $metaKey, $single, $metaType)
    {
        if (!RemotePosts::isRemotePostID($objectId)) {
            return $value;
        }

        $registeredPostType = $this->modifiersHelper->getResourceFromPostId($objectId);

        if (is_null($registeredPostType)) {
            return $value;
        }

        $objectId = RemotePosts::getRemoteId($objectId, $registeredPostType);

        return $registeredPostType->getMeta($objectId, $metaKey, $single) ?? $value;
    }
}
