<?php

namespace Municipio\Content\ResourceFromApi;

use Municipio\Content\ResourceFromApi\PostType\PostTypeRegistrar;
use Municipio\Content\ResourceFromApi\Taxonomy\TaxonomyRegistrar;
use stdClass;

class ResourceRegistry implements ResourceRegistryInterface
{
    /**
     * @var ResourceInterface[] $registry
     */
    public static ?array $registry = null;
    private string $resourcePostTypeName = 'api-resource';
    private static $resourceIdRangeStart = 100;

    public function __construct()
    {
        if ($this->isInitialized()) {
            return;
        }

        self::$registry = [];
    }

    public function addHooks(): void
    {
        add_action('init', function () {
            $this->populateRegistry();
            $this->register();
            $this->registerPostTypesRewriteRules();
        });
    }

    public function getRegistry(): array
    {
        return self::$registry;
    }

    private function isInitialized(): bool
    {
        return is_array(self::$registry);
    }

    public function getByName(string $name): array
    {
        return array_filter(self::$registry, function ($resource) use ($name) {
            return $resource->getName() === $name;
        });
    }

    private function register()
    {
        foreach (self::$registry as $resource) {

            if ($resource->getType() === ResourceType::POST_TYPE) {
                $registrar = new PostTypeRegistrar($resource->getName(), $resource->getArguments());
                $registrar->register();

                if ($registrar->isRegistered()) {
                    $resource->resourceID = $this->getNewResourceID();
                }
            }

            if ($resource->getType() === ResourceType::TAXONOMY) {
                $registrar = new TaxonomyRegistrar($resource->getName(), $resource->getArguments());
                $registrar->register();

                if ($registrar->isRegistered()) {
                    $resource->resourceID = $this->getNewResourceID();
                }
            }
        }
    }

    private function registerPostTypesRewriteRules()
    {
        $resources = $this->getByType(ResourceType::POST_TYPE);

        foreach ($resources as $resource) {
            $this->addRewriteRules($resource->getName(), $resource->getArguments());
        }
    }

    private function addRewriteRules(string $postTypeName, array $arguments): void
    {

        if ($arguments['hierarchical'] === true || empty($arguments['parent_post_types'])) {
            return;
        }

        foreach ($arguments['parent_post_types'] as $parentPostTypeName) {

            if ($arguments['hierarchical'] === true || empty($arguments['parent_post_types'])) {
                return;
            }

            foreach ($arguments['parent_post_types'] as $parentPostTypeName) {

                $parentPostTypeObject = get_post_type_object($parentPostTypeName);

                if (empty($parentPostTypeObject)) {
                    continue;
                }

                $parentRewriteSlug = $parentPostTypeObject->rewrite['slug'];

                add_rewrite_rule(
                    $parentRewriteSlug . '/(.*)/(.*)',
                    'index.php?' . $postTypeName . '=$matches[2]',
                    'top'
                );
            }
        }
    }

    private function populateRegistry()
    {
        $types = [
            ResourceType::POST_TYPE,
            ResourceType::TAXONOMY
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

                self::$registry[] = new Resource($id, $name, ResourceType::POST_TYPE, $arguments, $baseUrl, $originalName, $baseName);
            }
        }
    }

    private function getNewResourceID(): int
    {
        $currentSize = sizeof(self::$registry);

        return self::$resourceIdRangeStart + $currentSize + 1;
    }

    public function getByType(string $type): array
    {
        return array_filter(self::$registry, function ($resource) use ($type) {
            return $resource->getType() === $type;
        });
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
        }

        return null;
    }

    private function getName(int $resourceId, string $type, array $arguments): ?string
    {
        if (!function_exists('get_field')) {
            return '';
        }

        if ($type === ResourceType::POST_TYPE && isset($arguments['post_type_key'])) {
            return $arguments['post_type_key'];
        }

        if ($type === ResourceType::TAXONOMY && isset($arguments['taxonomy_key'])) {
            return $arguments['taxonomy_key'];
        }

        return null;
    }
}
