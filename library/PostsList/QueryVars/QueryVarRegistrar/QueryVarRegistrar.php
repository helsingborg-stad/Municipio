<?php

declare(strict_types=1);

namespace Municipio\PostsList\QueryVars\QueryVarRegistrar;

use Municipio\PostsList\QueryVars\QueryVarsInterface;
use WpService\Contracts\AddFilter;

/*
 * Registers query vars for posts list
 */
class QueryVarRegistrar implements QueryVarRegistrarInterface
{
    /**
     * Constructor
     */
    public function __construct(
        private QueryVarsInterface $queryVar,
        private AddFilter $wpService,
    ) {}

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->wpService->addFilter('query_vars', fn($queryVars) => [...$queryVars, ...$this->getArrayOfQueryVars()]);
    }

    /**
     * Get array of query vars to register
     */
    private function getArrayOfQueryVars(): array
    {
        return [
            ...$this->queryVar->getTaxonomyParameterNames(),
            $this->queryVar->getPaginationParameterName(),
            $this->queryVar->getDateFromParameterName(),
            $this->queryVar->getDateToParameterName(),
            $this->queryVar->getSearchParameterName(),
        ];
    }
}
