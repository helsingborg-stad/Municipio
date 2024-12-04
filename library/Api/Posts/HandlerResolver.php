<?php

namespace Municipio\Api\Posts;

use Municipio\Api\Posts\HandlerResolverInterface;

class HandlerResolver implements HandlerResolverInterface
{
    public function resolve($handler, array $params): ?array
    {
        if ($handler->getIdentifier() === $params['identifier']) {
            return $handler;
        }

        return null;
    }
}
