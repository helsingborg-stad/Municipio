<?php

namespace Municipio\Content\ResourceFromApi\Modifiers;

use Municipio\Helper\ResourceFromApiHelper;

/**
 * Class ModifyPreLoadAcfValue
 */
class ModifyPreLoadAcfValue
{
    private ModifiersHelperInterface $modifiersHelper;

    /**
     * Class constructor.
     *
     * @param ModifiersHelperInterface $modifiersHelper The modifiers helper instance.
     */
    public function __construct(ModifiersHelperInterface $modifiersHelper)
    {
        $this->modifiersHelper = $modifiersHelper;
    }

    /**
     * Handle the modification of the ACF value.
     *
     * @param mixed $value The original value.
     * @param int $postId The post ID.
     * @param array $field The ACF field.
     * @return mixed The modified value.
     */
    public function handle($value, $postId, $field)
    {
        if (!isset($field['name']) || !ResourceFromApiHelper::isRemotePostID($postId)) {
            return $value;
        }

        $registeredPostType = $this->modifiersHelper->getResourceFromPostId($postId);
        $remotePostId       = ResourceFromApiHelper::getRemoteId($postId, $registeredPostType);

        return $registeredPostType->getMeta($remotePostId, $field['name']) ?? $value;
    }
}
