<?php

declare(strict_types=1);

namespace Municipio\PostsList\ConfigMapper;

use Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigInterface;
use Municipio\PostsList\Config\FilterConfig\FilterConfigInterface;
use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;

/**
 * DTO for PostsList config objects
 */
class PostsListConfigDTO
{
    public function __construct(
        public GetPostsConfigInterface $getPostsConfig,
        public AppearanceConfigInterface $appearanceConfig,
        public FilterConfigInterface $filterConfig,
        public string $queryVarsPrefix = '',
    ) {}
}
