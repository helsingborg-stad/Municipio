<?php

namespace Municipio\Controller\Archive;

use Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigInterface;
use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;

class AsyncConfigBuilderFactory
{
    public static function fromConfigs(
        array $baseConfig,
        $id,
        bool $isAsync = true
    ): array {
        return (new AsyncConfigBuilder())
            ->withBaseConfig($baseConfig)
            ->setAsyncId($id)
            ->setIsAsync($isAsync)
            ->build();
    }
}
