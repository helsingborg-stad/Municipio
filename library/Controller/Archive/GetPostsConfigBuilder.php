<?php

namespace Municipio\Controller\Archive;

use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;
use Municipio\PostsList\Config\GetPostsConfig\OrderDirection;

/**
 * Builder for GetPostsConfig
 */
class GetPostsConfigBuilder
{
    private array $postTypes         = [];
    private bool $isFacettingEnabled = false;
    private string $orderBy          = 'date';
    private OrderDirection $order    = OrderDirection::DESC;
    private int $perPage             = 10;
    private string $dateSource       = 'post_date';
    private array $terms             = [];
    private int $currentPage         = 1;
    private string $search           = '';
    private string $dateFrom         = '';
    private string $dateTo           = '';

    /**
     * Set post types
     */
    public function setPostTypes(array $postTypes): self
    {
        $this->postTypes = $postTypes;
        return $this;
    }

    /**
     * Set is facetting enabled
     */
    public function setFacettingEnabled(bool $isFacettingEnabled): self
    {
        $this->isFacettingEnabled = $isFacettingEnabled;
        return $this;
    }

    /**
     * Set order by
     */
    public function setOrderBy(string $orderBy): self
    {
        $this->orderBy = $orderBy;
        return $this;
    }

    /**
     * Set order direction
     */
    public function setOrder(OrderDirection $order): self
    {
        $this->order = $order;
        return $this;
    }

    /**
     * Set posts per page
     */
    public function setPerPage(int $perPage): self
    {
        $this->perPage = $perPage;
        return $this;
    }

    /**
     * Set date source
     */
    public function setDateSource(string $dateSource): self
    {
        $this->dateSource = $dateSource;
        return $this;
    }

    /**
     * Set terms
     */
    public function setTerms(array $terms): self
    {
        $this->terms = $terms;
        return $this;
    }

    /**
     * Set current page
     */
    public function setCurrentPage(int $currentPage): self
    {
        $this->currentPage = $currentPage;
        return $this;
    }

    /**
     * Set search query
     */
    public function setSearch(string $search): self
    {
        $this->search = $search;
        return $this;
    }

    /**
     * Set date from
     */
    public function setDateFrom(string $dateFrom): self
    {
        $this->dateFrom = $dateFrom;
        return $this;
    }

    /**
     * Set date to
     */
    public function setDateTo(string $dateTo): self
    {
        $this->dateTo = $dateTo;
        return $this;
    }

    /**
     * Build GetPostsConfig
     */
    public function build(): GetPostsConfigInterface
    {
        return new class (
            $this->postTypes,
            $this->isFacettingEnabled,
            $this->orderBy,
            $this->order,
            $this->perPage,
            $this->dateSource,
            $this->terms,
            $this->currentPage,
            $this->search,
            $this->dateFrom,
            $this->dateTo
        ) extends \Municipio\PostsList\Config\GetPostsConfig\DefaultGetPostsConfig {
            /**
             * Constructor
             */
            public function __construct(
                private array $postTypes,
                private bool $isFacettingEnabled,
                private string $orderBy,
                private OrderDirection $order,
                private int $perPage,
                private string $dateSource,
                private array $terms,
                private int $currentPage,
                private string $search,
                private string $dateFrom,
                private string $dateTo
            ) {
            }

            /**
             * @inheritDoc
             */
            public function getPostTypes(): array
            {
                return $this->postTypes;
            }

            /**
             * @inheritDoc
             */
            public function getPostsPerPage(): int
            {
                return $this->perPage;
            }

            /**
             * @inheritDoc
             */
            public function isFacettingTaxonomyQueryEnabled(): bool
            {
                return $this->isFacettingEnabled;
            }

            /**
             * @inheritDoc
             */
            public function getOrderBy(): string
            {
                return $this->orderBy;
            }

            /**
             * @inheritDoc
             */
            public function getOrder(): \Municipio\PostsList\Config\GetPostsConfig\OrderDirection
            {
                return $this->order;
            }

            /**
             * @inheritDoc
             */
            public function getDateSource(): string
            {
                return $this->dateSource;
            }

            /**
             * @inheritDoc
             */
            public function getTerms(): array
            {
                return $this->terms;
            }

            /**
             * @inheritDoc
             */
            public function getPage(): int
            {
                return $this->currentPage;
            }

            /**
             * @inheritDoc
             */
            public function getSearch(): ?string
            {
                return $this->search;
            }

            /**
             * @inheritDoc
             */
            public function getDateFrom(): ?string
            {
                return $this->dateFrom;
            }

            /**
             * @inheritDoc
             */
            public function getDateTo(): ?string
            {
                return $this->dateTo;
            }
        };
    }
}
