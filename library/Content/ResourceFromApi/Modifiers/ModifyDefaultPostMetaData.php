<?php

namespace Municipio\Content\ResourceFromApi\Modifiers;

use Municipio\Helper\ResourceFromApiHelper;

class ModifyDefaultPostMetaData
{
    private ModifiersHelperInterface $modifiersHelper;

    public function __construct(ModifiersHelperInterface $modifiersHelper)
    {
        $this->modifiersHelper = $modifiersHelper;
    }

    public function handle($value, int $objectId, $metaKey, $single, $metaType)
    {
        if (!ResourceFromApiHelper::isRemotePostID($objectId)) {
            return $value;
        }

        $registeredPostType = $this->modifiersHelper->getResourceFromPostId($objectId);

        if (is_null($registeredPostType)) {
            return $value;
        }

        $objectId = ResourceFromApiHelper::getRemoteId($objectId, $registeredPostType);

        return $registeredPostType->getMeta($objectId, $metaKey, $single) ?? $value;
    }
}
