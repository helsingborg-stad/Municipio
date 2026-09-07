<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Index\Record\PropertyMappers;

use WpService\Contracts\GetPermalink;

/**
 * Maps the post permalink.
 *
 * Uses get_permalink() rather than get_post_permalink() so that every post type,
 * including pages, resolves to the permalink structure configured in WordPress.
 */
class MapPermalink implements PropertyMapperInterface
{
    public function __construct(private GetPermalink $wpService)
    {
    }

    /**
     * Map the permalink or an empty string when unavailable.
     */
    public function mapProperty(\WP_Post $post): string
    {
        $permalink = $this->wpService->getPermalink($post);

        return is_string($permalink) ? $permalink : '';
    }
}