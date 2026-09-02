<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Index\Record\PropertyMappers;

use WpService\Contracts\GetPost;
use WpService\Contracts\GetPostAncestors;

/**
 * Maps the title of a post's highest ancestor.
 */
class MapTopMostParent implements PropertyMapperInterface
{
    public function __construct(private GetPostAncestors&GetPost $wpService)
    {
    }

    /**
     * Map the highest ancestor title or the current title for a root post.
     */
    public function mapProperty(\WP_Post $post): string
    {
        $ancestors = $this->wpService->getPostAncestors($post);
        $topMostParentId = $ancestors !== [] ? end($ancestors) : $post->ID;
        $topMostParent = $this->wpService->getPost((int) $topMostParentId);

        return $topMostParent instanceof \WP_Post ? $topMostParent->post_title : '';
    }
}