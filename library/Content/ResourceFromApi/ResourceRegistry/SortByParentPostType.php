<?php

namespace Municipio\Content\ResourceFromApi\ResourceRegistry;

use Municipio\Content\ResourceFromApi\ResourceInterface;

/**
 * Class SortByParentPostType
 */
class SortByParentPostType implements SortByParentPostTypeInterface
{
    /**
     * Sorts the resources by parent post type.
     *
     * @param ResourceInterface[] $resources The resources to sort.
     * @return array The sorted resources.
     */
    public function sortByParentPostType(array $resources): array
    {
        usort($resources, function ($a, $b) {
            $aParentPostTypes = $this->getParentPostTypesFromResource($a);
            $bParentPostTypes = $this->getParentPostTypesFromResource($b);

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

    /**
     * Gets the parent post types from a resource.
     *
     * @param ResourceInterface $resource The resource to get the parent post types from.
     * @return array The parent post types.
     */
    private function getParentPostTypesFromResource(ResourceInterface $resource): array
    {
        $parentPostTypes = $resource->getArguments()['parent_post_types'] ?? [];
        $sanitized       = array_filter(is_array($parentPostTypes) ? $parentPostTypes : []);

        return $sanitized;
    }
}
