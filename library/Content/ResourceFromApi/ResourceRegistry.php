<?php

namespace Municipio\Content\ResourceFromApi;

use stdClass;

class ResourceRegistry implements ResourceRegistryInterface
{
    public static ?object $registry = null;
    private PostTypeRegistrar $postTypeRegistrar;
    private TaxonomyRegistrar $taxonomyRegistrar;
    private string $resourcePostTypeName = 'api-resource';

    public function __construct(PostTypeRegistrar $postTypeRegistrar, TaxonomyRegistrar $taxonomyRegistrar)
    {
        $this->postTypeRegistrar = $postTypeRegistrar;
        $this->taxonomyRegistrar = $taxonomyRegistrar;
    }

    public function initialize()
    {
        if (is_object(self::$registry)) {
            // Already initialized.
            return;
        }

        self::$registry = new stdClass();
        self::$registry->postTypes = [];
        self::$registry->taxonomies = [];
        self::$registry->attachments = [];

        $this->registerPostTypes();
        $this->registerTaxonomies();
    }

    public function getRegisteredPostTypes(): array
    {
        return self::$registry->postTypes;
    }

    public function getRegisteredPostType(string $postTypeName): ?object
    {
        if (empty(self::$registry->postTypes)) {
            return null;
        }

        foreach (self::$registry->postTypes as $postType) {
            if ($postType->name === $postTypeName) {
                return $postType;
            }
        }

        return null;
    }

    public function getRegisteredTaxonomy(string $taxonomyName): ?object
    {
        if (empty(self::$registry->taxonomies)) {
            return null;
        }

        foreach (self::$registry->taxonomies as $taxonomy) {
            if ($taxonomy->name === $taxonomyName) {
                return $taxonomy;
            }
        }

        return null;
    }

    public function getRegisteredTaxonomies(): array
    {
        return self::$registry->taxonomies;
    }

    public function getRegisteredAttachments(): array
    {
        return self::$registry->attachments;
    }

    private function registerPostTypes()
    {
        $postTypes = $this->getPostTypes();

        if (empty($postTypes)) {
            return;
        }

        foreach ($postTypes as $postType) {

            if (
                empty($postType->name) ||
                empty($postType->arguments) ||
                empty($postType->resourceID)
            ) {
                continue;
            }

            $registered = $this->postTypeRegistrar->register($postType->name, $postType->arguments);

            if ($registered) {
                self::$registry->postTypes[] = $postType;
            }
        }
    }

    private function registerTaxonomies() {
        $taxonomies = $this->getTaxonomies();

        if (empty($taxonomies)) {
            return;
        }

        foreach ($taxonomies as $taxonomy) {

            if (
                empty($taxonomy->name) ||
                empty($taxonomy->arguments) ||
                empty($taxonomy->resourceID)
            ) {
                continue;
            }

            $registered = $this->taxonomyRegistrar->register($taxonomy->name, $taxonomy->arguments);

            if ($registered) {
                self::$registry->taxonomies[] = $taxonomy;
            }
        }
    }

    private function getPostTypes(): array
    {
        $resources = $this->getResourcesByType('postType');

        if (empty($resources)) {
            return [];
        }

        return array_map(function ($resource) {
            $arguments = $this->getPostTypeArguments($resource->ID);
            $name = $this->getPostTypeName($resource->ID);
            $resourceID = $resource->ID;

            return (object)['name' => $name, 'resourceID' => $resourceID, 'arguments' => $arguments];
        }, $resources);
    }

    private function getTaxonomies(): array {
        $resources = $this->getResourcesByType('taxonomy');

        if (empty($resources)) {
            return [];
        }

        return array_map(function ($resource) {
            $arguments = $this->getTaxonomyArguments($resource->ID);
            $name = $this->getTaxonomyName($resource->ID);
            $resourceID = $resource->ID;

            return (object)['name' => $name, 'resourceID' => $resourceID, 'arguments' => $arguments];
        }, $resources);
    }

    private function getResourcesByType(string $type): array
    {
        if (!in_array($type, ['postType', 'taxonomy', 'attachment'])) {
            return [];
        }

        return get_posts([
            'post_type' => $this->resourcePostTypeName,
            'meta_key' => 'type',
            'meta_value' => $type
        ]);
    }

    private function getPostTypeArguments(int $resourceId): array
    {
        if (!function_exists('get_field')) {
            return [];
        }

        return get_field('post_type_arguments', $resourceId) ?? [];
    }

    public function getTaxonomyArguments(int $resourceId): array {
        if (!function_exists('get_field')) {
            return [];
        }

        return get_field('taxonomy_arguments', $resourceId) ?? [];
    }

    private function getPostTypeName(int $resourceId) {
        if (!function_exists('get_field')) {
            return '';
        }

        $arguments = $this->getPostTypeArguments($resourceId);

        if( isset($arguments['post_type_key']) ) {
            return $arguments['post_type_key'];
        }

        return '';
    }

    private function getTaxonomyName(int $resourceId) {
        if (!function_exists('get_field')) {
            return '';
        }

        $arguments = $this->getTaxonomyArguments($resourceId);

        if( isset($arguments['taxonomy_key']) ) {
            return $arguments['taxonomy_key'];
        }

        return '';
    }
}
