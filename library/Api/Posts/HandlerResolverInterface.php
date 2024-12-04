<?php

namespace Municipio\Api\Posts;



interface HandlerResolverInterface
{
    public function resolve(mixed $handler, array $params): ?array;
}
