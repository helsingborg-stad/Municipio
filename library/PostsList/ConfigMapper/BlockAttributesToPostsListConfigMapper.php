<?php

declare(strict_types=1);

namespace Municipio\PostsList\ConfigMapper;

use Municipio\PostsList\QueryVars\QueryVarsInterface;
use WpService\Contracts\GetTerms;

/**
 * Maps block attributes to PostsList config objects
 */
class BlockAttributesToPostsListConfigMapper implements PostsListConfigMapperInterface
{
    public function __construct(
        private GetTerms $wpService,
        private QueryVarsInterface $queryVars,
    ) {}

    public function map(mixed $attributes): PostsListConfigDTOInterface
    {
        $getPostsConfig = (new \Municipio\PostsList\Block\PostsListBlockRenderer\ConfigMappers\BlockAttributesToGetPostsConfigMapper($this->wpService))->map(
            $attributes,
        );
        $appearanceConfig = (new \Municipio\PostsList\Block\PostsListBlockRenderer\ConfigMappers\BlockAttributesToAppearanceConfigMapper())->map(
            $attributes,
        );
        $filterConfig = (new \Municipio\PostsList\Block\PostsListBlockRenderer\ConfigMappers\BlockAttributesToFilterConfigMapper($this->queryVars))->map(
            $attributes,
        );

        return new PostsListConfigDTO($getPostsConfig, $appearanceConfig, $filterConfig, $this->queryVars->getPrefix());
    }
}
