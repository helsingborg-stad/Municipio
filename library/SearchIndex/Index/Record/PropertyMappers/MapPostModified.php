<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Index\Record\PropertyMappers;

/**
 * Maps the post modification timestamp.
 */
class MapPostModified implements PropertyMapperInterface
{
    /**
     * Map the modification date to a Unix timestamp.
     */
    public function mapProperty(\WP_Post $post): int|false
    {
        return strtotime($post->post_modified);
    }
}