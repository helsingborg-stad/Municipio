<?php

namespace Municipio\Content\ResourceFromApi\Modifiers;

use Municipio\Content\ResourceFromApi\PostType\PostTypeResourceRequest;
use WP_Query;

class ModifyPostsResults
{
    public function handle(array $posts, WP_Query $query): array
    {
        $registeredPostType = ModifiersHelper::getResourceFromQuery($query);

        if (empty($registeredPostType)) {
            return $posts;
        }

        if ($query->is_single()) {
            $posts = PostTypeResourceRequest::getSingle($query->get('name'), $registeredPostType);
        } else {
            $queryVars = ModifiersHelper::prepareQueryArgsForRequest($query->query_vars, $registeredPostType);
            $posts = PostTypeResourceRequest::getCollection($registeredPostType, $queryVars);
            $headers = PostTypeResourceRequest::getCollectionHeaders($registeredPostType, $queryVars);
            $query->found_posts = $headers['x-wp-total'] ?? count($posts);
            $query->max_num_pages = $headers['x-wp-totalpages'] ?? 1;
        }

        return is_array($posts) ? $posts : [$posts];
    }
}
