<?php

namespace Municipio\PostsList\ViewCallableProviders\Schema\Project;

use Municipio\PostObject\PostObjectInterface;
use Municipio\PostsList\ViewCallableProviders\ViewCallableProviderInterface;

/*
 * View utility to get project progress percentage
 */
class GetProgressPercentage implements ViewCallableProviderInterface
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
