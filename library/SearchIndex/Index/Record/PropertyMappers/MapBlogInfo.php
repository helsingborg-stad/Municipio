<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Index\Record\PropertyMappers;

use WpService\Contracts\GetBloginfo;

/**
 * Maps a WordPress site information property.
 */
class MapBlogInfo implements PropertyMapperInterface
{
    public function __construct(
        private GetBloginfo $wpService,
        private string $property,
    ) {
    }

    /**
     * Map the configured site information property.
     */
    public function mapProperty(\WP_Post $post): string
    {
        return $this->wpService->getBloginfo($this->property);
    }
}