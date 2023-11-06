<?php

namespace Municipio\Content\ResourceFromApi\Taxonomy;

use Municipio\Content\ResourceFromApi\TypeRegistrarInterface;
use WP_Taxonomy;

/**
 * Class TaxonomyRegistrar
 * 
 * This class implements the TypeRegistrarInterface and is responsible for registering taxonomies.
 *
 * @package Municipio\Content\ResourceFromApi\Taxonomy
 */
class TaxonomyRegistrar implements TypeRegistrarInterface
{
    private string $name;
    private array $arguments;
    private bool $registered = false;
    private array $objectType = [];

    /**
     * Constructor for TaxonomyRegistrar class.
     *
     * @param string $name The name of the taxonomy.
     * @param array $arguments Optional. Array of arguments for registering the taxonomy.
     */
    public function __construct(string $name, array $arguments)
    {
        $this->name = $name;
        $this->arguments = $arguments;

        if (isset($arguments['object_type']) && is_array($arguments['object_type'])) {
            $this->objectType = $arguments['object_type'];
        }
    }

    /**
     * Registers the taxonomy.
     *
     * @return void
     */
    public function register(): void
    {
        $this->prepareArguments();
        $this->registerTaxonomy();
    }

    /**
     * Get the name of the taxonomy.
     *
     * @return string The name of the taxonomy.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the arguments for registering a taxonomy.
     *
     * @return array The arguments for registering a taxonomy.
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * Check if the taxonomy is registered.
     *
     * @return bool True if the taxonomy is registered, false otherwise.
     */
    public function isRegistered(): bool
    {
        return $this->registered;
    }

    /**
     * Registers a taxonomy.
     *
     * @return void
     */
    private function registerTaxonomy(): void
    {
        $success = register_taxonomy($this->getName(), $this->objectType, $this->getArguments());

        if (is_a($success, WP_Taxonomy::class)) {
            $this->registered = true;
        }
    }

    /**
     * Prepares the arguments for the taxonomy registrar.
     *
     * @return void
     */
    private function prepareArguments(): void
    {
        $preparedArguments = $this->arguments;
        $preparedArguments = $this->prepareLabels($preparedArguments);

        $this->arguments = $preparedArguments;
    }

    /**
     * Prepare labels for a taxonomy based on the provided arguments.
     *
     * @param array $arguments The arguments to use for preparing the labels.
     * @return array The prepared labels.
     */
    private function prepareLabels(array $arguments): array
    {
        if (isset($arguments['labels_singular_name']) && !empty($arguments['labels_singular_name'])) {
            $arguments['labels'] = ['singular_name' => $arguments['labels_singular_name']];
        }

        return $arguments;
    }
}
