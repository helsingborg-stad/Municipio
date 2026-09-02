<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Index\Record\PropertyMappers;

use WpService\Contracts\GetPostMeta;
use WpService\Contracts\GetPostThumbnailId;

/**
 * Maps the post thumbnail alternative text.
 */
class MapThumbnailAlt implements PropertyMapperInterface
{
    public function __construct(private GetPostThumbnailId&GetPostMeta $wpService)
    {
    }

    /**
     * Map the attachment alternative text when a thumbnail exists.
     */
    public function mapProperty(\WP_Post $post): string
    {
        $thumbnailId = $this->wpService->getPostThumbnailId($post);

        return $thumbnailId
            ? (string) $this->wpService->getPostMeta($thumbnailId, '_wp_attachment_image_alt', true)
            : '';
    }
}