<?php

namespace Municipio\PostsList\QueryVarRegistrar;

use WpService\Contracts\AddFilter;

class QueryVarRegistrar implements QueryVarRegistrarInterface
{
    public function __construct(private AddFilter $wpService)
    {
    }

    public function register(string $queryVar): void
    {
        $this->wpService->addFilter('query_vars', fn($queryVars) => [...$queryVars, $queryVar]);
    }
}
