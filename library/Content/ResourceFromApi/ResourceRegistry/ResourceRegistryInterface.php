<?php

namespace Municipio\Content\ResourceFromApi\ResourceRegistry;

use Municipio\Content\ResourceFromApi\ResourceRegistry\SortByParentPostTypeInterface;

interface ResourceRegistryInterface extends SortByParentPostTypeInterface
{
    /**
     * Returns the registry of resources.
     *
     * @return array The registry of resources.
     */
    public function getRegistry(): array;

    /**
     * @param string $type
     * @return \Municipio\Content\ResourceFromApi\ResourceInterface[]
     */
    public function getByType(string $type): array;

    /**
     * @param string $name
     * @return \Municipio\Content\ResourceFromApi\ResourceInterface[]
     */
    public function getByName(string $name): array;
}
