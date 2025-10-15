<?php

namespace Modularity\Helper\WpQueryFactory;

interface WpQueryFactoryInterface
{
    /**
     * Create a new WP_Query instance.
     * 
     * @param string|array $query URL query string or array of vars.
     */
    public function create(string|array $args = []): \WP_Query;
}
