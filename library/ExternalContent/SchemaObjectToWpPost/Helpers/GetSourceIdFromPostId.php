<?php

namespace Municipio\ExternalContent\SchemaObjectToWpPost\Helpers;

interface GetSourceIdFromPostId
{
    /**
     * Get source ID from post ID.
     *
     * @param int $postId
     * @return int Source ID. Returns 0 if source ID could not be found.
     */
    public function getSourceIdFromPostId(int $postId): int;
}
