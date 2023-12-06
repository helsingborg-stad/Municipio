<?php

namespace Municipio\Content\ResourceFromApi;

use Municipio\Content\ResourceFromApi\ResourceInterface;
use Municipio\Content\ResourceFromApi\TypeRegistrarInterface;
use WP_Taxonomy;

/**
 * Class TaxonomyRegistrar
 * 
 * This class implements the TypeRegistrarInterface and is responsible for registering taxonomies.
 *
 * @package Municipio\Content\ResourceFromApi\Taxonomy
 */
class TaxonomyFromResource implements TypeRegistrarInterface
{
    private ResourceInterface $resource;

    /**
     * Constructor for TaxonomyRegistrar class.
     *
     * @param string $name The name of the taxonomy.
     * @param array $arguments Optional. Array of arguments for registering the taxonomy.
     */
    public function __construct(ResourceInterface $resource)
    {
        $this->resource = $resource;
    }

    private function getObjectType (): array
    {
        $arguments = $this->resource->getArguments();

        if (isset($arguments['object_type']) && is_array($arguments['object_type'])) {
            return $arguments['object_type'];
        }

        return [];
    }

    /**
     * Registers the taxonomy.
     *
     * @return void
     */
    public function register(): bool
    {
        $arguments = $this->prepareArguments();
        $success = register_taxonomy($this->resource->getName(), $this->getObjectType(), $arguments);
        return is_a($success, WP_Taxonomy::class);
    }
    
    private function prepareArguments(): array
    {
        $preparedArguments = $this->resource->getArguments();
        $preparedArguments = $this->prepareLabels($preparedArguments);

        return $preparedArguments;
    }

    /**
     * Prepare labels for a taxonomy based on the provided arguments.
     *
     * @param array $arguments The arguments to use for preparing the labels.
     * @return array The prepared labels.
     */
    private function prepareLabels(array $arguments): array
    {
        $arguments['labels'] = [];

        if( isset($arguments['labels_name']) && !empty($arguments['labels_name']) ) {
            $arguments['labels']['name'] = $arguments['labels_name'];
        }

        if (isset($arguments['labels_singular_name']) && !empty($arguments['labels_singular_name'])) {
            $arguments['labels']['singular_name'] = $arguments['labels_singular_name'];
        }

        return $arguments;
    }
}
