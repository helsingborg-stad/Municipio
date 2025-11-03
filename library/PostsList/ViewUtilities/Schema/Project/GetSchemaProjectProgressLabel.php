<?php

namespace Municipio\PostsList\ViewUtilities\Schema\Project;

use Municipio\PostObject\PostObjectInterface;
use Municipio\PostsList\ViewUtilities\ViewUtilityInterface;

/*
 * View utility to get project progress label
 */
class GetSchemaProjectProgressLabel implements ViewUtilityInterface
{
    /**
     * Get the callable for the view utility
     *
     * @return callable
     */
    public function getCallable(): callable
    {
        return function (PostObjectInterface $post): string {
            return $post->getSchemaProperty('status')['name'] ?? '';
        };
    }
}
