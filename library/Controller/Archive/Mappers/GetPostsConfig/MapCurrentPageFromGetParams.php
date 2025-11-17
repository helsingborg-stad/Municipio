<?php

namespace Municipio\Controller\Archive\Mappers\GetPostsConfig;

use Municipio\Controller\Archive\Mappers\MapperInterface;
use Municipio\PostsList\QueryVars\QueryVarsInterface;

/**
 * Map current page from GET params
 */
class MapCurrentPageFromGetParams implements MapperInterface
{
    /**
     * Constructor
     *
     * @param array $getParams
     * @param QueryVarsInterface $queryVars
     */
    public function __construct(private array $getParams, private QueryVarsInterface $queryVars)
    {
    }

    /**
     * Map current page from GET params
     *
     * @param array $data
     * @return int
     */
    public function map(array $data): int
    {
        return $this->getParams[$this->queryVars->getPaginationParameterName()] ?? 1;
    }
}
