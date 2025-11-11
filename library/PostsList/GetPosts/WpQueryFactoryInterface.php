<?php

namespace Municipio\PostsList\GetPosts;

interface WpQueryFactoryInterface
{
    public static function create($query = ''): \WP_Query;
}
