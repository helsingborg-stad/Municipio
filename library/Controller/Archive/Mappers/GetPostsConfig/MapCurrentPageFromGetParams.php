<?php

namespace Municipio\Controller\Archive\Mappers\GetPostsConfig;

use Municipio\Controller\Archive\Mappers\MapperInterface;
use Municipio\PostsList\QueryVars\QueryVarsInterface;

class MapCurrentPageFromGetParams implements MapperInterface
{
    public function __construct(private array $getParams, private QueryVarsInterface $queryVars)
    {
    }

    public function map(array $data): int
    {
        return $this->getParams[$this->queryVars->getPaginationParameterName()] ?? 1;
    }
}
