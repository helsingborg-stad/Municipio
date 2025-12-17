<?php

declare(strict_types=1);

namespace Municipio\PostsList\Config\GetPostsConfig;

use Municipio\PostsList\Config\GetPostsConfig\GetTermsFromGetParams\GetTermsFromGetParams;

class GetPostsConfigUsingGetParamsDecorator extends AbstractDecoratedGetPostsConfig implements GetPostsConfigInterface
{
    /**
     * Constructor
     *
     * @param GetPostsConfigInterface $decorated
     * @param array $getParams
     * @param \Municipio\PostsList\QueryVars\QueryVarsInterface $queryVars
     */
    public function __construct(
        protected GetPostsConfigInterface $innerConfig,
        private array $getParams,
        private \Municipio\PostsList\QueryVars\QueryVarsInterface $queryVars,
        private GetTermsFromGetParams $getTermsFromGetParams,
    ) {}

    /**
     * Get search parameter
     *
     * @return string|null
     */
    public function getSearch(): null|string
    {
        return $this->getParams[$this->queryVars->getSearchParameterName()] ?? parent::getSearch();
    }

    public function getDateFrom(): null|string
    {
        return $this->getParams[$this->queryVars->getDateFromParameterName()] ?? parent::getDateFrom();
    }

    public function getDateTo(): null|string
    {
        return $this->getParams[$this->queryVars->getDateToParameterName()] ?? parent::getDateTo();
    }

    public function getTerms(): array
    {
        return [...$this->innerConfig->getTerms(), ...$this->getTermsFromGetParams->getTerms()];
    }

    public function getPage(): int
    {
        return (int) ($this->getParams[$this->queryVars->getPaginationParameterName()] ?? parent::getPage());
    }
}
