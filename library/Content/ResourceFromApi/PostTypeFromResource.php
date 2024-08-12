<?php

namespace Municipio\Content\ResourceFromApi;

use Municipio\Content\ResourceFromApi\ResourceInterface;
use Municipio\Content\ResourceFromApi\TypeRegistrarInterface;
use WP_Post_Type;

/**
 * Class PostTypeRegistrar
 *
 * This class implements the TypeRegistrarInterface and is responsible for registering custom post types for resources.
 *
 * @package Content\ResourceFromApi\PostType
 */
class PostTypeFromResource implements TypeRegistrarInterface
{
    private ResourceInterface $resource;

    /**
     * Constructor for the PostTypeRegistrar class.
     *
     * @param string $name The name of the post type.
     * @param array $arguments An array of arguments for registering the post type.
     */
    public function __construct(ResourceInterface $resource)
    {
        $this->resource = $resource;
    }

    /**
     * Registers the post type.
     *
     * @return void
     */
    public function register(): bool
    {
        $arguments = $this->prepareArguments($this->resource->getArguments());
        $success   = register_post_type($this->resource->getName(), $arguments);
        $this->addRewriteRules();

        if (is_a($success, WP_Post_Type::class)) {
            return true;
        }

        return false;
    }

    /**
     * Add rewrite rules for the post type.
     */
    private function addRewriteRules(): void
    {
        $postTypeName = $this->resource->getName();
        $arguments    = $this->resource->getArguments();

        if ($arguments['hierarchical'] === true || empty($arguments['parent_post_types'])) {
            return;
        }

        foreach ($arguments['parent_post_types'] as $parentPostTypeName) {
            $parentPostTypeObject = get_post_type_object($parentPostTypeName);

            if (!is_a($parentPostTypeObject, WP_Post_Type::class)) {
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

    /**
     * Prepare the arguments for registering a custom post type.
     *
     * @return void
     */
    private function prepareArguments(): array
    {
        $arguments = $this->resource->getArguments();
        $arguments = $this->prepareLabels($arguments);
        $arguments = $this->prepareQueryVar($arguments);
        $arguments = $this->prepareRewrite($arguments);
        $arguments = $this->prepareParentPostTypes($arguments);

        return $arguments;
    }

    /**
     * Prepare labels for the post type.
     *
     * @param array $arguments The arguments for the post type.
     * @return array The prepared labels.
     */
    private function prepareLabels(array $arguments): array
    {
        if (isset($arguments['labels_singular_name']) && !empty($arguments['labels_singular_name'])) {
            $arguments['labels'] = ['singular_name' => $arguments['labels_singular_name']];
        }

        return $arguments;
    }

    /**
     * Prepare the query variable.
     *
     * @param array $arguments The arguments to prepare.
     * @return array The prepared query variable.
     */
    private function prepareQueryVar(array $arguments): array
    {
        if (empty($arguments['query_var'])) {
            unset($arguments['query_var']);
        }

        return $arguments;
    }

    /**
     * Prepare the rewrite rules for a post type.
     *
     * @param array $arguments The arguments for the post type.
     * @return array The modified arguments with the rewrite rules prepared.
     */
    private function prepareRewrite(array $arguments): array
    {
        if ($arguments['rewrite'] === true) {
            $arguments['rewrite'] = $arguments['rewrite_options'];

            if (empty($arguments['rewrite_options']['slug'])) {
                unset($arguments['rewrite']['slug']);
            }
        }

        return $arguments;
    }

    /**
     * Prepare parent post types.
     *
     * If the post type is not hierarchical and has parent post types, add parent post slug to the rewrite slug.
     *
     * @param array $arguments The post type arguments.
     * @return array The modified post type arguments.
     */
    private function prepareParentPostTypes(array $arguments): array
    {
        $parentPostTypes = is_array($arguments['parent_post_types']) ? $arguments['parent_post_types'] : [];
        $parentPostTypes = array_filter($parentPostTypes);

        if (!$arguments['hierarchical'] && !empty($parentPostTypes)) {
            $parentSlug = '/%parentPost%';
            $slug       = '';

            if (isset($arguments['rewrite']) && isset($arguments['rewrite']['slug'])) {
                $slug = $arguments['rewrite']['slug'];
                $slug = ltrim($slug, '/');
            }

            $arguments['rewrite']['slug'] = "{$parentSlug}{$slug}";
        }

        return $arguments;
    }
}
