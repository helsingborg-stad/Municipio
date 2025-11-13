<?php

namespace Municipio\Controller\Archive;

use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;
use Municipio\PostsList\Config\GetPostsConfig\OrderDirection;
use WpService\Contracts\GetThemeMod;

/**
 * Factory class for creating GetPostsConfig instances
 */
class GetPostsConfigFactory
{
    /**
     * Constructor
     */
    public function __construct(private GetThemeMod $wpService)
    {
    }

    /**
     * Create a GetPostsConfig instance
     *
     * @param array $data
     * @return GetPostsConfigInterface
     */
    public function create(array $data): GetPostsConfigInterface
    {
        $postType           = [$this->getPostType($data)];
        $isFacettingEnabled = $this->getFacettingType($data['archiveProps']);
        $orderBy            = $data['archiveProps']->orderBy ?? 'post_date';
        $perPage            = (int)$this->wpService->getThemeMod('archive_' . $this->getPostType($data) . '_post_count', 12);
        $dateSource         = $data['archiveProps']->dateField ?? 'post_date';
        $order              = $data['archiveProps']->orderDirection && strtoupper($data['archiveProps']->orderDirection) === 'ASC'
            ? OrderDirection::ASC
            : OrderDirection::DESC;

        return new class (
            $postType,
            $isFacettingEnabled,
            $orderBy,
            $order,
            $perPage,
            $dateSource
        ) extends \Municipio\PostsList\Config\GetPostsConfig\DefaultGetPostsConfig {
            /**
             * Constructor
             */
            public function __construct(
                private array $postType,
                private bool $isFacettingEnabled,
                private string $orderBy,
                private OrderDirection $order,
                private int $perPage,
                private string $dateSource
            ) {
            }

            /**
             * @inheritDoc
             */
            public function getPostTypes(): array
            {
                return $this->postType;
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
        };
    }

    /**
     * Get the post type for the archive
     *
     * @return string
     */
    private function getPostType(array $data): string
    {
        return !empty($data['postType']) ? $data['postType'] : 'page';
    }

    /**
     * Boolean function to determine if text search should be enabled
     *
     * @param   string      $postType   The current post type
     * @return  boolean                 True or false val.
     */
    private function getFacettingType($args): bool
    {
        if (!is_object($args)) {
            $args = (object) [];
        }

        if (!isset($args->filterType) || is_null($args->filterType)) {
            $args->filterType = false;
        }

        return (bool) $args->filterType;
    }
}
