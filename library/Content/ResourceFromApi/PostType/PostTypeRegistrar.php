<?php

namespace Municipio\Content\ResourceFromApi\PostType;

use Municipio\Content\ResourceFromApi\TypeRegistrarInterface;
use WP_Post_Type;

/**
 * Class PostTypeRegistrar
 * 
 * This class implements the TypeRegistrarInterface and is responsible for registering custom post types for resources.
 *
 * @package Content\ResourceFromApi\PostType
 */
class PostTypeRegistrar implements TypeRegistrarInterface
{
    private string $name;
    private array $arguments;
    private bool $registered = false;

    /**
     * Constructor for the PostTypeRegistrar class.
     *
     * @param string $name The name of the post type.
     * @param array $arguments An array of arguments for registering the post type.
     */
    public function __construct(string $name, array $arguments)
    {
        $this->name = $name;
        $this->arguments = $arguments;
    }

    /**
     * Registers the post type.
     *
     * @return void
     */
    public function register(): void
    {
        $this->prepareArguments();
        $this->registerPostType();
    }

    /**
     * Get the name of the post type.
     *
     * @return string The name of the post type.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the arguments for registering the post type.
     *
     * @return array The arguments for registering the post type.
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * Check if the post type is registered.
     *
     * @return bool True if the post type is registered, false otherwise.
     */
    public function isRegistered(): bool
    {
        return $this->registered;
    }

    /**
     * Registers a custom post type.
     *
     * @access private
     * @return void
     */
    private function registerPostType()
    {
        $success = register_post_type($this->name, $this->arguments);

        if (is_a($success, WP_Post_Type::class)) {
            $this->registered = true;
        }
    }

    /**
     * Prepare the arguments for registering a custom post type.
     *
     * @return void
     */
    private function prepareArguments(): void
    {
        $preparedArguments = $this->arguments;
        $preparedArguments = $this->prepareLabels($preparedArguments);
        $preparedArguments = $this->prepareQueryVar($preparedArguments);
        $preparedArguments = $this->prepareRewrite($preparedArguments);
        $preparedArguments = $this->prepareParentPostTypes($preparedArguments);

        $this->arguments = $preparedArguments;
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
        if (!$arguments['hierarchical'] && !empty($arguments['parent_post_types'])) {
            $parentSlug = '/%parentPost%';
            $slug = '';

            if (isset($arguments['rewrite']) && isset($arguments['rewrite']['slug'])) {
                $slug = $arguments['rewrite']['slug'];
                $slug = ltrim($slug, '/');
            }

            $arguments['rewrite']['slug'] = "{$parentSlug}{$slug}";
        }

        return $arguments;
    }
}
