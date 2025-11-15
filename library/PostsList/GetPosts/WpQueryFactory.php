<?php

namespace Municipio\PostsList\GetPosts;

/**
 * Factory for creating WP_Query instances
 */
class WpQueryFactory implements WpQueryFactoryInterface
{
    /**
     * @inheritDoc
     */
    public static function create($query = ''): \WP_Query
    {
        return new \WP_Query($query);
    }
}
