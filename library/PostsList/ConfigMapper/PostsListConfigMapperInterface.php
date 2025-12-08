<?php

declare(strict_types=1);

namespace Municipio\PostsList\ConfigMapper;

use Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigInterface;
use Municipio\PostsList\Config\FilterConfig\FilterConfigInterface;
use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;

/**
 * Interface for mapping data sources to PostsList config objects
 */
interface PostsListConfigMapperInterface
{
    /**
     * @param mixed $sourceData
     * @return PostsListConfigDTO
     */
    public function map(mixed $sourceData): PostsListConfigDTO;
}
