<?php

namespace Municipio\Controller\Archive\Mappers\GetPostsConfig;

use Municipio\Controller\Archive\Mappers\MapperInterface;

/**
 * Map terms from data
 */
class MapTermsFromData implements MapperInterface
{
    /**
     * Constructor
     */
    public function __construct(
        private \Municipio\PostsList\Config\FilterConfig\FilterConfigInterface $filterConfig,
        private \Municipio\PostsList\QueryVars\QueryVarsInterface $queryVars,
        private \WpService\Contracts\GetThemeMod&\WpService\Contracts\GetTerms $wpService
    ) {
    }

    /**
     * Map terms from data
     *
     * @param array $data
     * @return \WP_Term[]
     */
    public function map(array $data): array
    {
        if (!isset($data['wpQuery']) || !is_a($data['wpQuery'], \WP_Query::class)) {
            return [];
        }

        return $this->resolveTerms($data['wpQuery']);
    }

    /**
     * Resolve terms
     *
     * @param \WP_Query|null $wpQuery
     * @return \WP_Term[]
     */
    private function resolveTerms(?\WP_Query $wpQuery = null): array
    {
        $currentTerm = $this->getCurrentTerm($wpQuery);

        return !is_null($currentTerm) ? [$currentTerm] :  $this->resolveTermsFromQueryParams();
    }

    /**
     * Get current term from WP_Query
     *
     * @param \WP_Query|null $wpQuery
     * @return \WP_Term|null
     */
    private function getCurrentTerm(?\WP_Query $wpQuery = null): ?\WP_Term
    {
        if (is_null($wpQuery)) {
            return null;
        }

        if (!$wpQuery->is_tax && !$wpQuery->is_category && !$wpQuery->is_tag) {
            return null;
        }

        return is_a($wpQuery->queried_object, \WP_Term::class) ?  $wpQuery->queried_object : null;
    }

    /**
     * Resolve terms from query parameters
     *
     * @return \WP_Term[]
     */
    private function resolveTermsFromQueryParams(): array
    {
        return (new \Municipio\PostsList\Config\GetPostsConfig\GetTermsFromGetParams\GetTermsFromGetParams(
            $_GET,
            $this->filterConfig,
            $this->queryVars->getPrefix(),
            $this->wpService
        ))->getTerms();
    }
}
