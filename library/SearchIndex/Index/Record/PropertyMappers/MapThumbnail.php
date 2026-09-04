<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Index\Record\PropertyMappers;

use WpService\Contracts\GetPostThumbnailId;
use WpService\Contracts\GetThePostThumbnailUrl;

/**
 * Maps the post thumbnail URL.
 */
class MapThumbnail implements PropertyMapperInterface
{
    public function __construct(private GetPostThumbnailId&GetThePostThumbnailUrl $wpService)
    {
    }

    /**
     * Map the thumbnail URL at the search result image size.
     */
    public function mapProperty(\WP_Post $post): string
    {
        if (!$this->wpService->getPostThumbnailId($post)) {
            return '';
        }

        $thumbnail = $this->wpService->getThePostThumbnailUrl($post, [480, 270]);

        return is_string($thumbnail) ? $thumbnail : '';
    }
}