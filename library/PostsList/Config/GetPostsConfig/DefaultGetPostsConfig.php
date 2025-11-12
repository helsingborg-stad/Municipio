<?php

namespace Municipio\PostsList\Config\GetPostsConfig;

/**
 * Default implementation of GetPostsConfigInterface
 */
class DefaultGetPostsConfig implements GetPostsConfigInterface
{
    /**
     * @inheritDoc
     */
    public function getPostTypes(): array
    {
        return ['post'];
    }

    /**
     * @inheritDoc
     */
    public function getPostsPerPage(): int
    {
        return 10;
    }

    /**
     * @inheritDoc
     */
    public function getPage(): int
    {
        return 1;
    }

    /**
     * @inheritDoc
     */
    public function isFacettingTaxonomyQueryEnabled(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getSearch(): ?string
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getOrderBy(): string
    {
        return 'post_date';
    }

    /**
     * @inheritDoc
     */
    public function getOrder(): OrderDirection
    {
        return OrderDirection::DESC;
    }

    /**
     * @inheritDoc
     */
    public function getDateFrom(): ?string
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getDateTo(): ?string
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getDateSource(): string
    {
        return 'post_date';
    }

    /**
     * @inheritDoc
     */
    public function getTerms(): array
    {
        return [];
    }
}
