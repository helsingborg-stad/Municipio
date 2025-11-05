<?php

namespace Municipio\PostsList\Config\GetPostsConfig;

/**
 * Default implementation of GetPostsConfigInterface
 */
class DefaultGetPostsConfig implements GetPostsConfigInterface
{
    /**
     * @inheritDoc
     */
    public function getPostTypes(): array
    {
        return ['post'];
    }

    /**
     * @inheritDoc
     */
    public function getPostsPerPage(): int
    {
        return 10;
    }

    public function isFacettingTaxonomyQueryEnabled(): bool
    {
        return false;
    }
}
