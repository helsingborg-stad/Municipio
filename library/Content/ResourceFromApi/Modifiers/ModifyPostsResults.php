<?php

namespace Municipio\Content\ResourceFromApi\Modifiers;

use WP_Query;

class ModifyPostsResults
{
    private ModifiersHelperInterface $modifiersHelper;

    public function __construct(ModifiersHelperInterface $modifiersHelper)
    {
        $this->modifiersHelper = $modifiersHelper;
    }

    public function handle(array $posts, WP_Query $query): array
    {
        $registeredPostType = $this->modifiersHelper->getResourceFromQuery($query);

        if (empty($registeredPostType)) {
            return $posts;
        }

        if ($query->is_single()) {
            $posts = $registeredPostType->getSingle($query->get('name'));
        } else {
            $queryVars = $this->modifiersHelper->prepareQueryArgsForRequest($query->query_vars, $registeredPostType);
            $posts = $registeredPostType->getCollection($queryVars);
            $headers = $registeredPostType->getCollectionHeaders($queryVars);
            $query->found_posts = $headers['x-wp-total'] ?? count($posts);
            $query->max_num_pages = $headers['x-wp-totalpages'] ?? 1;
        }

        return is_array($posts) ? $posts : [$posts];
    }
}
