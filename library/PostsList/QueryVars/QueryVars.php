<?php

declare(strict_types=1);

namespace Municipio\PostsList\QueryVars;

/**
 * Manages query variable names with a specific prefix
 */
class QueryVars implements QueryVarsInterface
{
    /**
     * Constructor
     *
     * @param string $prefix The prefix to use for query variables
     * @param array<string> $taxonomies allowed taxonomy parameter names
     */
    public function __construct(
        private string $prefix,
        private array $taxonomies = [],
    ) {}

    /**
     * @inheritDoc
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }

    /**
     * @inheritDoc
     */
    public function getPaginationParameterName(): string
    {
        return $this->prefixParameter('page');
    }

    /**
     * @inheritDoc
     */
    public function getDateFromParameterName(): string
    {
        return $this->prefixParameter('date_from');
    }

    /**
     * @inheritDoc
     */
    public function getDateToParameterName(): string
    {
        return $this->prefixParameter('date_to');
    }

    /**
     * @inheritDoc
     */
    public function getSearchParameterName(): string
    {
        return $this->prefixParameter('search');
    }

    /**
     * Prefix a parameter name with the instance's prefix
     */
    private function prefixParameter(string $param): string
    {
        return $this->getPrefix() . $param;
    }

    public function getTaxonomyParameterNames(): array
    {
        return array_map(fn(string $taxonomyName) => $this->getPrefix() . $taxonomyName, $this->taxonomies);
    }
}
