<?php

namespace Municipio\Content\ResourceFromApi\Modifiers;

use Municipio\Content\ResourceFromApi\ResourceRegistryInterface;
use Municipio\Content\ResourceFromApi\ResourceType;

class ModifyGetObjectTerms
{
    private ResourceRegistryInterface $resourceRegistry;
    private ModifiersHelperInterface $modifiersHelper;

    public function __construct(ResourceRegistryInterface $resourceRegistry, ModifiersHelperInterface $modifiersHelper)
    {
        $this->resourceRegistry = $resourceRegistry;
        $this->modifiersHelper = $modifiersHelper;
    }

    public function handle($terms, array $objectIds, array $taxonomies, array $queryVars)
    {
        $terms = !is_array($terms) ? [] : $terms;

        if (!isset($queryVars['taxonomy'])) {
            return $terms;
        }

        $cacheKey = $this->modifiersHelper->getTermQueryCacheKey($queryVars);
        $foundInCache = $this->modifiersHelper->getTermQueryResultFromCache($cacheKey, $queryVars['taxonomy'][0]);

        if ($foundInCache) {
            return $foundInCache;
        }

        $resources = $this->resourceRegistry->getByType(ResourceType::TAXONOMY);
        $matchingResources = array_filter($resources, fn ($resource) => $resource->getName() === $queryVars['taxonomy'][0]);

        if (empty($matchingResources)) {
            return $terms;
        }

        if (isset($queryVars['object_ids']) && !empty($queryVars['object_ids'])) {
            $queryVars['object_ids'] = array_map(function ($objectId) {

                $objectResource = $this->modifiersHelper->getResourceFromPostId($objectId);

                if (empty($objectResource)) {
                    return null;
                }

                return (int)str_replace($objectResource->getResourceID(), '', (string)absint($objectId));
            }, $queryVars['object_ids']);

            array_filter($queryVars['object_ids']);
        }

        $collection = reset($matchingResources)->getCollection($queryVars);

        if ($collection !== null) {
            $terms = $collection;
        }

        $this->modifiersHelper->addTermQueryResultToCache($cacheKey, $terms, $queryVars['taxonomy'][0]);

        return $terms;
    }
}
