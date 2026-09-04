<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Index\Record\PropertyMappers;

/**
 * Maps the WordPress post ID.
 */
class MapPostId implements PropertyMapperInterface
{
    /**
     * Map the post ID as a string.
     */
    public function mapProperty(\WP_Post $post): string
    {
        return (string) $post->ID;
    }
}