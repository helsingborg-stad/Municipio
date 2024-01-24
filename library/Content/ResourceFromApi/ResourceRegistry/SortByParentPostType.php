<?php

namespace Municipio\Content\ResourceFromApi\ResourceRegistry;

class SortByParentPostType implements SortByParentPostTypeInterface {

    /**
     * Sorts the resources by parent post type.
     * 
     * @param Resource[] $resources The resources to sort.
     * @return array The sorted resources.
     */
    public function sortByParentPostType(array $resources):array {

        // Sort the resources by parent post type using usort.
        usort($resources, function($a, $b) {
            $aParentPostTypes = $a->getArguments()['parent_post_types'] ?? [];
            $bParentPostTypes = $b->getArguments()['parent_post_types'] ?? [];

            if (empty($aParentPostTypes) && !empty($bParentPostTypes)) {
                return -1;
            }

            if (!empty($aParentPostTypes) && empty($bParentPostTypes)) {
                return 1;
            }

            return 0;
        });

        return $resources;
    }
}