<?php

namespace Municipio\Content\ResourceFromApi\Modifiers;

use Municipio\Helper\ResourceFromApiHelper;

class ModifyPreLoadAcfValue
{
    private ModifiersHelperInterface $modifiersHelper;

    public function __construct(ModifiersHelperInterface $modifiersHelper)
    {
        $this->modifiersHelper = $modifiersHelper;
    }

    public function handle($value, $postId, $field)
    {
        if (!isset($field['name']) || !ResourceFromApiHelper::isRemotePostID($postId)) {
            return $value;
        }

        $registeredPostType = $this->modifiersHelper->getResourceFromPostId($postId);
        $remotePostId = ResourceFromApiHelper::getRemoteId($postId, $registeredPostType);

        return $registeredPostType->getMeta($remotePostId, $field['name']) ?? $value;
    }
}
