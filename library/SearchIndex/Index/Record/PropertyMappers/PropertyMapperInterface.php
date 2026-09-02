<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Index\Record\PropertyMappers;

/**
 * Maps one search record property from a WordPress post.
 */
interface PropertyMapperInterface
{
    /**
     * Map a property value from the given post.
     */
    public function mapProperty(\WP_Post $post): mixed;
}