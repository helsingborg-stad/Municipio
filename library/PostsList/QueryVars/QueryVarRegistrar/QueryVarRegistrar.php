<?php

namespace Municipio\PostsList\QueryVars\QueryVarRegistrar;

use Municipio\PostsList\QueryVars\QueryVarsInterface;
use WpService\Contracts\AddFilter;

class QueryVarRegistrar implements QueryVarRegistrarInterface
{
    public function __construct(
        private QueryVarsInterface $queryVar,
        private AddFilter $wpService
    ) {
    }

    public function register(): void
    {
        $this->wpService->addFilter('query_vars', fn($queryVars) => [...$queryVars, ...$this->getArrayOfQueryVars()]);
    }

    private function getArrayOfQueryVars(): array
    {
        return [
            $this->queryVar->getPaginationParameterName(),
            $this->queryVar->getDateFromParameterName(),
            $this->queryVar->getDateToParameterName(),
            $this->queryVar->getSearchParameterName(),
        ];
    }
}
