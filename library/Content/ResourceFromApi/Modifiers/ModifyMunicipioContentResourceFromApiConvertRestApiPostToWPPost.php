<?php

namespace Municipio\Content\ResourceFromApi\Modifiers;

use Municipio\Content\ResourceFromApi\ResourceRegistryInterface;
use Municipio\Content\ResourceFromApi\ResourceType;
use Municipio\Helper\RestRequestHelper;

class ModifyMunicipioContentResourceFromApiConvertRestApiPostToWPPost
{
    private ResourceRegistryInterface $resourceRegistry;

    public function __construct(ResourceRegistryInterface $resourceRegistry)
    {
        $this->resourceRegistry = $resourceRegistry;
    }

    public function handle($wpPost, $restApiPost, $localPostType)
    {

        if ($wpPost->post_parent === 0) {
            return $wpPost;
        }

        if (!isset($restApiPost->_links) || !isset($restApiPost->_links->up)) {
            return $wpPost;
        }

        $parentUrl = $restApiPost->_links->up[0]->href;

        if (empty($parentUrl)) {
            return $wpPost;
        }

        // Get parent post from API and convert to WP_Post. Then use the id from that WP_Post as the parent id for $wpPost.
        $parentPostFromApi = RestRequestHelper::getFromApi($parentUrl);
        $parentId = $parentPostFromApi->id;
        $parentResource = null;

        if (!isset($parentPostFromApi->_links) || !isset($parentPostFromApi->_links->collection)) {
            return $wpPost;
        }

        $parentCollectionUrl = $parentPostFromApi->_links->collection[0]->href;

        $resources = $this->resourceRegistry->getByType(ResourceType::POST_TYPE);

        foreach ($resources as $resource) {
            if ($resource->getBaseUrl() === $parentCollectionUrl) {
                $parentResource = $resource;
                break;
            }
        }

        if (empty($parentResource)) {
            return $wpPost;
        }

        $parentPost = $parentResource->getSingle($parentId);

        if (empty($parentPost)) {
            return $wpPost;
        }

        $localParentId = $parentPost->ID;
        $wpPost->post_parent = $localParentId;

        return $wpPost;
    }
}
