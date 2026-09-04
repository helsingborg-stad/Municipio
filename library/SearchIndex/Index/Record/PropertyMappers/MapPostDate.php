<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Index\Record\PropertyMappers;

/**
 * Maps the post publication timestamp.
 */
class MapPostDate implements PropertyMapperInterface
{
    /**
     * Map the publication date to a Unix timestamp.
     */
    public function mapProperty(\WP_Post $post): int|false
    {
        return strtotime($post->post_date);
    }
}