<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Index\Record\PropertyMappers;

use WpService\Contracts\GetPostType;
use WpService\Contracts\GetPostTypeObject;

/**
 * Maps the display name of the post type.
 */
class MapPostTypeName implements PropertyMapperInterface
{
    public function __construct(private GetPostType&GetPostTypeObject $wpService)
    {
    }

    /**
     * Map the post type plural label.
     */
    public function mapProperty(\WP_Post $post): string
    {
        $postType = $this->wpService->getPostType($post);
        $postTypeObject = is_string($postType)
            ? $this->wpService->getPostTypeObject($postType)
            : null;

        return $postTypeObject?->labels->name ?? '';
    }
}