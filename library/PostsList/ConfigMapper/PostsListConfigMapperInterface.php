<?php

declare(strict_types=1);

namespace Municipio\PostsList\ConfigMapper;

/**
 * Interface for mapping data sources to PostsList config objects
 */
interface PostsListConfigMapperInterface
{
    /**
     * @param mixed $sourceData
     * @return PostsListConfigDTOInterface
     */
    public function map(mixed $sourceData): PostsListConfigDTOInterface;
}
