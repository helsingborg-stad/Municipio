<?php

namespace Municipio\Content\ResourceFromApi\ResourceRegistry;

interface SortByParentPostTypeInterface
{
    /**
     * Sorts the resources by parent post type.
     *
     * @param Resource[] $resources The resources to sort.
     * @return array The sorted resources.
     */
    public function sortByParentPostType(array $resources): array;
}
