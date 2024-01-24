<?php

namespace Municipio\Content\ResourceFromApi\Modifiers;

use Municipio\Content\ResourceFromApi\ResourceRegistry\ResourceRegistryInterface;
use Municipio\Content\ResourceFromApi\ResourceType;
use WP_Term_Query;

/**
 * Class ModifyTermsPreQuery
 */
class ModifyTermsPreQuery
{
    private ResourceRegistryInterface $resourceRegistry;
    private ModifiersHelperInterface $modifiersHelper;

    /**
     * ModifyTermsPreQuery constructor.
     *
     * @param ResourceRegistryInterface $resourceRegistry
     * @param ModifiersHelperInterface $modifiersHelper
     */
    public function __construct(ResourceRegistryInterface $resourceRegistry, ModifiersHelperInterface $modifiersHelper)
    {
        $this->resourceRegistry = $resourceRegistry;
        $this->modifiersHelper  = $modifiersHelper;
    }

    /**
     * Handle the modification of terms before the query.
     *
     * @param array|null $terms The terms to be modified.
     * @param WP_Term_Query $termQuery The term query object.
     * @return array|null The modified terms.
     */
    public function handle(?array $terms, WP_Term_Query $termQuery): ?array
    {
        $queryVars = $termQuery->query_vars;

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

                return (int)str_replace($objectResource->getResourceID(), '', (string)absint($objectId));
            }, $queryVars['object_ids']);

            array_filter($queryVars['object_ids']);
        }

        $collection = reset($matchingResources)->getCollection($queryVars);

        $this->modifiersHelper->addTermQueryResultToCache($cacheKey, $collection, $queryVars['taxonomy'][0]);

        return $collection;
    }
}
