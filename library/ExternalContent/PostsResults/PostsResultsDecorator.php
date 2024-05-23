<?php

namespace Municipio\ExternalContent\PostsResults;

use WP_Query;

interface PostsResultsDecorator
{
    public function apply(array $posts, WP_Query $query): array;
}
