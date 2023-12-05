<?php

namespace Municipio\Content\ResourceFromApi\Modifiers;

use Municipio\Content\ResourceFromApi\ResourceRegistryInterface;
use Municipio\Content\ResourceFromApi\ResourceType;

class ModifyCleanObjectTermCache
{
    private ResourceRegistryInterface $resourceRegistry;

    public function __construct(ResourceRegistryInterface $resourceRegistry)
    {
        $this->resourceRegistry = $resourceRegistry;
    }

    public function handle($object_ids, $object_type)
    {
        $resources = $this->resourceRegistry->getByType(ResourceType::POST_TYPE);
        $matchingResources = array_filter($resources, fn ($resource) => $resource->getName() === $object_type);

        if (empty($matchingResources)) {
            return;
        }

        $postType = get_post_type_object($object_type);
        $postTypeTaxonomies = $postType->taxonomies;

        foreach ($postTypeTaxonomies as $taxonomyname => $taxonomy) {
            $cacheGroup = 'termQueryRemoteResourceResults-' . $taxonomyname;
            wp_cache_flush_group($cacheGroup);
        }
    }
}
