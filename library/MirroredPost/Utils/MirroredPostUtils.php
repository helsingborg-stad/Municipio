<?php

namespace Municipio\MirroredPost\Utils;

use Municipio\MirroredPost\Utils\GetOtherBlogId\GetOtherBlogIdInterface;
use Municipio\MirroredPost\Utils\IsMirroredPost\IsMirroredPostInterface;
use Municipio\MirroredPost\Utils\MirroredPostUtilsInterface;

/**
 * MirroredPostUtils class.
 *
 * This class provides utility methods for checking if a post is mirrored
 * and retrieving the ID of the other blog.
 */
class MirroredPostUtils implements MirroredPostUtilsInterface, GetOtherBlogIdInterface
{
    /**
     * Constructor.
     *
     * @param IsMirroredPostInterface $isMirroredPost The service to check if a post is mirrored.
     * @param GetOtherBlogIdInterface $getOtherBlogId The service to get the ID of the other blog.
     */
    public function __construct(
        private IsMirroredPostInterface $isMirroredPost,
        private GetOtherBlogIdInterface $getOtherBlogId
    ) {
    }

    /**
     * @inheritDoc
     */
    public function isMirrored(): bool
    {
        return $this->isMirroredPost->isMirrored();
    }

    /**
     * @inheritDoc
     */
    public function getOtherBlogId(): ?int
    {
        return $this->getOtherBlogId->getOtherBlogId();
    }
}
