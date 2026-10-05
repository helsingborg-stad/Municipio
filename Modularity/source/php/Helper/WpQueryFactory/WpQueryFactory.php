<?php

declare(strict_types=1);

namespace Modularity\Helper\WpQueryFactory;

class WpQueryFactory implements WpQueryFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function create(string|array $args = []): \WP_Query
    {
        return new \WP_Query($args);
    }
}
