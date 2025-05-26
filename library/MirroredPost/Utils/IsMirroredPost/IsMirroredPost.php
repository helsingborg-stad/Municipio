<?php

namespace Municipio\MirroredPost\Utils\IsMirroredPost;

use Municipio\MirroredPost\Contracts\BlogIdQueryVar;
use WpService\Contracts\GetQueryVar;

/**
 * Class IsMirroredPost
 *
 * This class implements the IsMirroredPostInterface and provides a method to check if a post is mirrored.
 * In this implementation, it always returns false, indicating that the post is not mirrored.
 */
class IsMirroredPost implements IsMirroredPostInterface
{
    public function __construct(private GetQueryVar $wpService)
    {
    }

    /**
     * @inheritDoc
     */
    public function isMirrored(): bool
    {
        if (empty($this->wpService->getQueryVar(BlogIdQueryVar::BLOG_ID_QUERY_VAR, null))) {
            return false;
        }

        if (empty($this->wpService->getQueryVar('p', null))) {
            return false;
        }

        return true;
    }
}
