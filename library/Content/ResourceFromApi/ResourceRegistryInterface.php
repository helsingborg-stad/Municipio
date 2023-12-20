<?php

namespace Municipio\Content\ResourceFromApi;

interface ResourceRegistryInterface
{
    /**
     * Returns the registry of resources.
     *
     * @return array The registry of resources.
     */
    public function getRegistry(): array;

    /**
     * @param string $type
     * @return ResourceInterface[]
     */
    public function getByType(string $type): array;

    /**
     * @param string $name
     * @return ResourceInterface[]
     */
    public function getByName(string $name): array;
}
