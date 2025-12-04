<?php

namespace Municipio\PostsList\Config\GetPostsConfig\GetParameterFromGetParams;

/*
 * Get parameter from GET params
 */
class GetParameterFromGetParams
{
    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * Get parameter from GET params
     *
     * @param array $getParams
     * @param string $parameterName
     * @return string|null
     */
    public function getParam(array $getParams, string $parameterName): ?string
    {
        return $getParams[$parameterName] ?? null ?: null;
    }
}
