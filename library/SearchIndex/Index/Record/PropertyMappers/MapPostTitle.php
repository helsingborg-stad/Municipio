<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Index\Record\PropertyMappers;

use WpService\Contracts\ApplyFilters;

/**
 * Maps a post title to normalized searchable text.
 */
class MapPostTitle implements PropertyMapperInterface
{
    use SanitizesText;

    public function __construct(private ApplyFilters $wpService)
    {
    }

    /**
     * Map the post title.
     */
    public function mapProperty(\WP_Post $post): string
    {
        return $this->sanitizeText($this->wpService->applyFilters('the_title', $post->post_title));
    }
}