<?php

namespace Municipio\PostsList\Config\GetPostsConfig\GetSearchFromGetParams;

class GetSearchFromGetParams
{
    public function __construct(private array $getParams, private string $parameterName)
    {
    }

    public function getSearch(): string
    {
        return $this->getParams[$this->parameterName] ?? '';
    }
}
