<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Index\Record\PropertyMappers;

use WpService\Contracts\GetCurrentBlogId;

/**
 * Maps the current multisite blog ID.
 */
class MapBlogId implements PropertyMapperInterface
{
    public function __construct(private GetCurrentBlogId $wpService)
    {
    }

    /**
     * Map the current blog ID.
     */
    public function mapProperty(\WP_Post $post): int
    {
        return $this->wpService->getCurrentBlogId();
    }
}