<?php

namespace Municipio\PostDecorators;

use WpService\Contracts\GetPostMeta;

class ApplyProjectProgress implements PostDecorator
{
    public function __construct(private ?PostDecorator $inner = new NullDecorator(), private GetPostMeta $wpService)
    {}

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