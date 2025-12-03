<?php

namespace Municipio\PostDecorators;

use AcfService\Contracts\GetFields;
use WP_Post;

class ApplyBookingLinkToPlace implements PostDecorator
{
    public function __construct(
        private GetFields $acfService,
        private ?PostDecorator $inner = new NullDecorator()
    ) {
    }

    public function apply(WP_Post $post): WP_Post
    {
        $post = $this->inner->apply($post);

        if (!empty($post->schemaObject) && $post->schemaObject['@type'] !== 'Place') {
            return $post;
        }

        $fields            = $this->acfService->getFields($post->ID ?? null);
        $post->bookingLink = $fields['booking_link'] ?? false;

        return $post;
    }
}
