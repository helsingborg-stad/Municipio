<?php

namespace Municipio\ExternalContent\SchemaObjectToWpPost\Helpers;

class Helpers implements GetSourceIdFromPostId
{
    public function __construct()
    {
    }

    public function getSourceIdFromPostId(int $postId): int
    {
        if ($postId < -1000) {
            // Return first 3 digits of the post ID.
            return (int) substr($postId, 1, 3);
        }

        return 0;
    }
}
