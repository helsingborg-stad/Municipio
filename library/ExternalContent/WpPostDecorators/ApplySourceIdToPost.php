<?php

namespace Municipio\ExternalContent\WpPostDecorators;

use WP_Post;

class ApplySourceIdToPost
{
    public function __construct(private WP_Post $inner, private int $sourceId)
    {
        return $this;
    }

    public function apply(): WP_Post
    {
        $this->inner->ID = (int)($this->sourceId . $this->inner->ID);
        return $this->inner;
    }
}
