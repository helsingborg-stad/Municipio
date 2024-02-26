<?php

namespace Municipio\Content\ResourceFromApi\ResourceRegistry;

use Municipio\Content\ResourceFromApi\AttachmentResource;
use Municipio\Content\ResourceFromApi\PostTypeResource;
use Municipio\Content\ResourceFromApi\ResourceRegistry\SortByParentPostType;
use Municipio\Content\ResourceFromApi\ResourceType;
use Municipio\Content\ResourceFromApi\TaxonomyResource;
use Municipio\Helper\WP;

/**
 * Class ResourceRegistry
 * Represents a registry of resources obtained from an API.
 */
class ResourceRegistry extends SortByParentPostType implements ResourceRegistryInterface
{
    /**
     * @var \Municipio\Content\ResourceFromApi\ResourceInterface[] $registry
     */
    private array $registry               = [];
    private string $resourcePostTypeName  = 'api-resource';
    private const RESOURCE_ID_RANGE_START = 100;

    /**
     * Returns the registry.
     *
     * @return \Municipio\Content\ResourceFromApi\ResourceInterface[] The registry.
     */
    public function getRegistry(): array
    {
        return $this->registry;
    }

    /**
     * Returns a resource by its ID.
     *
     * @param int $id The ID of the resource.
     * @return \Municipio\Content\ResourceFromApi\ResourceInterface|null The resource.
     */
    public function getByName(string $name): array
    {
        return array_filter($this->registry, function ($resource) use ($name) {
            return $resource->getName() === $name;
        });
    }

    /**
     * Returns a resource by its ID.
     *
     * @param int $id The ID of the resource.
     * @return \Municipio\Content\ResourceFromApi\ResourceInterface|null The resource.
     */
    public function registerResources()
    {
        $types = [
            ResourceType::ATTACHMENT,
            ResourceType::POST_TYPE,
            ResourceType::TAXONOMY,
        ];

        foreach ($types as $type) {
            $resourcePosts = get_posts([
                'post_type'      => $this->resourcePostTypeName,
                'meta_key'       => 'type',
                'meta_value'     => $type,
                'posts_per_page' => -1,
            ]);

            if (empty($resourcePosts)) {
                return [];
            }

            foreach ($resourcePosts as $resourcePost) {
                $arguments      = $this->getArguments($resourcePost->ID, $type);
                $name           = $this->getName($resourcePost->ID, $type, $arguments ?? []);
                $resourcePostId = $resourcePost->ID;
                $resourceUrl    = get_post_meta($resourcePostId, 'api_resource_url', true);
                $originalName   = get_post_meta($resourcePostId, 'api_resource_original_name', true);
                $baseName       = get_post_meta($resourcePostId, 'api_resource_base_name', true);
                $baseUrl        = $resourceUrl . $baseName;
                $id             = $this->getNewResourceID();
                $mediaResource  = null;

                if ($type === ResourceType::POST_TYPE) {
                    // Attempt to add attachment resource to post type.
                    $mediaResources         = $this->getByType(ResourceType::ATTACHMENT);
                    $matchingMediaResources = array_filter(
                        $mediaResources,
                        function ($mediaResource) use ($resourcePost) {
                            $mediaResourceArgs = $mediaResource->getArguments();
                            return !empty($mediaResourceArgs['post_types']) &&
                                in_array((string)$resourcePost->ID, $mediaResourceArgs['post_types']);
                        }
                    );

                    if (!empty($matchingMediaResources)) {
                        $mediaResource = array_shift($matchingMediaResources);
                    }

                    $this->registry[] = new PostTypeResource(
                        $id,
                        $name,
                        $arguments,
                        $baseUrl,
                        $originalName,
                        $baseName,
                        $mediaResource
                    );
                } elseif ($type === ResourceType::TAXONOMY) {
                    $this->registry[] = new TaxonomyResource(
                        $id,
                        $name,
                        $arguments,
                        $baseUrl,
                        $originalName,
                        $baseName,
                        $mediaResource
                    );
                } elseif ($type === ResourceType::ATTACHMENT) {
                    $this->registry[] = new AttachmentResource(
                        $id,
                        $name,
                        $arguments,
                        $baseUrl,
                        $originalName,
                        $baseName,
                        $mediaResource
                    );
                }
            }
        }
    }

    /**
     * Returns a resource by its ID.
     *
     * @param int $id The ID of the resource.
     * @return int The resource.
     */
    private function getNewResourceID(): int
    {
        $currentSize = sizeof($this->registry);

        return self::RESOURCE_ID_RANGE_START + $currentSize + 1;
    }

    /**
     * Returns a resource by its ID.
     *
     * @param int $id The ID of the resource.
     * @return \Municipio\Content\ResourceFromApi\ResourceInterface[]|null The resource.
     */
    public function getByType(string $type): array
    {
        return array_filter($this->registry, fn ($resource) => $resource->getType() === $type);
    }

    /**
     * Returns a resource by its ID.
     *
     * @param int $id The ID of the resource.
     * @return array|null The resource.
     */
    private function getArguments(int $resourceId, string $type): ?array
    {
        if (!function_exists('get_field')) {
            return [];
        }

        if ($type === ResourceType::POST_TYPE) {
            return get_field('post_type_arguments', $resourceId) ?? [];
        } elseif ($type === ResourceType::TAXONOMY) {
            return get_field('taxonomy_arguments', $resourceId) ?? [];
        } elseif ($type === ResourceType::ATTACHMENT) {
            return get_field('attachment_arguments', $resourceId) ?? [];
        }

        return null;
    }

    /**
     * Returns a resource by its ID.
     *
     * @param int $id The ID of the resource.
     * @return string|null The resource.
     */
    private function getName(int $resourceId, string $type, array $arguments): ?string
    {
        if (!function_exists('get_field')) {
            return null;
        }

        if ($type === ResourceType::POST_TYPE && isset($arguments['post_type_key'])) {
            return $arguments['post_type_key'];
        }

        if ($type === ResourceType::TAXONOMY && isset($arguments['taxonomy_key'])) {
            return $arguments['taxonomy_key'];
        }

        if ($type === ResourceType::ATTACHMENT) {
            return WP::getPost($resourceId)->post_name ?? null;
        }

        return null;
    }
}
