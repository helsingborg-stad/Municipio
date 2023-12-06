<?php

namespace Municipio\Content\ResourceFromApi\Modifiers;

use Municipio\Content\ResourceFromApi\ResourceRegistryInterface;
use Municipio\Content\ResourceFromApi\ResourceType;
use Municipio\Helper\WP;
use WP_Post;

class ModifyPostTypeLink
{
    private ResourceRegistryInterface $resourceRegistry;

    public function __construct(ResourceRegistryInterface $resourceRegistry)
    {
        $this->resourceRegistry = $resourceRegistry;
    }

    public function handle(string $postLink, WP_Post $post)
    {
        if ($post->post_parent === 0) {
            return $postLink;
        }

        $resources = $this->resourceRegistry->getByType(ResourceType::POST_TYPE);
        $matchingResources = array_filter($resources, fn ($r) => $r->getName() === $post->post_type);
        $postTypeObject = get_post_type_object($post->post_type);

        if (empty($matchingResources) || empty($postTypeObject)) {
            return $postLink;
        }

        if (!str_starts_with($postTypeObject->rewrite['slug'], '/%parentPost%')) {
            return $postLink;
        }

        $parent = WP::getPost($post->post_parent);

        if (empty($parent)) {
            return $postLink;
        }

        $path     = trim(parse_url(WP::getPermalink($parent))['path'], '/');
        $postLink = str_replace('%parentPost%', $path, $postLink);

        return $postLink;
    }
}