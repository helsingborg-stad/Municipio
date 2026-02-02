<?php

namespace Municipio\Controller\Archive;

use Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigInterface;
use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;

class AsyncConfigBuilderFactory
{
    public static function fromConfigs(
        string $queryVarsPrefix,
        $id,
        string $postType,
        AppearanceConfigInterface $appearanceConfig,
        GetPostsConfigInterface $getPostsConfig
    ): array {
        return (new AsyncConfigBuilder())
            ->setQueryVarsPrefix($queryVarsPrefix)
            ->setId($id)
            ->setPostType($postType)
            ->setDateSource($appearanceConfig->getDateSource() ?? 'post_date')
            ->setDateFormat(method_exists($appearanceConfig, 'getDateFormat') && $appearanceConfig->getDateFormat() ? $appearanceConfig->getDateFormat()->value : 'date-time')
            ->setNumberOfColumns(method_exists($appearanceConfig, 'getNumberOfColumns') ? $appearanceConfig->getNumberOfColumns() : 1)
            ->setPostsPerPage(method_exists($getPostsConfig, 'getPostsPerPage') ? $getPostsConfig->getPostsPerPage() : 10)
            ->setPaginationEnabled(method_exists($getPostsConfig, 'paginationEnabled') ? $getPostsConfig->paginationEnabled() : true)
            ->build();
    }
}
