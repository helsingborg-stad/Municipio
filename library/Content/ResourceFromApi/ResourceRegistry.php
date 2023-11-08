<?php

namespace Municipio\Content\ResourceFromApi;

use Municipio\Content\ResourceFromApi\PostType\PostTypeRegistrar;
use Municipio\Content\ResourceFromApi\Taxonomy\TaxonomyRegistrar;
use stdClass;

class ResourceRegistry implements ResourceRegistryInterface
{
    public static ?object $registry = null;
    private string $resourcePostTypeName = 'api-resource';

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
        $this->registerPostTypesRewriteRules();
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

            $registrar = new PostTypeRegistrar($postType->name, $postType->arguments);
            $registrar->register($postType->name, $postType->arguments);

            if ($registrar->isRegistered()) {
                self::$registry->postTypes[] = $postType;
            }
        }
    }

    private function registerPostTypesRewriteRules() {
        $postTypes = $this->getPostTypes();
        foreach($postTypes as $postType) {
            $this->addRewriteRules($postType->name, $postType->arguments);
        }
    }

    private function addRewriteRules(string $postTypeName, array $arguments):void {

        if ($arguments['hierarchical'] === true || empty($arguments['parent_post_types'])) {
            return;
        }

        foreach($arguments['parent_post_types'] as $parentPostTypeName) {
            
            if ($arguments['hierarchical'] === true || empty($arguments['parent_post_types'])) {
                return;
            }
    
            foreach($arguments['parent_post_types'] as $parentPostTypeName) {
                
                $parentPostTypeObject = get_post_type_object($parentPostTypeName);
    
                if( empty($parentPostTypeObject) ) {
                    continue;
                }
    
                $parentRewriteSlug = $parentPostTypeObject->rewrite['slug'];
    
                add_rewrite_rule(
                    $parentRewriteSlug . '/(.*)/(.*)',
                    'index.php?'.$postTypeName . '=$matches[2]',
                    'top'
                );
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

            $registrar = new TaxonomyRegistrar($taxonomy->name, $taxonomy->arguments);
            $registrar->register($taxonomy->name, $taxonomy->arguments);

            if ($registrar->isRegistered()) {
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
            $apiInformation = self::getApiInformation($resourceID, 'postType');
            $collectionUrl = $apiInformation ? $apiInformation['url'] . $apiInformation['baseName'] : '';
            $originalName = $apiInformation ? $apiInformation['originalName'] : '';
            $baseName = $apiInformation ? $apiInformation['baseName'] : '';

            return (object)['name' => $name, 'resourceID' => $resourceID, 'arguments' => $arguments, 'collectionUrl' => $collectionUrl, 'originalName' => $originalName, 'baseName' => $baseName];
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
            $apiInformation = self::getApiInformation($resourceID, 'taxonomy');
            $collectionUrl = $apiInformation ? $apiInformation['url'] . $apiInformation['baseName'] : '';
            $originalName = $apiInformation ? $apiInformation['originalName'] : '';
            $baseName = $apiInformation ? $apiInformation['baseName'] : '';

            return (object)['name' => $name, 'resourceID' => $resourceID, 'arguments' => $arguments, 'collectionUrl' => $collectionUrl, 'originalName' => $originalName, 'baseName' => $baseName];
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
            'meta_value' => $type,
            'posts_per_page' => -1,
        ]);
    }

    private function getPostTypeArguments(int $resourceId): array
    {
        if (!function_exists('get_field')) {
            return [];
        }

        return get_field('post_type_arguments', $resourceId) ?? [];
    }

    private function getApiInformation(int $resourceId, string $type = 'postType'): ?array
    {
        if (!function_exists('get_field')) {
            return null;
        }

        $fieldName = $type === 'postType' ? 'post_type_source' : 'taxonomy_source';
        $source = get_field($fieldName, $resourceId);

        if (!is_string($source) || empty($source)) {
            return null;
        }

        $parts = explode(',', $source);

        if (sizeof($parts) !== 3) {
            return null;
        }

        $url = $parts[0];
        $originalName = $parts[1];
        $baseName = $parts[2];


        return [
            'url' => $url,
            'originalName' => $originalName,
            'baseName' => $baseName,
        ];
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
