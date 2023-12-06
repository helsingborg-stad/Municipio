<?php

namespace Municipio\Content\ResourceFromApi;

use Municipio\Helper\WP;

class ResourceRegistry implements ResourceRegistryInterface
{
    /**
     * @var ResourceInterface[] $registry
     */
    private array $registry = [];
    private string $resourcePostTypeName = 'api-resource';
    private const RESOURCE_ID_RANGE_START = 100;

    public function getRegistry(): array
    {
        return $this->registry;
    }

    public function getByName(string $name): array
    {
        return array_filter($this->registry, function ($resource) use ($name) {
            return $resource->getName() === $name;
        });
    }

    public function registerResources()
    {
        $types = [
            ResourceType::ATTACHMENT,
            ResourceType::POST_TYPE,
            ResourceType::TAXONOMY,
        ];

        foreach ($types as $type) {

            $resourcePosts = get_posts([
                'post_type' => $this->resourcePostTypeName,
                'meta_key' => 'type',
                'meta_value' => $type,
                'posts_per_page' => -1,
            ]);

            if (empty($resourcePosts)) {
                return [];
            }

            foreach ($resourcePosts as $resourcePost) {
                $arguments = $this->getArguments($resourcePost->ID, $type);
                $name = $this->getName($resourcePost->ID, $type, $arguments);
                $resourcePostId = $resourcePost->ID;
                $resourceUrl = get_post_meta($resourcePostId, 'api_resource_url', true);
                $originalName = get_post_meta($resourcePostId, 'api_resource_original_name', true);
                $baseName = get_post_meta($resourcePostId, 'api_resource_base_name', true);
                $baseUrl = $resourceUrl . $baseName;
                $id = $this->getNewResourceID();
                $mediaResource = null;

                if ($type === ResourceType::POST_TYPE) {
                    // Attempt to add attachment resource to post type.
                    $mediaResources = $this->getByType(ResourceType::ATTACHMENT);
                    $matchingMediaResources = array_filter($mediaResources, function ($mediaResource) use ($resourcePost) {
                        $mediaResourceArgs = $mediaResource->getArguments();
                        return !empty($mediaResourceArgs['post_types']) &&
                            in_array((string)$resourcePost->ID, $mediaResourceArgs['post_types']);
                    });

                    if (!empty($matchingMediaResources)) {
                        $mediaResource = array_shift($matchingMediaResources);
                    }

                    $this->registry[] = new PostTypeResource($id, $name, $arguments, $baseUrl, $originalName, $baseName, $mediaResource);
                } elseif ($type === ResourceType::TAXONOMY) {
                    $this->registry[] = new TaxonomyResource($id, $name, $arguments, $baseUrl, $originalName, $baseName, $mediaResource);
                } elseif ($type === ResourceType::ATTACHMENT) {
                    $this->registry[] = new AttachmentResource($id, $name, $arguments, $baseUrl, $originalName, $baseName, $mediaResource);
                }
            }
        }
    }

    private function getNewResourceID(): int
    {
        $currentSize = sizeof($this->registry);

        return self::RESOURCE_ID_RANGE_START + $currentSize + 1;
    }

    public function getByType(string $type): array
    {
        return array_filter( $this->registry, fn ($resource) => $resource->getType() === $type );
    }

    private function getArguments(int $resourceId, string $type): ?array
    {
        if (!function_exists('get_field')) {
            return [];
        }

        if ($type === ResourceType::POST_TYPE) {
            return get_field('post_type_arguments', $resourceId) ?? [];
        } else if ($type === ResourceType::TAXONOMY) {
            return get_field('taxonomy_arguments', $resourceId) ?? [];
        } else if ($type === ResourceType::ATTACHMENT) {
            return get_field('attachment_arguments', $resourceId) ?? [];
        }

        return null;
    }

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
