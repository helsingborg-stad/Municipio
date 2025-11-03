<?php

namespace Municipio\PostsList\ViewUtilities\Schema\Project;

use Municipio\PostObject\PostObjectInterface;
use Municipio\PostsList\ViewUtilities\ViewUtilityInterface;

/*
 * View utility to get project progress percentage
 */
class GetSchemaProjectProgressPercentage implements ViewUtilityInterface
{
    /**
     * Get the callable for the view utility
     *
     * @return callable
     */
    public function getCallable(): callable
    {
        return function (PostObjectInterface $post): int {
            return (int) ($post->getSchemaProperty('status')['number'] ?? 0);
        };
    }
}
