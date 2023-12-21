<?php

namespace Municipio\Content\ResourceFromApi\Modifiers;

use Municipio\Content\ResourceFromApi\ResourceRegistryInterface;
use Municipio\Content\ResourceFromApi\ResourceType;
use Municipio\Helper\ResourceFromApiHelper;

/**
 * Class ModifyGetObjectTerms
 */
class ModifyGetObjectTerms
{
    private ResourceRegistryInterface $resourceRegistry;
    private ModifiersHelperInterface $modifiersHelper;

    /**
     * Class constructor.
     *
     * @param ResourceRegistryInterface $resourceRegistry The resource registry.
     * @param ModifiersHelperInterface $modifiersHelper The modifiers helper.
     */
    public function __construct(ResourceRegistryInterface $resourceRegistry, ModifiersHelperInterface $modifiersHelper)
    {
        $this->resourceRegistry = $resourceRegistry;
        $this->modifiersHelper  = $modifiersHelper;
    }

    /**
     * Handle the terms.
     *
     * @param mixed[] $terms The terms.
     * @param int[] $objectIds The object IDs.
     * @param string[] $taxonomies The taxonomies.
     * @param mixed[] $queryVars The query variables.
     * @return mixed[] The modified terms.
     */
    public function handle($terms, array $objectIds, array $taxonomies, array $queryVars)
    {
        $terms = !is_array($terms) ? [] : $terms;

        if (!isset($queryVars['taxonomy'])) {
            return $terms;
        }

        $cacheKey     = $this->modifiersHelper->getTermQueryCacheKey($queryVars);
        $foundInCache = $this->modifiersHelper->getTermQueryResultFromCache($cacheKey, $queryVars['taxonomy'][0]);

        if ($foundInCache) {
            return $foundInCache;
        }

        $resources         = $this->resourceRegistry->getByType(ResourceType::TAXONOMY);
        $matchingResources = array_filter($resources, function ($resource) use ($queryVars) {
            return $resource->getName() === $queryVars['taxonomy'][0];
        });

        if (empty($matchingResources)) {
            return $terms;
        }

        if (isset($queryVars['object_ids']) && !empty($queryVars['object_ids'])) {
            $queryVars['object_ids'] = array_map(function ($objectId) {

                $objectResource = $this->modifiersHelper->getResourceFromPostId($objectId);

                if (empty($objectResource)) {
                    return null;
                }

                return ResourceFromApiHelper::getRemoteId($objectId, $objectResource);
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
