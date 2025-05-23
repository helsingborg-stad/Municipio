<?php

namespace Municipio\MirroredPost\Utils\IsMirroredPost;

/**
 * Class IsMirroredPost
 *
 * This class implements the IsMirroredPostInterface and provides a method to check if a post is mirrored.
 * In this implementation, it always returns false, indicating that the post is not mirrored.
 */
class IsMirroredPost implements IsMirroredPostInterface
{
    /**
     * @inheritDoc
     */
    public function isMirrored(): bool
    {
        return false;
    }
}
