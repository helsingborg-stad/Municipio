<?php

namespace Municipio\PostObject\Decorators;

use AllowDynamicProperties;
use Municipio\PostObject\PostObjectInterface;

/**
 * Backwards compatible PostObject.
 *
 * This class is used to make sure that the PostObjectInterface is backwards compatible with the old PostObject class.
 */
#[AllowDynamicProperties]
class BackwardsCompatiblePostObject extends AbstractPostObjectDecorator implements PostObjectInterface
{
    /**
     * Constructor.
     */
    public function __construct(PostObjectInterface $postObject, object $legacyPost)
    {
        $this->postObject = $postObject;

        foreach ($legacyPost as $key => $value) {
            if (!isset($this->{$key})) {
                $this->{$key} = $value;
            }
        }
    }
}
