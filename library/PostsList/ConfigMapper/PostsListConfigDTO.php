<?php

declare(strict_types=1);

namespace Municipio\PostsList\ConfigMapper;

use Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigInterface;
use Municipio\PostsList\Config\FilterConfig\FilterConfigInterface;
use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;

/**
 * DTO for PostsList config objects
 */
class PostsListConfigDTO implements PostsListConfigDTOInterface
{
    public function __construct(
        public GetPostsConfigInterface $getPostsConfig,
        public AppearanceConfigInterface $appearanceConfig,
        public FilterConfigInterface $filterConfig,
        public string $queryVarsPrefix = '',
    ) {}

    public function getGetPostsConfig(): GetPostsConfigInterface
    {
        return $this->getPostsConfig;
    }

    public function getAppearanceConfig(): AppearanceConfigInterface
    {
        return $this->appearanceConfig;
    }

    public function getFilterConfig(): FilterConfigInterface
    {
        return $this->filterConfig;
    }

    public function getQueryVarsPrefix(): string
    {
        return $this->queryVarsPrefix;
    }
}
