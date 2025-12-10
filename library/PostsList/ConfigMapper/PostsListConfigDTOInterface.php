<?php

declare(strict_types=1);

namespace Municipio\PostsList\ConfigMapper;

use Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigInterface;
use Municipio\PostsList\Config\FilterConfig\FilterConfigInterface;
use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;

/**
 * DTO for PostsList config objects
 */
interface PostsListConfigDTOInterface
{
    public function getGetPostsConfig(): GetPostsConfigInterface;

    public function getAppearanceConfig(): AppearanceConfigInterface;

    public function getFilterConfig(): FilterConfigInterface;

    public function getQueryVarsPrefix(): string;
}
