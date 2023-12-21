<?php

namespace Municipio\Content\ResourceFromApi\Modifiers;

use Municipio\Helper\ResourceFromApiHelper;

/**
 * Class ModifyDefaultPostMetaData
 */
class ModifyDefaultPostMetaData
{
    private ModifiersHelperInterface $modifiersHelper;

    /**
     * ModifyDefaultPostMetaData constructor.
     *
     * @param ModifiersHelperInterface $modifiersHelper The modifiers helper instance.
     */
    public function __construct(ModifiersHelperInterface $modifiersHelper)
    {
        $this->modifiersHelper = $modifiersHelper;
    }

    /**
     * Handle the modification of default post meta data.
     *
     * @param mixed $value The original meta value.
     * @param int $objectId The object ID.
     * @param string $metaKey The meta key.
     * @param bool $single Whether to return a single value or an array of values.
     * @param string $metaType The meta type.
     * @return mixed The modified meta value.
     */
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
