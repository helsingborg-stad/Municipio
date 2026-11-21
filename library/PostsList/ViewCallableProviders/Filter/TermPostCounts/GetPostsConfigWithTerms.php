<?php

declare(strict_types=1);

namespace Municipio\PostsList\ViewCallableProviders\Filter\TermPostCounts;

use Municipio\PostsList\Config\GetPostsConfig\AbstractDecoratedGetPostsConfig;
use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;
use WP_Term;

/**
 * Get posts config with the terms used for filtering replaced.
 */
class GetPostsConfigWithTerms extends AbstractDecoratedGetPostsConfig
{
    /**
     * Constructor
     *
     * @param GetPostsConfigInterface $innerConfig
     * @param WP_Term[] $terms
     */
    public function __construct(
        protected GetPostsConfigInterface $innerConfig,
        private array $terms,
    ) {}

    /**
     * @inheritDoc
     */
    public function getTerms(): array
    {
        return $this->terms;
    }
}
