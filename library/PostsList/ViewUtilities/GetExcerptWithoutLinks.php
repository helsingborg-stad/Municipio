<?php

namespace Municipio\PostsList\ViewUtilities;

use Municipio\Helper\Sanitize;
use Municipio\PostObject\PostObjectInterface;

class GetExcerptWithoutLinks implements ViewUtilityInterface
{
    /**
     * Get the callable for the view utility
     *
     * @return callable
     */
    public function getCallable(): callable
    {
        return function (PostObjectInterface $post): string {
            return Sanitize::sanitizeATags($post->getExcerpt());
        };
    }
}
