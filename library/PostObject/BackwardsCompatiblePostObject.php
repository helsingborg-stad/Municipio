<?php

namespace Municipio\PostObject;

use AllowDynamicProperties;
use Municipio\PostObject\PostObjectInterface;

/**
 * Backwards compatible PostObject.
 *
 * This class is used to make sure that the PostObjectInterface is backwards compatible with the old PostObject class.
 */
#[AllowDynamicProperties]
class BackwardsCompatiblePostObject extends PostObjectDecorator
{
    /**
     * Constructor.
     */
    public function __construct(private PostObjectInterface $postObject, private object $legacyPost)
    {
        parent::__construct($postObject);

        foreach (get_object_vars($this->legacyPost) as $key => $value) {
            $this->{$key} = $value;
        }
    }
}
