<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Index\Record\PropertyMappers;

use WpService\Contracts\GetPostType;

/**
 * Maps the post type slug.
 */
class MapPostType implements PropertyMapperInterface
{
    public function __construct(private GetPostType $wpService)
    {
    }

    /**
     * Map the post type or an empty string when unavailable.
     */
    public function mapProperty(\WP_Post $post): string
    {
        $postType = $this->wpService->getPostType($post);

        return is_string($postType) ? $postType : '';
    }
}