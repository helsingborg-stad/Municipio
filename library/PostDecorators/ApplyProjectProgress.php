<?php

namespace Municipio\PostDecorators;

use WpService\Contracts\GetPostMeta;

/**
 * ApplyProjectProgress class.
 *
 * This class is a PostDecorator implementation that applies project progress to a post.
 */
class ApplyProjectProgress implements PostDecorator
{
    /**
     * @param GetPostMeta $wpService The WordPress service for retrieving post meta.
     * @param PostDecorator|null $inner The inner post decorator. Defaults to a NullDecorator.
     */
    public function __construct(private GetPostMeta $wpService, private ?PostDecorator $inner = new NullDecorator())
    {
    }

    /**
     * Applies project progress to a post.
     *
     * @param \WP_Post $post The post to apply progress to.
     * @return \WP_Post The post with progress applied.
     */
    public function apply(\WP_Post $post): \WP_Post
    {
        $post = $this->inner->apply($post);

        if (empty($post->schemaObject) || $post->schemaObject->getType() !== 'Project') {
            return $post;
        }

        $post->progress = (int) $this->wpService->getPostMeta($post->ID, 'progress', true) ?? 0;

        return $post;
    }
}
