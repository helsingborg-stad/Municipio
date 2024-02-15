<?php

namespace Municipio\Content\ResourceFromApi\Modifiers;

use Municipio\Content\ResourceFromApi\ResourceRegistry\ResourceRegistryInterface;
use Municipio\Content\ResourceFromApi\ResourceType;

/**
 * Class ModifyCleanObjectTermCache
 */
class ModifyCleanObjectTermCache
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
     * Handle the object IDs and object type.
     *
     * @param mixed $object_ids The object IDs.
     * @param string $object_type The object type.
     */
    public function handle($object_ids, $object_type)
    {
        $resources         = $this->resourceRegistry->getByType(ResourceType::POST_TYPE);
        $matchingResources = array_filter($resources, fn ($resource) => $resource->getName() === $object_type);

        if (empty($matchingResources)) {
            return;
        }

        $postType           = get_post_type_object($object_type);
        $postTypeTaxonomies = $postType->taxonomies;

        foreach ($postTypeTaxonomies as $taxonomyname => $taxonomy) {
            $cacheGroup = 'termQueryRemoteResourceResults-' . $taxonomyname;
            wp_cache_flush_group($cacheGroup);
        }
    }
}
