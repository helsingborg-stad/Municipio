<?php

namespace Municipio\Content\ResourceFromApi\Modifiers;

use Municipio\Content\ResourceFromApi\ResourceRegistryInterface;
use Municipio\Content\ResourceFromApi\ResourceType;

/**
 * Class ModifyMunicipioArchiveGetTaxonomyFiltersOptionValue
 */
class ModifyMunicipioArchiveGetTaxonomyFiltersOptionValue
{
    private ResourceRegistryInterface $resourceRegistry;

    /**
     * Class constructor.
     *
     * @param ResourceRegistryInterface $resourceRegistry The resource registry.
     */
    public function __construct(ResourceRegistryInterface $resourceRegistry)
    {
        $this->resourceRegistry = $resourceRegistry;
    }

    /**
     * Handle the modification of the page data.
     *
     * @param mixed $pageData The page data.
     * @param mixed $queriedObject The queried object.
     * @param mixed $queriedObjectData The queried object data.
     * @return mixed The modified page data.
     */
    public function handle($pageData, $queriedObject, $queriedObjectData)
    {
        if (is_null($pageData) || !is_a($queriedObject, 'WP_Post')) {
            return $pageData;
        }

        $resources         = $this->resourceRegistry->getByType(ResourceType::POST_TYPE);
        $matchingResources = array_filter($resources, fn ($r) => $r->getName() === $queriedObject->post_type);

        if (empty($matchingResources)) {
            return $pageData;
        }

        if ($queriedObject->post_parent === 0) {
            return $pageData;
        }

        $parentPosts = get_posts(['post__in' => [$queriedObject->post_parent], 'suppress_filters' => false]);

        if (empty($parentPosts)) {
            return $pageData;
        }

        array_splice($pageData, -1, 0, [
            [
                'label'   => $parentPosts[0]->post_title,
                'href'    => get_post_permalink($parentPosts[0]),
                'current' => false
            ],
        ]);

        return $pageData;
    }
}
