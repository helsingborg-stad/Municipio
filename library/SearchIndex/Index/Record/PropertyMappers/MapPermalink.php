<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Index\Record\PropertyMappers;

use WpService\Contracts\GetPostPermalink;

/**
 * Maps the post permalink.
 */
class MapPermalink implements PropertyMapperInterface
{
    public function __construct(private GetPostPermalink $wpService)
    {
    }

    /**
     * Map the permalink or an empty string when unavailable.
     */
    public function mapProperty(\WP_Post $post): string
    {
        $permalink = $this->wpService->getPostPermalink($post);

        return is_string($permalink) ? $permalink : '';
    }
}