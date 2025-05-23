<?php

namespace Municipio\MirroredPost\Utils\IsMirroredPost;

interface IsMirroredPostInterface
{
    /**
     * Check if the post is mirrored.
     *
     * @return bool True if the post is mirrored, false otherwise.
     */
    public function isMirrored(): bool;
}
