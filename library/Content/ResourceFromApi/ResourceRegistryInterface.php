<?php

namespace Municipio\Content\ResourceFromApi;

interface ResourceRegistryInterface
{
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
