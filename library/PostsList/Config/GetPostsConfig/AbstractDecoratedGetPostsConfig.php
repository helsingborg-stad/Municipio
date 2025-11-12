<?php

namespace Municipio\PostsList\Config\GetPostsConfig;

/**
 * Abstract implementation of GetPostsConfigInterface used for decorating
 */
abstract class AbstractDecoratedGetPostsConfig implements GetPostsConfigInterface
{
    protected GetPostsConfigInterface $innerConfig ;

    /**
     * @inheritDoc
     */
    public function getPostTypes(): array
    {
        return $this->innerConfig->getPostTypes();
    }

    /**
     * @inheritDoc
     */
    public function getPostsPerPage(): int
    {
        return $this->innerConfig->getPostsPerPage();
    }

    /**
     * @inheritDoc
     */
    public function getPage(): int
    {
        return $this->innerConfig->getPage();
    }

    /**
     * @inheritDoc
     */
    public function isFacettingTaxonomyQueryEnabled(): bool
    {
        return $this->innerConfig->isFacettingTaxonomyQueryEnabled();
    }

    /**
     * @inheritDoc
     */
    public function getSearch(): ?string
    {
        return $this->innerConfig->getSearch();
    }

    /**
     * @inheritDoc
     */
    public function getOrderBy(): string
    {
        return $this->innerConfig->getOrderBy();
    }

    /**
     * @inheritDoc
     */
    public function getOrder(): OrderDirection
    {
        return $this->innerConfig->getOrder();
    }

    /**
     * @inheritDoc
     */
    public function getDateFrom(): ?string
    {
        return $this->innerConfig->getDateFrom();
    }

    /**
     * @inheritDoc
     */
    public function getDateTo(): ?string
    {
        return $this->innerConfig->getDateTo();
    }

    /**
     * @inheritDoc
     */
    public function getDateSource(): string
    {
        return $this->innerConfig->getDateSource();
    }

    /**
     * @inheritDoc
     */
    public function getTerms(): array
    {
        return $this->innerConfig->getTerms();
    }
}
