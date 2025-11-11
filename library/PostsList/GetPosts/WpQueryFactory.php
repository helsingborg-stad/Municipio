<?php

namespace Municipio\PostsList\GetPosts;

class WpQueryFactory implements WPQueryFactoryInterface
{
    public static function create($query = ''): \WP_Query
    {
        return new \WP_Query($query);
    }
}
