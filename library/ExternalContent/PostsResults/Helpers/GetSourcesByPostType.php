<?php

namespace Municipio\ExternalContent\PostsResults\Helpers;

use WP_Query;

interface GetSourcesByPostType
{
    /**
     * Get sources by post type.
     *
     * @param string $postType
     * @return \Municipio\ExternalContent\Sources\ISource[]
     */
    public function getSourcesByPostType(string $postType): array;
}
