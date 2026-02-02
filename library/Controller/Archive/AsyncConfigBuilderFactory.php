<?php

namespace Municipio\Controller\Archive;

use Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigInterface;
use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;

class AsyncConfigBuilderFactory
{
    public static function fromConfigs(
        $postsListConfigDTO,
        $postsListData,
        bool $isAsync = true
    ): array {
        $appearanceConfig = $postsListConfigDTO->getAppearanceConfig();
        $getPostsConfig = $postsListConfigDTO->getGetPostsConfig();
        $baseConfig = [
            'queryVarsPrefix' => $postsListConfigDTO->getQueryVarsPrefix(),
            'id' => $postsListData['id'] ?? null,
            'postType' => $getPostsConfig->getPostTypes()[0] ?? null,
            'dateSource' => $appearanceConfig->getDateSource() ?? 'post_date',
            'dateFormat' => method_exists($appearanceConfig, 'getDateFormat') && $appearanceConfig->getDateFormat() ? $appearanceConfig->getDateFormat()->value : 'date-time',
            'numberOfColumns' => method_exists($appearanceConfig, 'getNumberOfColumns') ? $appearanceConfig->getNumberOfColumns() : 1,
            'postsPerPage' => method_exists($getPostsConfig, 'getPostsPerPage') ? $getPostsConfig->getPostsPerPage() : 10,
            'paginationEnabled' => method_exists($getPostsConfig, 'paginationEnabled') ? $getPostsConfig->paginationEnabled() : true,
        ];
        return (new AsyncConfigBuilder())
            ->withBaseConfig($baseConfig)
            ->setAsyncId($postsListData['id'] ?? null)
            ->setIsAsync($isAsync)
            ->build();
    }
}
