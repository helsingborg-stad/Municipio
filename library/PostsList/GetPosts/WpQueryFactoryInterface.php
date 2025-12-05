<?php

namespace Municipio\PostsList\GetPosts;

interface WpQueryFactoryInterface
{
    /**
     * Create WP_Query instance
     */
    public static function create($query = ''): \WP_Query;
}
